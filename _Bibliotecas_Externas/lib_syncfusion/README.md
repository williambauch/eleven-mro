# Syncfusion EJ2 — Biblioteca de Componentes

Biblioteca de componentes UI (grids, gráficos, diagramas, etc.) usada nas aplicações Blank do MRO System.

## Como usar

Inclua os 3 arquivos no `<head>` da aplicação (ou no `onExecute` de uma Blank), **exatamente nesta ordem**:

```html
<link href="/libs/syncfusion/styles/material3.css" rel="stylesheet">
<script src="/libs/syncfusion/scripts/ej2-syncfusion.js" type="text/javascript"></script>
<script src="/libs/syncfusion/mro_config.js" type="text/javascript"></script>
```

## Exemplo de uso (Blank ScriptCase)

```php
<?php
// blank_fluxo_trabalho - exemplo de uso
$var_dados = mGerarDados();
?>
<link href="/libs/syncfusion/styles/material3.css" rel="stylesheet">
<script src="/libs/syncfusion/scripts/ej2-syncfusion.js" type="text/javascript"></script>
<script src="/libs/syncfusion/mro_config.js" type="text/javascript"></script>

<!-- Seu HTML aqui -->

<script>
    // Exemplo: inicializa um componente Syncfusion (Diagram)
    // Sempre verifique se o módulo existe antes de usar (defensivo)
    if (window.ej && ej.diagrams && ej.diagrams.Diagram) {
        var diagram = new ej.diagrams.Diagram({
            width: '100%',
            height: '100%',
            nodes: []
        });
        diagram.appendTo('#meu-diagram');
    }
</script>
```

## Arquivos

| Arquivo | Descrição |
|---|---|
| `styles/material3.css` | Tema Material 3 (CSS) |
| `scripts/ej2-syncfusion.js` | Bundle completo dos componentes EJ2 |
| `scripts/mro_config.js` | Configurações/ajustes do projeto MRO |

## Componentes disponíveis (bundle completo)

- `ej.grids.Grid` — tabelas/grids
- `ej.charts.Chart` — gráficos (colunas, barras, linhas, pizza, etc.)
- `ej.charts.AccumulationChart` — gráficos de pizza/rosca
- `ej.diagrams.Diagram` — diagramas/fluxogramas (swimlanes, nós, conectores)
- `ej.gantt.Gantt` — cronogramas Gantt
- `ej.dropdowns` — dropdowns, combos
- `ej.lists` — listas
- `ej.inputs` — inputs, sliders, switches
- `ej.buttons` — botões, chips
- `ej.notifications` — toasts, badges, tooltips
- `ej.popups` — dialogs, tooltips, spinners
- `ej.calendars` — datepickers, calendars
- E outros módulos do EJ2

## Observações

- Os módulos exigem **injeção** antes do uso em alguns casos (ex: `ej.grids.Grid.Inject(...)`) — veja o exemplo em `tasks/blank_fluxo_trabalho/methods/mGerarScriptFluxo.php`.
- O `mro_config.js` contém ajustes específicos do MRO System — **não remova**.
- Versão de referência: `crg-resources/32.1.25` (verificada no bundle).
