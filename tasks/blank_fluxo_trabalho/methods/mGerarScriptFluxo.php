<?php
// MRO-122 - JavaScript do diagrama de fluxo (Rotina Padrao + NRC)
// Recebe os dados (nodes + links) ja como array PHP e gera o JS completo
function mGerarScriptFluxo($var_dados)
{
    $var_nodes_json = json_encode($var_dados['nodes'], JSON_UNESCAPED_UNICODE);
    $var_links_json = json_encode($var_dados['links'], JSON_UNESCAPED_UNICODE);

    return <<<JS
<script>
    // =========================================================================
    // DADOS DO DIAGRAMA (gerados pelo PHP via metodo mGerarDadosFluxo)
    // =========================================================================
    var NODES = {$var_nodes_json};
    var LINKS = {$var_links_json};

    // =========================================================================
    // RENDERIZACAO: Syncfusion EJ2 Diagram (interativo)
    // =========================================================================
    function renderSyncfusion() {
        try {
            if (!window.ej || !ej.diagrams || !ej.diagrams.Diagram) {
                return false;
            }

            // Inject dos modulos necessarios
            try {
                var mods = [];
                if (ej.diagrams.BpmnDiagrams) mods.push(ej.diagrams.BpmnDiagrams);
                if (ej.diagrams.UndoRedo) mods.push(ej.diagrams.UndoRedo);
                if (mods.length) { ej.diagrams.Diagram.Inject.apply(ej.diagrams.Diagram, mods); }
            } catch (e) {}

            var NODE_W = 170;
            var NODE_H = 52;
            var DEC_W = 150;
            var DEC_H = 84;

            var nodes = [];
            var connectors = [];

            // 1. Cria os nodes (estados) com cores e formatos do mermaid
            NODES.forEach(function (n) {
                var ehDecisao = (n.shape === 'diamond');
                var w = ehDecisao ? DEC_W : NODE_W;
                var h = ehDecisao ? DEC_H : NODE_H;
                var corTexto = n.color.toLowerCase() === '#fff8e1' ? '#000000' : '#000000';

                nodes.push({
                    id: n.id,
                    shape: { type: 'Basic', shape: ehDecisao ? 'Diamond' : 'Rectangle' },
                    width: w,
                    height: h,
                    offsetX: n.x,
                    offsetY: n.y,
                    style: { fill: n.color, strokeColor: n.stroke, strokeWidth: 2 },
                    annotations: [
                        { content: n.text, style: { fontSize: 11, bold: true, color: '#000', fill: 'transparent' } },
                        { content: n.sub, offset: { x: 0.5, y: 1.25 }, style: { fontSize: 9, color: '#4a5568', fill: 'transparent' } }
                    ],
                    addInfo: { sub: n.sub }
                });
            });

            // 2. Conectores (transicoes com labels dos botoes)
            // Tipo Bezier: curvas suaves desviam dos nos (rota ortogonal
            // cruzava os retangulos e sobrepunha os labels).
            LINKS.forEach(function (l, i) {
                var cor = l.color || '#718096';
                var c = {
                    id: 'conn_' + i,
                    sourceID: l.from,
                    targetID: l.to,
                    type: 'Bezier',
                    style: { strokeColor: cor, strokeWidth: 1.5, strokeDashArray: l.dash ? '5 5' : '0' },
                    targetDecorator: { shape: 'Arrow', style: { fill: cor, strokeColor: cor } }
                };
                if (l.label) {
                    c.annotations = [{ content: l.label, style: { fontSize: 9, fill: '#ffffff', color: '#1a202c' } }];
                }
                connectors.push(c);
            });

            var diagram = new ej.diagrams.Diagram({
                width: '100%',
                height: '100%',
                nodes: nodes,
                connectors: connectors,
                snapSettings: { constraints: ej.diagrams.SnapConstraints.None },
                scrollSettings: { canAutoScroll: false, scrollLimit: 'Infinity' },
                pageSettings: { scrollLimit: 'Infinity', background: { color: '#ffffff' } },
                tool: ej.diagrams.DiagramTools.ZoomPan,
                created: function () {
                    try { diagram.fitToPage({ mode: 'Page', region: 'Content' }); } catch (e) {}
                    try { mroAtualizarZoom(); } catch (e) {}
                },
                scrollChange: function () {
                    try { mroAtualizarZoom(); } catch (e) {}
                },
                selectionChange: function (args) {
                    try {
                        if (args.state === 'Changing' && args.newValue && args.newValue.length) {
                            var sel = args.newValue[0];
                            if (sel.addInfo && sel.addInfo.sub) {
                                diagram.tooltip = { content: sel.addInfo.sub, position: 'TopCenter', relativeMode: 'Object' };
                            }
                        }
                    } catch (e) {}
                }
            });
            diagram.appendTo('#diagram');

            // Expoe o diagrama para os botoes de zoom da toolbar
            window.mroDiagram = diagram;

            return true;
        } catch (err) {
            console.error('Syncfusion Diagram error:', err);
            return false;
        }
    }

    // =========================================================================
    // CONTROLE DE ZOOM (toolbar)
    // =========================================================================
    function mroZoomIn() {
        var d = window.mroDiagram;
        if (!d) return;
        var atual = d.scrollSettings.currentZoom || 1;
        d.zoomTo((atual + 0.1) * 100);
        mroAtualizarZoom();
    }

    function mroZoomOut() {
        var d = window.mroDiagram;
        if (!d) return;
        var atual = d.scrollSettings.currentZoom || 1;
        d.zoomTo(Math.max(0.2, atual - 0.1) * 100);
        mroAtualizarZoom();
    }

    function mroZoomFit() {
        var d = window.mroDiagram;
        if (!d) return;
        d.fitToPage({ mode: 'Page', region: 'Content' });
        mroAtualizarZoom();
    }

    function mroZoomReset() {
        var d = window.mroDiagram;
        if (!d) return;
        d.zoomTo(100);
        d.pan(0, 0);
        mroAtualizarZoom();
    }

    function mroAtualizarZoom() {
        var d = window.mroDiagram;
        var el = document.getElementById('mro_zoom_label');
        if (!d || !el) return;
        try {
            var z = Math.round((d.scrollSettings.currentZoom || 1) * 100);
            el.textContent = z + '%';
        } catch (e) {}
    }

    // =========================================================================
    // FALLBACK: CSS puro (caso ej.diagrams nao exista no bundle)
    // =========================================================================
    function renderFallback() {
        var container = document.getElementById('diagram');
        var html = '<div class="fb-container">';

        NODES.forEach(function (n) {
            var ehDecisao = (n.shape === 'diamond');
            html += '<div class="fb-node" style="background:' + n.color + ';border:2px solid ' + n.stroke + ';border-radius:' + (ehDecisao ? '50%' : '4px') + '">';
            html += '<strong>' + n.text + '</strong>' + (n.sub ? '<small>' + n.sub + '</small>' : '');
            html += '</div>';
        });

        html += '</div>';
        container.innerHTML = html;
    }

    // =========================================================================
    // BOOT
    // =========================================================================
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            var ok = renderSyncfusion();
            if (!ok) {
                renderFallback();
            }
        }, 100);
    });
</script>
JS;
}
