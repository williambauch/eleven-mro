<?php
// MRO-122 - CSS do diagrama de fluxo dos operadores
function mGerarCssFluxo()
{
    return <<<'CSS'
<style>
html, body {
    font-family: 'Roboto', 'Segoe UI', Tahoma, sans-serif;
    background-color: #f4f6f8;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    height: 100%;
    overflow: hidden;
}

/* Wrapper flex: o diagrama ocupa o espaco restante sem scroll da pagina */
#app-wrapper {
    display: flex;
    flex-direction: column;
    height: 100vh;
    overflow: hidden;
    box-sizing: border-box;
}

.page-header {
    font-size: 20px;
    color: #1a202c;
    padding: 12px 15px 8px 15px;
    font-weight: 600;
    border-bottom: 2px solid #e2e8f0;
    flex-shrink: 0;
}

.page-header small {
    font-size: 12px;
    color: #718096;
    font-weight: 400;
}

.legend {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    padding: 10px 15px;
    font-size: 12px;
    flex-shrink: 0;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.legend-swatch {
    width: 14px;
    height: 14px;
    border-radius: 3px;
    display: inline-block;
}

/* Diagrama ocupa o espaco restante (flex:1 + min-height:0) */
.diagram-container {
    background: #fff;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    flex: 1;
    min-height: 0;
    margin: 0 15px 15px 15px;
    overflow: hidden;
}

/* Toolbar de controle do diagrama */
.zoom-toolbar {
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0 15px 10px 15px;
    padding: 6px 10px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    width: fit-content;
    flex-shrink: 0;
}

.zoom-toolbar button {
    border: 1px solid #cbd5e0;
    background: #f7fafc;
    color: #2d3748;
    border-radius: 4px;
    padding: 5px 12px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    line-height: 1.2;
}

.zoom-toolbar button:hover {
    background: #e2e8f0;
    border-color: #a0aec0;
}

.zoom-toolbar button:active {
    background: #cbd5e0;
}

.zoom-label {
    font-size: 13px;
    font-weight: 700;
    color: #4a5568;
    min-width: 48px;
    text-align: center;
    font-variant-numeric: tabular-nums;
}

.zoom-toolbar .separator {
    width: 1px;
    height: 22px;
    background: #e2e8f0;
    margin: 0 2px;
}

.legend {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin: 12px 0;
    font-size: 12px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.legend-swatch {
    width: 14px;
    height: 14px;
    border-radius: 3px;
    display: inline-block;
}

/* Fallback CSS (caso ej.diagrams nao exista no bundle) */
.fb-container {
    display: flex;
    flex-direction: column;
    gap: 14px;
    padding: 15px;
    overflow: auto;
    height: 100%;
    box-sizing: border-box;
}

.fb-lane {
    border-radius: 6px;
    padding: 10px 12px;
    color: #fff;
}

.fb-lane-title {
    font-weight: 700;
    font-size: 13px;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.fb-lane-body {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.fb-node {
    background: rgba(255,255,255,0.92);
    color: #1a202c;
    border-radius: 4px;
    padding: 6px 10px;
    font-size: 12px;
    font-weight: 500;
    box-shadow: 0 1px 2px rgba(0,0,0,0.15);
    max-width: 240px;
}

.fb-node small {
    display: block;
    font-weight: 400;
    color: #4a5568;
    font-size: 11px;
}

.fb-links {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 8px 0 0 0;
    font-size: 11px;
}

.fb-link {
    background: #edf2f7;
    color: #2d3748;
    border-radius: 10px;
    padding: 3px 10px;
}
</style>
CSS;
}
