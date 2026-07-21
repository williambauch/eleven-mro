# Job Card App — Editor Visual de Job Instruction Cards

## O que é

O **jobcard_app** é um editor visual drag-and-drop baseado em PHP/JavaScript puro que permite posicionar campos de texto sobre imagens de fundo de documentos aeronáuticos (Job Cards). Ele funciona como uma ferramenta de **design/layout** para definir exatamente onde cada campo de informação deve aparecer na folha impressa.

O editor oferece:

- **5 páginas** no formato **Letter** (816 × 1056 px, equivalente a 215,9 × 279,4 mm)
- Imagem de fundo específica para cada página (escaneada do documento modelo)
- Sidebar com todos os campos disponíveis para arrastar até o canvas
- Ajuste de posição (drag), tamanho da fonte, negrito, tipo (texto/data/barcode)
- Suporte a multilinha com redimensionamento de largura
- Grade de alinhamento sobreposta para facilitar o posicionamento
- Botão **▢ Bordas** para mostrar/ocultar as bordas de todos os boxes
- Salvamento/carregamento via AJAX (armazenamento em `assets/data/pageN.json`)
- **Exportação de código PHP** para o método TCPDF correspondente no ScriptCase

---

## Relação com a tarefa MRO-118

A tarefa **MRO-118** ("Criar Pack JIC para impressão") tem como objetivo principal gerar um relatório PDF que reúna os documentos aeronáuticos: **JIC** (Job Instruction Card), **JEC** (Job Equipment and Tool Card), **JMC**, **Shift Turnover**, **Calibrated Tool**, etc.

O fluxo de trabalho é:

```
┌──────────────────────────────────────────────────────────────┐
│                    jobcard_app (EDITOR)                       │
│                                                              │
│   1. Abre a imagem de fundo de uma página                     │
│   2. Usuário arrasta + posiciona os campos                   │
│   3. Ajusta fonte, negrito, tipo, multilinha                  │
│   4. Salva o layout em assets/data/pageN.json                 │
│   5. Exporta o código PHP para o ScriptCase                  │
└──────────────────────────┬───────────────────────────────────┘
                           │ exporta código
                           ▼
┌──────────────────────────────────────────────────────────────┐
│              blank_pdf_pack_jic (ScriptCase)                  │
│                                                              │
│   Gera o PDF real usando TCPDF:                               │
│   - Lê os dados do banco (tarefa, aeronave, etc.)           │
│   - Usa as posições definidas no editor para colocar texto   │
│   - Adiciona imagem de fundo + campos + códigos de barras   │
│   - Exibe o PDF inline ou disponibiliza para download        │
└──────────────────────────────────────────────────────────────┘
```

O `jobcard_app` foi criado **dentro** da pasta `__Tarefas/MRO-118/` porque é um artefato de suporte ao desenvolvimento — ele serve para definir e ajustar visualmente o layout antes de gerar o código PHP definitivo.

---

## Relação com o ScriptCase

No **ScriptCase**, a aplicação responsável por gerar o PDF final é:

- **`reports/blank_pdf_pack_jic/`** — Aplicação Blank do ScriptCase
  - `events/01_onExecute/onExecute.scriptcase` — Inicializa TCPDF e chama os métodos de cada página
  - `methods/mGerarPagina1JIC.php` — Método que desenha a Página 1 (JIC) com TCPDF
  - `methods/mGerarPagina2JEC.php` — Método que desenha a Página 2 (JEC)

### Como o editor se conecta ao ScriptCase

1. O usuário abre o `jobcard_app`, posiciona os campos visualmente
2. Clica em **📋 CSS** ou **🐘 TCPDF** na toolbar
3. O editor gera automaticamente o código PHP com as coordenadas e configurações de cada campo
4. Esse código é copiado para o método correspondente no ScriptCase (`mGerarPagina1JIC.php`, etc.)
5. O ScriptCase chama o método no evento `onExecute`, que produz o PDF final com dados reais do banco

### Estrutura de exportação

A pasta `export/` contém exemplos dos métodos exportados:
- `export/mGerarPagina1.php` — Código TCPDF gerado pelo editor para a página 1

---

## Estrutura de arquivos

```
jobcard_app/
├── README.md                    ← Este arquivo
├── index.php                    ← Página inicial (lista das 5 páginas)
├── editor.php                   ← Editor drag-and-drop principal (~1850 linhas)
├── pages_config.php             ← Configuração das páginas e itens padrão
├── pages_data.json              ← Metadados compartilhados entre páginas
├── test_js.php                  ← Script de teste (include do editor)
│
├── api/
│   ├── save.php                 ← AJAX: salva itens em assets/data/pageN.json
│   └── load.php                 ← AJAX: carrega itens de assets/data/pageN.json
│
├── assets/
│   ├── images/                  ← Imagens de fundo das páginas (page1.png ... page5.png)
│   └── data/                    ← Arquivos JSON de layout salvos (page1.json ... page5.json)
│
└── export/
    └── mGerarPagina1.php        ← Exemplo de método TCPDF exportado
```

---

## Como usar

1. Acesse `http://localhost/MRO_System/__Tarefas/MRO-118/jobcard_app/`
2. Escolha uma das 5 páginas para editar
3. Arraste os campos da sidebar para o canvas (sobre a imagem de fundo)
4. Ajuste posição, fonte, negrito, tipo, multilinha no painel de propriedades
5. Salve o layout com o botão **💾 Salvar**
6. Exporte o código PHP com o botão **🐘 TCPDF** e cole no método ScriptCase correspondente

---

## Tecnologias

- **PHP 8.2** — Renderização do editor e APIs de save/load
- **JavaScript puro** — Drag-and-drop, manipulação do DOM, zoom, grid, exportação
- **TCPDF** — Biblioteca PHP para geração do PDF final (usada no ScriptCase, não no editor)
- **ScriptCase** — Plataforma low-code que consome o código gerado pelo editor
