<?php
require_once __DIR__ . '/pages_config.php';

$pageNum = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if (!isset($PAGES[$pageNum])) {
    header('Location: index.php');
    exit;
}

$page = $PAGES[$pageNum];
$itemsJson = json_encode($page['items'], JSON_UNESCAPED_UNICODE);
$imageUrl = 'assets/images/' . htmlspecialchars($page['image']);
$pageTitle = htmlspecialchars($page['title']);
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Job Card - <?= $pageTitle ?></title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    background: #e5e5e5;
    font-family: Arial, Helvetica, sans-serif;
    display: flex;
    height: 100vh;
    overflow: hidden;
  }

  /* ===== TOP BAR ===== */
  .topbar {
    position: fixed;
    top: 0; left: 0; right: 0;
    height: 40px;
    background: #2c3e50;
    color: #fff;
    display: flex;
    align-items: center;
    padding: 0 16px;
    z-index: 9000;
    gap: 12px;
  }
  .topbar a { color: #fff; text-decoration: none; font-size: 13px; }
  .topbar a:hover { text-decoration: underline; }
  .topbar .sep { color: #7f8c8d; font-size: 16px; }
  .topbar .title { font-weight: 700; font-size: 14px; flex: 1; }
  .topbar .save-btn {
    background: #27ae60;
    border: none; color: #fff;
    padding: 4px 16px; border-radius: 4px;
    cursor: pointer; font-size: 12px; font-weight: 600;
  }
  .topbar .save-btn:hover { background: #219a52; }
  .topbar .save-btn.saving { background: #95a5a6; pointer-events: none; }
  .topbar .export-btn {
    background: #8e44ad;
    border: none; color: #fff;
    padding: 4px 14px; border-radius: 4px;
    cursor: pointer; font-size: 12px; font-weight: 600;
  }
  .topbar .export-btn:hover { background: #7d3c98; }
  .topbar .tcpdf-btn {
    background: #d35400;
    border: none; color: #fff;
    padding: 4px 14px; border-radius: 4px;
    cursor: pointer; font-size: 12px; font-weight: 600;
  }
  .topbar .tcpdf-btn:hover { background: #ba4a00; }
  .topbar .clear-btn {
    background: #c0392b;
    border: none; color: #fff;
    padding: 4px 14px; border-radius: 4px;
    cursor: pointer; font-size: 12px; font-weight: 600;
  }
  .topbar .clear-btn:hover { background: #a93226; }
  .topbar .grid-btn {
    background: #7f8c8d;
    border: none; color: #fff;
    padding: 4px 14px; border-radius: 4px;
    cursor: pointer; font-size: 12px; font-weight: 600;
    transition: background .15s;
  }
  .topbar .grid-btn:hover { background: #6c7a7d; }
  .topbar .grid-btn.active { background: #2980b9; }
  .topbar .grid-btn.active:hover { background: #2472a4; }
  .topbar .border-btn {
    background: #7f8c8d;
    border: none; color: #fff;
    padding: 4px 14px; border-radius: 4px;
    cursor: pointer; font-size: 12px; font-weight: 600;
    transition: background .15s;
  }
  .topbar .border-btn:hover { background: #6c7a7d; }
  .topbar .border-btn.active { background: #2980b9; }
  .topbar .border-btn.active:hover { background: #2472a4; }

  /* ===== ZOOM ===== */
  .topbar .zoom-group {
    display: flex;
    align-items: center;
    gap: 2px;
    background: rgba(255,255,255,0.1);
    border-radius: 4px;
    padding: 2px;
  }
  .topbar .zoom-group button {
    background: none;
    border: none;
    color: #fff;
    padding: 2px 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 700;
    line-height: 1;
    border-radius: 3px;
    transition: background .15s;
  }
  .topbar .zoom-group button:hover { background: rgba(255,255,255,0.15); }
  .topbar .zoom-group .zoom-label {
    font-size: 11px;
    color: #ccc;
    min-width: 36px;
    text-align: center;
    font-variant-numeric: tabular-nums;
  }

  /* ===== SIDEBAR ===== */
  .sidebar {
    width: 300px;
    min-width: 300px;
    background: #f5f5f5;
    border-right: 2px solid #ccc;
    display: flex;
    flex-direction: column;
    height: 100vh;
    padding-top: 40px;
  }

  .sidebar-search {
    padding: 8px 10px;
    border-bottom: 1px solid #ddd;
  }
  .sidebar-search input {
    width: 100%;
    padding: 6px 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 13px; outline: none;
  }
  .sidebar-search input:focus { border-color: #3498db; }

  .sidebar-items {
    flex: 1;
    overflow-y: auto;
    padding: 6px;
  }

  .drag-item {
    padding: 4px 8px;
    margin: 2px 4px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 11px;
    cursor: grab;
    user-select: none;
    transition: box-shadow .15s, border-color .15s;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .drag-item:hover {
    border-color: #3498db;
    box-shadow: 0 2px 6px rgba(52,152,219,0.2);
  }
  .drag-item:active { cursor: grabbing; }
  .drag-item.dragging { opacity: 0.4; border-style: dashed; }
  .drag-item .tag {
    display: inline-block;
    background: #3498db;
    color: #fff;
    font-size: 8px;
    padding: 1px 4px;
    border-radius: 3px;
    margin-right: 5px;
    font-weight: 600;
  }
  .drag-item .hide-btn {
    float: right;
    background: none; border: none;
    color: #bbb; cursor: pointer;
    font-size: 13px; line-height: 1; padding: 0 3px; margin-left: 4px;
  }
  .drag-item .hide-btn:hover { color: #e74c3c; }
  .drag-item.hidden-item { opacity: 0.3; background: #fafafa; border-style: dashed; }

  .restore-link {
    text-align: center; font-size: 11px; padding: 6px 4px 4px;
    color: #3498db; cursor: pointer; display: none; font-weight: 600;
  }
  .restore-link:hover { text-decoration: underline; }
  .restore-link.show { display: block; }

  /* ===== PROPS PANEL ===== */
  .props-panel {
    display: none;
    padding: 8px 10px;
    background: #fff;
    border-top: 2px solid #e74c3c;
  }
  .props-panel.show { display: block; }
  .props-panel .prop-title {
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    color: #e74c3c; margin-bottom: 6px;
  }
  .props-panel .prop-row {
    display: flex; align-items: center; gap: 6px; margin-bottom: 4px;
  }
  .props-panel .prop-row label { font-size: 11px; color: #555; min-width: 40px; }
  .props-panel .prop-row .prop-id {
    font-size: 11px; font-weight: 600; color: #2c3e50;
    background: #f0f0f0; padding: 2px 6px; border-radius: 3px;
  }
  .props-panel input[type="number"] {
    width: 55px; padding: 3px 5px;
    border: 1px solid #ccc; border-radius: 3px; font-size: 12px; outline: none;
  }
  .props-panel input[type="text"] {
    flex: 1;
    padding: 3px 5px;
    border: 1px solid #ccc;
    border-radius: 3px;
    font-size: 12px;
    outline: none;
    font-family: Arial, Helvetica, sans-serif;
  }
  .props-panel input[type="text"]:focus { border-color: #e74c3c; }
  .props-panel input[type="number"]:focus { border-color: #3498db; }
  .props-panel .btn-bold {
    background: #ecf0f1; border: 1px solid #ccc; border-radius: 3px;
    padding: 3px 10px; cursor: pointer; font-size: 12px; font-weight: 700;
    transition: all .15s; line-height: 1;
  }
  .props-panel .btn-bold.active { background: #2c3e50; color: #fff; border-color: #2c3e50; }
  .props-panel .btn-bold:hover { border-color: #3498db; }
  .props-panel .btn-edit-text {
    background: none;
    border: none;
    color: #3498db;
    cursor: pointer;
    font-size: 13px;
    padding: 2px 4px;
    text-decoration: underline;
    font-weight: 600;
  }
  .props-panel .btn-edit-text:hover { color: #2980b9; }
  .props-panel .btn-del-item {
    background: #e74c3c; color: #fff; border: none; border-radius: 3px;
    padding: 3px 10px; cursor: pointer; font-size: 11px; font-weight: 600;
  }
  .props-panel .btn-del-item:hover { background: #c0392b; }

  /* ===== MULTI-SELECT / ALINHAR ===== */
  .placed-item.multi-sel {
    border-color: #3498db;
    background: rgba(52,152,219,0.08);
    z-index: 12;
  }
  .placed-item.multi-sel .coord-label { display: block; background: #3498db; }
  .align-bar {
    display: none;
    padding: 6px 10px;
    background: #f0f8ff;
    border-top: 2px solid #3498db;
  }
  .align-bar.show { display: block; }
  .align-bar .align-title {
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    color: #3498db; margin-bottom: 4px;
  }
  .align-bar .align-btns {
    display: flex; gap: 4px;
  }
  .align-bar .align-btns button {
    flex: 1;
    background: #ecf0f1;
    border: 1px solid #ccc;
    border-radius: 3px;
    padding: 4px 2px;
    cursor: pointer;
    font-size: 13px;
    line-height: 1;
    transition: all .15s;
  }
  .align-bar .align-btns button:hover {
    background: #3498db;
    color: #fff;
    border-color: #3498db;
  }
  .align-bar .align-info {
    font-size: 10px; color: #888; margin-top: 3px; text-align: center;
  }

  /* ===== NOVO TEXTO ===== */
  .new-text-bar {
    padding: 6px 8px;
    border-bottom: 1px solid #ddd;
    display: flex;
    gap: 4px;
  }
  .new-text-bar input {
    flex: 1;
    padding: 4px 6px;
    border: 1px solid #ccc;
    border-radius: 3px;
    font-size: 11px;
    outline: none;
  }
  .new-text-bar input:focus { border-color: #27ae60; }
  .new-text-bar .btn-add {
    background: #27ae60;
    color: #fff;
    border: none;
    border-radius: 3px;
    padding: 4px 12px;
    cursor: pointer;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
  }
  .new-text-bar .btn-add:hover { background: #219a52; }

  /* ===== CUSTOM TAG ===== */
  .drag-item .tag.custom { background: #27ae60; }

  /* ===== STATUS BAR ===== */
  .status-bar {
    padding: 6px 10px;
    background: #ecf0f1;
    border-top: 1px solid #ddd;
    font-size: 11px; color: #555;
    display: flex; justify-content: space-between; align-items: center;
  }
  .status-bar .info { flex: 1; }

  /* ===== CANVAS ===== */
  .canvas {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 50px 20px 20px;
    overflow: auto;
    height: 100vh;
    background: #e5e5e5;
  }

  /* ===== LETTER (drop zone) ===== */
  /* Canvas identico ao PDF final: 215.9 x 279.4 mm = 816 x 1056 px @96dpi */
  /* background-size 100% 100% reproduz o Image() do TCPDF que estica a imagem */
  .letter {
    width: 816px;
    height: 1056px;
    margin: 0 auto;
    background: #fff;
    box-shadow: 0 0 30px rgba(0,0,0,0.15);
    position: relative;
    overflow: hidden;
    background-image: url('<?= $imageUrl ?>');
    background-size: 100% 100%;
    background-repeat: no-repeat;
    background-position: top left;
    flex-shrink: 0;
  }
  .letter.drag-over { outline: 3px solid #2ecc71; outline-offset: -3px; }

  /* ===== CANVAS WRAPPER (zoom) ===== */
  .canvas-wrapper {
    transform-origin: top left;
    transition: transform .15s ease;
    overflow: hidden;
    flex-shrink: 0;
  }

  /* ===== GRID OVERLAY ===== */
  .letter .grid-overlay {
    display: none;
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    pointer-events: none;
    z-index: 5;
  }
  .letter.show-grid .grid-overlay { display: block; }

  /* Grade azul — linhas finas a cada 10px, fortes a cada 50px */
  .letter.show-grid .grid-overlay {
    background-image:
      repeating-linear-gradient(
        to right,
        rgba(52,152,219,0.08) 0px, rgba(52,152,219,0.08) 1px,
        transparent 1px, transparent 10px
      ),
      repeating-linear-gradient(
        to bottom,
        rgba(52,152,219,0.08) 0px, rgba(52,152,219,0.08) 1px,
        transparent 1px, transparent 10px
      ),
      repeating-linear-gradient(
        to right,
        rgba(52,152,219,0.25) 0px, rgba(52,152,219,0.25) 1px,
        transparent 1px, transparent 50px
      ),
      repeating-linear-gradient(
        to bottom,
        rgba(52,152,219,0.25) 0px, rgba(52,152,219,0.25) 1px,
        transparent 1px, transparent 50px
      );
  }

  /* ===== MARQUEE SELECTION (seleção por área) ===== */
  .sel-rect {
    position: absolute;
    top: 0; left: 0;
    border: 1px solid #3498db;
    background: rgba(52,152,219,0.08);
    pointer-events: none;
    z-index: 50;
    display: none;
  }

  /* ===== BORDAS VISÍVEIS ===== */
  .letter.show-borders .placed-item {
    border-color: rgba(0,0,0,0.15);
  }
  .letter.show-borders .placed-item:hover {
    border-color: rgba(52,152,219,0.5);
  }
  .letter.show-borders .placed-item.selected {
    border-color: #e74c3c;
  }
  .letter.show-borders .placed-item.item--date {
    border-left-color: #3498db;
  }
  .letter.show-borders .placed-item.item--barcode {
    border-left-color: #8e44ad;
  }
  .letter.show-borders .placed-item.multiline {
    border-color: rgba(39,174,96,0.25);
  }
  .letter.show-borders .placed-item.multiline:hover {
    border-color: rgba(39,174,96,0.55);
  }

  .placed-item {
    position: absolute;
    cursor: move;
    user-select: none;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11px;
    color: #000;
    padding: 1px 2px;
    border: 1px solid transparent;
    white-space: nowrap;
    transition: border-color .15s, background .15s;
    z-index: 10;
  }
  .placed-item:hover {
    border-color: rgba(52,152,219,0.5);
    background: rgba(52,152,219,0.05);
  }
  .placed-item.selected {
    border-color: #e74c3c;
    background: rgba(231,76,60,0.06);
    z-index: 11;
  }
  .placed-item .coord-label {
    display: none;
    position: absolute;
    bottom: calc(100% + 2px);
    left: 0;
    background: #e74c3c;
    color: #fff;
    font-size: 9px;
    padding: 1px 5px;
    border-radius: 3px;
    white-space: nowrap;
    font-family: monospace;
  }
  .placed-item.selected .coord-label { display: block; }
  .placed-item .del-btn {
    display: none;
    position: absolute;
    top: -7px; right: -7px;
    width: 15px; height: 15px;
    background: #e74c3c; color: #fff;
    border: none; border-radius: 50%;
    font-size: 9px; line-height: 15px; text-align: center;
    cursor: pointer; z-index: 20;
  }
  .placed-item.selected .del-btn { display: block; }
  .placed-item .del-btn:hover { background: #c0392b; }

  /* ===== MULTILINHA ===== */
  .placed-item.multiline {
    border-color: rgba(39,174,96,0.25);
    background: rgba(39,174,96,0.03);
    white-space: normal;
    overflow-wrap: break-word;
  }
  .placed-item.multiline:hover {
    border-color: rgba(39,174,96,0.55);
  }
  .placed-item.multiline .maxw-label {
    display: none;
    position: absolute;
    top: calc(100% + 2px);
    right: 0;
    background: #27ae60;
    color: #fff;
    font-size: 8px;
    padding: 1px 4px;
    border-radius: 3px;
    white-space: nowrap;
    font-family: monospace;
  }
  .placed-item.multiline.selected .maxw-label { display: block; }
  .props-panel .multiline-row { margin-bottom: 0; }
  .props-panel .multiline-row label.cb-label {
    font-size: 12px; display: flex; align-items: center; gap: 4px; cursor: pointer;
  }

  /* ===== AL\u00c7A DE REDIMENSIONAMENTO ===== */
  .placed-item.multiline .resize-handle {
    display: none;
    position: absolute;
    top: 0; right: -5px;
    width: 10px; height: 100%;
    cursor: ew-resize;
    z-index: 25;
    touch-action: none;
  }
  .placed-item.multiline.selected .resize-handle { display: block; }
  .placed-item.multiline .resize-handle::after {
    content: '';
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    width: 3px; height: 24px;
    background: rgba(39,174,96,0.4);
    border-radius: 2px;
    transition: background .15s, height .15s;
  }
  .placed-item.multiline .resize-handle:hover::after,
  .placed-item.multiline .resize-handle.resizing::after {
    background: #27ae60;
    height: 36px;
  }

  /* ===== NOME DO CAMPO (ABAIXO DO BOX) ===== */
  .placed-item .field-name-label {
    display: none;
    position: absolute;
    top: calc(100% + 1px);
    left: 2px;
    font-size: 8px;
    color: #7f8c8d;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    line-height: 1;
    white-space: nowrap;
    pointer-events: none;
    z-index: 5;
  }
  .placed-item.selected .field-name-label { display: block; }
  .placed-item.item--date {
    border-left: 3px solid #3498db;
    padding-left: 5px;
    background: #fff;
    border-radius: 0 3px 3px 0;
  }
  .placed-item.item--date .field-name-label {
    color: #3498db;
  }
  .drag-item.item--date {
    background: rgba(52,152,219,0.06);
    border-left: 3px solid #3498db;
  }
  .drag-item.item--date .type-icon {
    font-size: 11px;
    margin-right: 3px;
  }

  /* ===== ITEM TIPO BARCODE ===== */
  .placed-item.item--barcode {
    border-left: 3px solid #8e44ad;
    padding-left: 5px;
    background: rgba(142,68,173,0.04);
    border-radius: 0 3px 3px 0;
    font-family: 'Courier New', Courier, monospace;
    letter-spacing: 0.5px;
  }
  .placed-item.item--barcode .field-name-label {
    color: #8e44ad;
    font-family: Arial, Helvetica, sans-serif;
  }
  .drag-item.item--barcode {
    background: rgba(142,68,173,0.06);
    border-left: 3px solid #8e44ad;
  }
  .drag-item.item--barcode .type-icon {
    font-size: 11px;
    margin-right: 3px;
  }

  /* ===== TOAST ===== */
  .toast {
    position: fixed;
    bottom: 30px; left: 50%;
    transform: translateX(-50%);
    background: #2c3e50; color: #fff;
    padding: 8px 20px; border-radius: 6px;
    font-size: 12px; z-index: 9999;
    opacity: 0; transition: opacity .3s;
    pointer-events: none;
  }
  .toast.show { opacity: 1; }
</style>
</head>
<body>

<!-- ===== TOPBAR ===== -->
<div class="topbar">
  <a href="index.php">&larr; Voltar</a>
  <span class="sep">|</span>
  <span class="title"><?= $pageTitle ?> — <?= htmlspecialchars($page['image']) ?></span>
  <button class="save-btn" id="saveBtn" onclick="salvar()">💾 Salvar</button>
  <button class="export-btn" id="exportBtn" onclick="exportarCSS()">📋 CSS</button>
  <button class="tcpdf-btn" id="tcpdfBtn" onclick="exportarTCPDF()">🐘 TCPDF</button>
  <button class="clear-btn" id="clearBtn" onclick="limparPagina()">🗑 Limpar</button>
  <span class="zoom-group">
    <button onclick="zoomOut()" title="Reduzir zoom">−</button>
    <span class="zoom-label" id="zoomLabel">100%</span>
    <button onclick="zoomIn()" title="Aumentar zoom">+</button>
    <button onclick="zoomReset()" title="Restaurar zoom">⟲</button>
  </span>
  <button class="grid-btn" id="gridBtn" onclick="toggleGrid()">▦ Grade</button>
  <button class="border-btn" id="borderBtn" onclick="toggleBorders()">▢ Bordas</button>
</div>

<!-- ===== SIDEBAR ===== -->
<div class="sidebar">
  <div class="sidebar-search">
    <input type="text" id="filtro" placeholder="Filtrar itens..." oninput="filtrarItens()">
  </div>
  <div class="new-text-bar">
    <input type="text" id="newTextInput" placeholder="Novo texto..." onkeydown="if(event.key==='Enter') adicionarNovoTexto()">
    <button class="btn-add" onclick="adicionarNovoTexto()">+ Novo</button>
  </div>
  <div class="sidebar-items" id="sidebarItems"></div>
  <div class="props-panel" id="propsPanel">
    <div class="prop-title">Propriedades</div>
    <div class="prop-row">
      <label>Item:</label>
      <span class="prop-id" id="propId">—</span>
    </div>
    <div class="prop-row">
      <label>Texto:</label>
      <input type="text" id="editTextInput" placeholder="Editar texto...">
      <button class="btn-edit-text" id="editTextBtn" onclick="aplicarEdicaoTexto()" title="Aplicar">✓</button>
    </div>
    <div class="prop-row">
      <label>Tipo:</label>
      <select id="typeSelect" onchange="alterarTipoItem()" style="flex:1;font-size:11px;padding:2px 4px;">
        <option value="text">Texto</option>
        <option value="date">📅 Data</option>
        <option value="barcode">🏷 Cód. Barras</option>
      </select>
    </div>
    <div class="prop-row" id="fieldNameRow" style="display:none;">
      <label>Campo:</label>
      <input type="text" id="fieldNameInput" placeholder="Ex: Data de Abertura" style="flex:1;" oninput="aplicarFieldName()">
    </div>
    <div class="prop-row">
      <label>Fonte:</label>
      <input type="number" id="fontSizeInput" value="11" min="4" max="72">
      <span style="font-size:11px;color:#999;">px</span>
      <button class="btn-bold" id="boldBtn" onclick="toggleBold()" title="Negrito"><b>B</b></button>
    </div>
    <div class="prop-row">
      <label>Larg.:</label>
      <input type="number" id="maxWidthInput" value="0" min="0" max="816" style="width:55px;">
      <span style="font-size:11px;color:#999;">px</span>
    </div>
    <div class="prop-row multiline-row">
      <label></label>
      <label class="cb-label">
        <input type="checkbox" id="multilineCheck"> Multilinha
      </label>
    </div>
    <div class="prop-row" style="margin-bottom:0;">
      <button class="btn-del-item" onclick="deletarItemSelecionado()">✖ Remover</button>
    </div>
  </div>
  <div class="align-bar" id="alignBar">
    <div class="align-title">Alinhar (Ctrl+click para múltiplos)</div>
    <div class="align-btns">
      <button onclick="alignLeft()" title="Alinhar à esquerda (mesmo X)">⬅</button>
      <button onclick="alignTop()" title="Alinhar ao topo (mesmo Y)">⬆</button>
      <button onclick="alignRight()" title="Alinhar à direita">➡</button>
      <button onclick="alignBottom()" title="Alinhar à base">⬇</button>
    </div>
    <div class="align-info" id="alignInfo"></div>
  </div>
  <div class="status-bar">
    <div class="info" id="statusInfo">Clique em um item</div>
  </div>
</div>

<!-- ===== CANVAS ===== -->
<div class="canvas">
  <div class="canvas-wrapper" id="canvasWrapper">
    <div class="letter" id="letter">
      <div class="grid-overlay"></div>
      <!-- Itens serão inseridos aqui -->
    </div>
  </div>
</div>

<!-- ===== TOAST ===== -->
<div class="toast" id="toast"></div>

<script>
// ===================================================================
// CONFIG CARREGADA DO PHP
// ===================================================================
const PAGE_NUM = <?= $pageNum ?>;
const PAGE_IMAGE = '<?= $page['image'] ?>';
const BASE_ITEMS = <?= $itemsJson ?>;
let hiddenItems = new Set();
let selectedEl = null;
let dragState = null;
let customItemCounter = 0;
let customItems = []; // { id, text } for user-created items
let multiSelected = new Set(); // elementos multi-selecionados (Ctrl+click)

// ===================================================================
// ZOOM
// ===================================================================
let zoomLevel = 1;

function aplicarZoom() {
  const wrapper = document.getElementById('canvasWrapper');
  const letter = document.getElementById('letter');
  const baseW = 816, baseH = 1056;
  wrapper.style.width = (baseW * zoomLevel) + 'px';
  wrapper.style.height = (baseH * zoomLevel) + 'px';
  wrapper.style.transform = 'scale(' + zoomLevel + ')';
  document.getElementById('zoomLabel').textContent = Math.round(zoomLevel * 100) + '%';
}

function zoomIn() {
  zoomLevel = Math.min(2, zoomLevel + 0.1);
  aplicarZoom();
}

function zoomOut() {
  zoomLevel = Math.max(0.3, zoomLevel - 0.1);
  aplicarZoom();
}

function zoomReset() {
  zoomLevel = 1;
  aplicarZoom();
}

// ===================================================================
// INICIAR SIDEBAR
// ===================================================================
function iniciarSidebar() {
  const container = document.getElementById('sidebarItems');
  container.innerHTML = '';

  // IDs já posicionados no canvas — não aparecem na sidebar
  const onCanvas = new Set();
  document.querySelectorAll('.placed-item').forEach(el => onCanvas.add(el.dataset.id));

  // --- Custom items section ---
  if (customItems.length > 0) {
    const secTitle = document.createElement('div');
    secTitle.style.cssText = 'font-size:10px;font-weight:700;text-transform:uppercase;color:#27ae60;padding:6px 10px 2px;letter-spacing:0.5px;';
    secTitle.textContent = '✦ Meus Textos';
    container.appendChild(secTitle);

    customItems.forEach(item => {
      if (!onCanvas.has(item.id)) {
        const el = criarDragItem(item.id, item.text, true, item.type, item.fieldName);
        container.appendChild(el);
      }
    });
  }

  // --- Base items ---
  BASE_ITEMS.forEach(item => {
    if (!onCanvas.has(item.id)) {
      const el = criarDragItem(item.id, item.text, false, item.type, item.fieldName);
      container.appendChild(el);
    }
  });

  // Restore link
  const restoreDiv = document.createElement('div');
  restoreDiv.className = 'restore-link' + (hiddenItems.size > 0 ? ' show' : '');
  restoreDiv.id = 'restoreLink';
  restoreDiv.textContent = hiddenItems.size > 0
    ? `↩ Restaurar ${hiddenItems.size} oculto(s)` : '↩ Restaurar ocultos';
  restoreDiv.onclick = restaurarOcultos;
  container.appendChild(restoreDiv);
}

function criarDragItem(id, text, isCustom, type, fieldName) {
  const el = document.createElement('div');
  const hidden = hiddenItems.has(id);
  el.className = 'drag-item' + (hidden ? ' hidden-item' : '') + (type === 'date' ? ' item--date' : '') + (type === 'barcode' ? ' item--barcode' : '');
  el.draggable = false;
  el.dataset.id = id;
  el.dataset.text = text;
  if (type) el.dataset.type = type;
  if (fieldName) el.dataset.fieldName = fieldName;
  let icon = '';
  if (type === 'date') icon = '<span class="type-icon">📅</span>';
  if (type === 'barcode') icon = '<span class="type-icon">🏷</span>';
  let label = fieldName || text;
  el.innerHTML = `<span class="tag${isCustom ? ' custom' : ''}">${id}</span>${icon}${label}<button class="hide-btn" title="Ocultar">✕</button>`;

  el.querySelector('.hide-btn').addEventListener('click', (e) => {
    e.stopPropagation();
    esconderItemSidebar(id);
  });

  // Mouse-based drag (mais confiável que HTML5 DnD)
  el.addEventListener('mousedown', (e) => {
    if (e.button !== 0) return;
    if (e.target.classList.contains('hide-btn')) return;
    if (hiddenItems.has(id)) return;
    e.preventDefault();
    sidebarDrag = {
      id,
      text,
      type: el.dataset.type || 'text',
      fieldName: el.dataset.fieldName || ''
    };
    el.classList.add('dragging');
    document.addEventListener('mousemove', onSidebarDragMove);
    document.addEventListener('mouseup', onSidebarDragUp);
  });

  return el;
}

// ===================================================================
// FILTRO
// ===================================================================
function filtrarItens() {
  const q = document.getElementById('filtro').value.toLowerCase();
  document.querySelectorAll('.drag-item').forEach(el => {
    const match = el.dataset.text.toLowerCase().includes(q) || el.dataset.id.toLowerCase().includes(q);
    el.style.display = match ? '' : 'none';
  });
}

// ===================================================================
// DROP ZONE (prevent defaults para segurança)
// ===================================================================
const letter = document.getElementById('letter');

letter.addEventListener('dragover', (e) => e.preventDefault());
letter.addEventListener('drop', (e) => e.preventDefault());

// ===================================================================
// CRIAR ITEM
// ===================================================================
function criarItem(id, text, left, top, fontSize, bold, maxWidth, multiline, type, fieldName) {
  // Remove se já existe
  const existing = document.querySelector(`.placed-item[data-id="${id}"]`);
  if (existing) existing.remove();

  const div = document.createElement('div');
  div.className = 'placed-item';
  div.dataset.id = id;
  div.dataset.text = text;
  div.dataset.type = type || 'text';
  div.dataset.fieldName = fieldName || '';
  if (type === 'date') div.classList.add('item--date');
  if (type === 'barcode') {
    div.classList.add('item--barcode');
    div.style.fontFamily = "'Courier New', Courier, monospace";
  }
  div.style.left = left + 'px';
  div.style.top = top + 'px';
  div.style.fontSize = (fontSize || 11) + 'px';
  div.dataset.fontSize = fontSize || 11;
  div.dataset.bold = bold ? '1' : '0';
  if (bold) div.style.fontWeight = 'bold';
  if (multiline) { div.classList.add('multiline'); div.dataset.multiline = '1'; }
  if (maxWidth > 0) {
    div.style.width = maxWidth + 'px';
    div.dataset.maxWidth = maxWidth;
  }

  // Label de largura máxima
  const mwLabel = document.createElement('span');
  mwLabel.className = 'maxw-label';
  mwLabel.textContent = maxWidth > 0 ? `max: ${maxWidth}px` : '';
  div.appendChild(mwLabel);

  // Alça de redimensionamento (só para multilinha)
  if (multiline) {
    const handle = document.createElement('span');
    handle.className = 'resize-handle';
    handle.title = 'Arraste para redimensionar';
    div.appendChild(handle);
    initResizeHandle(handle);
  }

  const coord = document.createElement('span');
  coord.className = 'coord-label';
  coord.textContent = `${Math.round(left)}px, ${Math.round(top)}px`;
  div.appendChild(coord);

  const del = document.createElement('button');
  del.className = 'del-btn';
  del.textContent = '×';
  del.addEventListener('click', (e) => {
    e.stopPropagation();
    if (selectedEl === div) deselectItem();
    div.remove();
    iniciarSidebar();
    atualizarStatus();
    marcarAlterado();
  });
  div.appendChild(del);

  const span = document.createElement('span');
  span.className = 'placed-text';
  span.textContent = text;
  div.appendChild(span);

  // Label do nome do campo (abaixo do box em position: absolute)
  if (fieldName) {
    const fnLabel = document.createElement('span');
    fnLabel.className = 'field-name-label';
    fnLabel.textContent = fieldName;
    div.appendChild(fnLabel);
  }

  // Double-click to edit text
  span.addEventListener('dblclick', (e) => {
    e.stopPropagation();
    selecionarItem(div);
    document.getElementById('editTextInput').focus();
    document.getElementById('editTextInput').select();
  });

  // Mouse drag
  div.addEventListener('mousedown', (e) => {
    if (e.target === del || e.target.classList.contains('resize-handle')) return;
    e.preventDefault();

    if (e.ctrlKey || e.metaKey) {
      toggleMultiSelect(div);
      return;
    }

    clearMultiSelect();
    selecionarItem(div);

    const rect = letter.getBoundingClientRect();
    const scaleX = letter.offsetWidth / rect.width;
    const scaleY = letter.offsetHeight / rect.height;
    const offsetX = (e.clientX - rect.left) * scaleX - parseFloat(div.style.left);
    const offsetY = (e.clientY - rect.top) * scaleY - parseFloat(div.style.top);

    dragState = { el: div, offsetX, offsetY, scaleX, scaleY };
    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup', onMouseUp);
  });

  div.addEventListener('touchstart', (e) => {
    if (e.target === del) return;
    const touch = e.touches[0];
    div.dispatchEvent(new MouseEvent('mousedown', { clientX: touch.clientX, clientY: touch.clientY }));
  }, { passive: true });

  letter.appendChild(div);
  selecionarItem(div);
  atualizarStatus();
  marcarAlterado();
}

function onMouseMove(e) {
  if (!dragState) return;
  const rect = letter.getBoundingClientRect();
  const newLeft = (e.clientX - rect.left) * dragState.scaleX - dragState.offsetX;
  const newTop = (e.clientY - rect.top) * dragState.scaleY - dragState.offsetY;
  const maxLeft = letter.offsetWidth - dragState.el.offsetWidth;
  const maxTop = letter.offsetHeight - dragState.el.offsetHeight;
  dragState.el.style.left = Math.max(0, Math.min(maxLeft, newLeft)) + 'px';
  dragState.el.style.top = Math.max(0, Math.min(maxTop, newTop)) + 'px';
  const coord = dragState.el.querySelector('.coord-label');
  if (coord) coord.textContent = `${Math.round(newLeft)}px, ${Math.round(newTop)}px`;
  atualizarStatus();
}
function onMouseUp() {
  const el = dragState?.el;
  dragState = null;
  document.removeEventListener('mousemove', onMouseMove);
  document.removeEventListener('mouseup', onMouseUp);
  if (el) marcarAlterado();
}

// ===================================================================
// SIDEBAR DRAG (por mouse events, substitui HTML5 DnD)
// ===================================================================
let sidebarDrag = null;

function onSidebarDragMove(e) {
  if (!sidebarDrag) return;
  // Feedback visual no letter
  const rect = letter.getBoundingClientRect();
  const inside = e.clientX >= rect.left && e.clientX <= rect.right &&
                 e.clientY >= rect.top && e.clientY <= rect.bottom;
  letter.classList.toggle('drag-over', inside);
  document.body.style.cursor = inside ? 'copy' : 'not-allowed';
}

function onSidebarDragUp(e) {
  const drag = sidebarDrag;
  sidebarDrag = null;
  document.removeEventListener('mousemove', onSidebarDragMove);
  document.removeEventListener('mouseup', onSidebarDragUp);
  document.body.style.cursor = '';

  // Limpa dragging de todos os sidebar items
  document.querySelectorAll('.drag-item.dragging').forEach(el => el.classList.remove('dragging'));
  letter.classList.remove('drag-over');

  if (!drag) return;

  const rect = letter.getBoundingClientRect();
  if (e.clientX >= rect.left && e.clientX <= rect.right &&
      e.clientY >= rect.top && e.clientY <= rect.bottom) {
    const scaleX = letter.offsetWidth / rect.width;
    const scaleY = letter.offsetHeight / rect.height;
    const left = (e.clientX - rect.left) * scaleX;
    const top = (e.clientY - rect.top) * scaleY;
    criarItem(drag.id, drag.text, left, top, 11, false, 0, false, drag.type, drag.fieldName);
    iniciarSidebar();
    mostrarToast(`"${drag.id}" adicionado`);
  }
}

// ===================================================================
// AL\u00c7A DE REDIMENSIONAMENTO (MULTILINHA)
// ===================================================================
let resizeState = null;

function initResizeHandle(handle) {
  handle.addEventListener('mousedown', (e) => {
    e.preventDefault();
    e.stopPropagation();
    const div = handle.parentElement;
    const letterRect = letter.getBoundingClientRect();
    const scaleX = letter.offsetWidth / letterRect.width;
    const startX = e.clientX;
    const startW = div.offsetWidth;

    handle.classList.add('resizing');
    resizeState = { div, startX, startW, scaleX };
    document.addEventListener('mousemove', onResizeMove);
    document.addEventListener('mouseup', onResizeUp);
  });

  handle.addEventListener('touchstart', (e) => {
    const touch = e.touches[0];
    handle.dispatchEvent(new MouseEvent('mousedown', { clientX: touch.clientX, clientY: touch.clientY }));
  }, { passive: true });
}

function onResizeMove(e) {
  if (!resizeState) return;
  const { div, startX, startW, scaleX } = resizeState;
  const delta = (e.clientX - startX) * scaleX;
  const newW = Math.max(20, Math.round(startW + delta));
  div.style.width = newW + 'px';
  div.dataset.maxWidth = newW;
  // Atualiza label
  const mwLabel = div.querySelector('.maxw-label');
  if (mwLabel) mwLabel.textContent = 'max: ' + newW + 'px';
  // Atualiza input se for o item selecionado
  if (div === selectedEl) {
    document.getElementById('maxWidthInput').value = newW;
  }
}

function onResizeUp() {
  if (resizeState) {
    const handle = resizeState.div.querySelector('.resize-handle');
    if (handle) handle.classList.remove('resizing');
    marcarAlterado();
  }
  resizeState = null;
  document.removeEventListener('mousemove', onResizeMove);
  document.removeEventListener('mouseup', onResizeUp);
}

// ===================================================================
// SELEÇÃO / PROPRIEDADES
// ===================================================================
function selecionarItem(el) {
  if (selectedEl && selectedEl !== el) selectedEl.classList.remove('selected');
  selectedEl = el;
  if (el) { el.classList.add('selected'); el.parentNode.appendChild(el); }
  atualizarPropsPanel();
  atualizarStatus();
}

function deselectItem() {
  if (selectedEl) { selectedEl.classList.remove('selected'); selectedEl = null; }
  clearMultiSelect();
  atualizarPropsPanel();
  atualizarStatus();
}

function atualizarPropsPanel() {
  const panel = document.getElementById('propsPanel');
  const alignBar = document.getElementById('alignBar');

  if (!selectedEl && multiSelected.size === 0) {
    panel.classList.remove('show');
    alignBar.classList.remove('show');
    return;
  }

  // Show alignment bar if multi-selected
  if (multiSelected.size >= 2) {
    alignBar.classList.add('show');
    document.getElementById('alignInfo').textContent = `${multiSelected.size} itens selecionados`;
  } else {
    alignBar.classList.remove('show');
  }

  if (!selectedEl) { panel.classList.remove('show'); return; }

  panel.classList.add('show');
  document.getElementById('propId').textContent = selectedEl.dataset.id;
  document.getElementById('editTextInput').value = selectedEl.dataset.text || '';
  document.getElementById('fontSizeInput').value = parseInt(selectedEl.dataset.fontSize) || 11;
  document.getElementById('boldBtn').classList.toggle('active', selectedEl.dataset.bold === '1');
  document.getElementById('maxWidthInput').value = parseInt(selectedEl.dataset.maxWidth) || 0;
  document.getElementById('multilineCheck').checked = selectedEl.dataset.multiline === '1';

  // Type
  const itemType = selectedEl.dataset.type || 'text';
  document.getElementById('typeSelect').value = itemType;
  const fnRow = document.getElementById('fieldNameRow');
  fnRow.style.display = 'flex';
  document.getElementById('fieldNameInput').value = selectedEl.dataset.fieldName || '';
}

function toggleBold() {
  if (!selectedEl) return;
  const newBold = selectedEl.dataset.bold === '1' ? '0' : '1';
  selectedEl.dataset.bold = newBold;
  selectedEl.style.fontWeight = newBold === '1' ? 'bold' : 'normal';
  atualizarPropsPanel();
  marcarAlterado();
}

function alterarTipoItem() {
  if (!selectedEl) return;
  const newType = document.getElementById('typeSelect').value;
  selectedEl.dataset.type = newType;
  selectedEl.classList.toggle('item--date', newType === 'date');
  selectedEl.classList.toggle('item--barcode', newType === 'barcode');
  // Fonte monospace para barcode
  if (newType === 'barcode') {
    selectedEl.style.fontFamily = "'Courier New', Courier, monospace";
  } else {
    selectedEl.style.fontFamily = '';
  }
  // Nome do campo sempre visivel
  document.getElementById('fieldNameRow').style.display = 'flex';
  if (!selectedEl.dataset.fieldName) {
    const defaultName = newType === 'date' ? 'Data' : newType === 'barcode' ? 'Cód. Barras' : 'Campo';
    selectedEl.dataset.fieldName = defaultName;
    document.getElementById('fieldNameInput').value = defaultName;
  }
  // Atualiza sidebar (se o item estiver na lista)
  const dragEl = document.querySelector(`.drag-item[data-id="${selectedEl.dataset.id}"]`);
  if (dragEl) {
    dragEl.dataset.type = newType;
    dragEl.classList.toggle('item--date', newType === 'date');
    dragEl.classList.toggle('item--barcode', newType === 'barcode');
  }
  marcarAlterado();
}

function aplicarFieldName() {
  if (!selectedEl) return;
  const fn = document.getElementById('fieldNameInput').value.trim();
  selectedEl.dataset.fieldName = fn;
  // Atualiza label no canvas
  let fnLabel = selectedEl.querySelector('.field-name-label');
  if (fn) {
    if (!fnLabel) {
      fnLabel = document.createElement('span');
      fnLabel.className = 'field-name-label';
      selectedEl.appendChild(fnLabel);
    }
    fnLabel.textContent = fn;
  } else if (fnLabel) {
    fnLabel.remove();
  }
  // Atualiza sidebar
  const dragEl = document.querySelector(`.drag-item[data-id="${selectedEl.dataset.id}"]`);
  if (dragEl) {
    dragEl.dataset.fieldName = fn;
  }
  marcarAlterado();
}

function deletarItemSelecionado() {
  if (!selectedEl) return;
  const id = selectedEl.dataset.id;
  selectedEl.remove();
  selectedEl = null;
  iniciarSidebar();
  atualizarPropsPanel();
  atualizarStatus();
  marcarAlterado();
  mostrarToast(`"${id}" removido`);
}

// ===================================================================
// MULTI-SELEÇÃO E ALINHAMENTO
// ===================================================================
function toggleMultiSelect(el) {
  if (multiSelected.has(el)) {
    multiSelected.delete(el);
    el.classList.remove('multi-sel');
  } else {
    multiSelected.add(el);
    el.classList.add('multi-sel');
  }
  selecionarItem(multiSelected.size > 0 ? null : el);
  // Force props panel update
  if (multiSelected.size >= 2) {
    document.getElementById('propsPanel').classList.add('show');
  }
  atualizarPropsPanel();
  atualizarStatus();
}

function clearMultiSelect() {
  multiSelected.forEach(el => el.classList.remove('multi-sel'));
  multiSelected.clear();
}

function getMultiArray() {
  return [...multiSelected];
}

function alignLeft() {
  const arr = getMultiArray();
  if (arr.length < 2) { mostrarToast('Selecione 2+ itens (Ctrl+click)'); return; }
  const ref = Math.min(...arr.map(el => parseFloat(el.style.left)));
  arr.forEach(el => { el.style.left = ref + 'px'; atualizarCoord(el); });
  marcarAlterado(); atualizarStatus();
  mostrarToast(`Alinhado à esquerda (${ref}px)`);
}

function alignRight() {
  const arr = getMultiArray();
  if (arr.length < 2) { mostrarToast('Selecione 2+ itens (Ctrl+click)'); return; }
  const ref = Math.max(...arr.map(el => parseFloat(el.style.left) + (el.offsetWidth || 0)));
  arr.forEach(el => { el.style.left = (ref - (el.offsetWidth || 0)) + 'px'; atualizarCoord(el); });
  marcarAlterado(); atualizarStatus();
  mostrarToast(`Alinhado à direita (${ref}px)`);
}

function alignTop() {
  const arr = getMultiArray();
  if (arr.length < 2) { mostrarToast('Selecione 2+ itens (Ctrl+click)'); return; }
  const ref = Math.min(...arr.map(el => parseFloat(el.style.top)));
  arr.forEach(el => { el.style.top = ref + 'px'; atualizarCoord(el); });
  marcarAlterado(); atualizarStatus();
  mostrarToast(`Alinhado ao topo (${ref}px)`);
}

function alignBottom() {
  const arr = getMultiArray();
  if (arr.length < 2) { mostrarToast('Selecione 2+ itens (Ctrl+click)'); return; }
  const ref = Math.max(...arr.map(el => parseFloat(el.style.top) + (el.offsetHeight || 0)));
  arr.forEach(el => { el.style.top = (ref - (el.offsetHeight || 0)) + 'px'; atualizarCoord(el); });
  marcarAlterado(); atualizarStatus();
  mostrarToast(`Alinhado à base (${ref}px)`);
}

function atualizarCoord(el) {
  const coord = el.querySelector('.coord-label');
  if (coord) coord.textContent = `${Math.round(parseFloat(el.style.left))}px, ${Math.round(parseFloat(el.style.top))}px`;
}

// ===================================================================
// EDITAR TEXTO
// ===================================================================
function aplicarEdicaoTexto() {
  if (!selectedEl) return;
  const input = document.getElementById('editTextInput');
  const newText = input.value.trim();
  if (newText === '') { mostrarToast('O texto não pode ficar vazio'); return; }

  const oldText = selectedEl.dataset.text;
  if (newText === oldText) return;

  selectedEl.dataset.text = newText;
  const span = selectedEl.querySelector('.placed-text');
  if (span) span.textContent = newText;

  // Update sidebar item text too
  const dragEl = document.querySelector(`.drag-item[data-id="${selectedEl.dataset.id}"]`);
  if (dragEl) {
    dragEl.dataset.text = newText;
    // Update label keeping the tag
    const tag = dragEl.querySelector('.tag');
    const hideBtn = dragEl.querySelector('.hide-btn');
    dragEl.innerHTML = '';
    if (tag) dragEl.appendChild(tag);
    dragEl.appendChild(document.createTextNode(newText));
    if (hideBtn) dragEl.appendChild(hideBtn);
  }

  marcarAlterado();
  mostrarToast('Texto atualizado');
}

// Enter key on edit input
document.getElementById('editTextInput').addEventListener('keydown', (e) => {
  if (e.key === 'Enter') aplicarEdicaoTexto();
});

// ===================================================================
// ADICIONAR NOVO TEXTO
// ===================================================================
function adicionarNovoTexto() {
  const input = document.getElementById('newTextInput');
  const text = input.value.trim();
  if (text === '') { mostrarToast('Digite um texto'); return; }

  customItemCounter++;
  const id = 'new-' + customItemCounter;

  // Add to custom items list
  customItems.push({ id, text });

  // Add to letter first, then refresh sidebar (que j\u00e1 vai ocult\u00e1-lo)
  criarItem(id, text, 20, 20, 11, false);
  iniciarSidebar();

  input.value = '';
  input.focus();
  mostrarToast(`"${id}" criado`);
}

// ===================================================================
// OCULTAR ITEM NA SIDEBAR
// ===================================================================
function esconderItemSidebar(id) {
  hiddenItems.add(id);
  document.querySelectorAll('.drag-item').forEach(el => {
    if (el.dataset.id === id) { el.classList.add('hidden-item'); el.draggable = false; }
  });
  const link = document.getElementById('restoreLink');
  if (link) {
    link.textContent = `↩ Restaurar ${hiddenItems.size} oculto(s)`;
    link.classList.add('show');
  }
}

function restaurarOcultos() {
  hiddenItems.clear();
  document.querySelectorAll('.drag-item').forEach(el => {
    el.classList.remove('hidden-item'); el.draggable = true;
  });
  const link = document.getElementById('restoreLink');
  if (link) { link.textContent = '↩ Restaurar ocultos'; link.classList.remove('show'); }
  mostrarToast('Itens restaurados');
}

// ===================================================================
// STATUS
// ==============================================================
function atualizarStatus() {
  const info = document.getElementById('statusInfo');
  if (selectedEl) {
    const id = selectedEl.dataset.id;
    const left = Math.round(parseFloat(selectedEl.style.left));
    const top = Math.round(parseFloat(selectedEl.style.top));
    const fs = selectedEl.dataset.fontSize || 11;
    const bold = selectedEl.dataset.bold === '1' ? ' · Negrito' : '';
    info.textContent = `${id} → (${left}, ${top})  ·  ${fs}px${bold}`;
  } else {
    const total = document.querySelectorAll('.placed-item').length;
    info.textContent = total > 0 ? `${total} item(ns)` : 'Arraste itens da lista para o card';
  }
}

// ===================================================================
// CONTROLE DE ALTERAÇÕES
// ===================================================================
let alterado = false;
function marcarAlterado() { alterado = true; }
function marcarSalvo() { alterado = false; }

// ===================================================================
// SALVAR (AJAX)
// ===================================================================
function salvar() {
  const btn = document.getElementById('saveBtn');
  btn.classList.add('saving');
  btn.textContent = '⏳ Salvando...';

  const items = [];
  document.querySelectorAll('.placed-item').forEach(el => {
    items.push({
      id: el.dataset.id,
      text: el.dataset.text || el.querySelector('.placed-text')?.textContent || '',
      left: Math.round(parseFloat(el.style.left)),
      top: Math.round(parseFloat(el.style.top)),
      fontSize: parseInt(el.dataset.fontSize) || 11,
      bold: el.dataset.bold === '1',
      maxWidth: parseInt(el.dataset.maxWidth) || 0,
      multiline: el.dataset.multiline === '1',
      type: el.dataset.type || 'text',
      fieldName: el.dataset.fieldName || ''
    });
  });

  fetch('api/save.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      page: PAGE_NUM,
      items: items,
      customItems: customItems
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      mostrarToast('Posições salvas!');
      marcarSalvo();
    } else {
      mostrarToast('Erro ao salvar: ' + (data.error || 'desconhecido'));
    }
  })
  .catch(err => {
    mostrarToast('Erro de conexão ao salvar');
  })
  .finally(() => {
    btn.classList.remove('saving');
    btn.textContent = '💾 Salvar';
  });
}

// ===================================================================
// CARREGAR DADOS SALVOS
// ===================================================================
function carregarSalvo() {
  fetch('api/load.php?page=' + PAGE_NUM)
  .then(r => r.json())
  .then(data => {
    // Restore custom items from saved data
    if (data.customItems && Array.isArray(data.customItems)) {
      customItemCounter = 0;
      customItems = data.customItems.map(ci => {
        const match = ci.id && ci.id.match(/^new-(\d+)$/);
        if (match) customItemCounter = Math.max(customItemCounter, parseInt(match[1]));
        return ci;
      });
    }

    if (data.saved && data.items && data.items.length > 0) {
      // Place only saved items — items removidos intencionalmente não voltam
      data.items.forEach(si => {
        // Se for base item, busca texto original do config
        const base = BASE_ITEMS.find(b => b.id === si.id);
        const text = si.text || (base ? base.text : '');
        // Mescla type/fieldName do config se não existirem no saved
        const itemType = si.type || (base ? base.type : null) || 'text';
        const itemField = si.fieldName !== undefined ? si.fieldName : (base ? base.fieldName || '' : '');
        if (si.id.startsWith('new-')) {
          const ci = customItems.find(c => c.id === si.id);
          if (ci) {
            criarItem(ci.id, ci.text, si.left, si.top, si.fontSize || 11, si.bold || false, si.maxWidth || 0, si.multiline || false, itemType, itemField);
          }
        } else if (base) {
          criarItem(si.id, text, si.left, si.top, si.fontSize || 11, si.bold || false, si.maxWidth || 0, si.multiline || false, itemType, itemField);
        }
      });

      mostrarToast(`Posições carregadas (${data.items.length} itens)`);
    } else {
      // Nada salvo - usa posições iniciais
      BASE_ITEMS.forEach(item => {
        criarItem(item.id, item.text, item.left, item.top, 11, item.bold || false, item.maxWidth || 0, item.multiline || false, item.type || 'text', item.fieldName || '');
      });
    }
    iniciarSidebar();
    marcarSalvo();
  })
  .catch(err => {
    mostrarToast('Erro ao carregar dados');
    // Fallback: carrega posições iniciais
    BASE_ITEMS.forEach(item => {
      criarItem(item.id, item.text, item.left, item.top, 11, item.bold || false, item.maxWidth || 0, item.multiline || false, item.type || 'text', item.fieldName || '');
    });
  });
}

// ===================================================================
// FONT SIZE INPUT
// ===================================================================
// ===================================================================
// LIMPAR PÁGINA
// ===================================================================
function limparPagina() {
  const total = document.querySelectorAll('.placed-item').length;
  if (total === 0) { mostrarToast('Nenhum item para limpar'); return; }

  if (!confirm(`Tem certeza que deseja remover todos os ${total} itens desta página?`)) return;

  document.querySelectorAll('.placed-item').forEach(el => el.remove());
  selectedEl = null;
  iniciarSidebar();
  atualizarPropsPanel();
  atualizarStatus();
  marcarAlterado();
  mostrarToast(`Página limpa (${total} itens removidos)`);
}

// ===================================================================
// EXPORTAR CSS
// ===================================================================
function exportarCSS() {
  const btn = document.getElementById('exportBtn');
  btn.textContent = '⏳ Gerando...';

  const items = [];
  document.querySelectorAll('.placed-item').forEach(el => {
    items.push({
      id: el.dataset.id,
      text: el.dataset.text || el.querySelector('.placed-text')?.textContent || '',
      left: Math.round(parseFloat(el.style.left)),
      top: Math.round(parseFloat(el.style.top)),
      fontSize: parseInt(el.dataset.fontSize) || 11,
      bold: el.dataset.bold === '1',
      maxWidth: parseInt(el.dataset.maxWidth) || 0,
      multiline: el.dataset.multiline === '1',
      type: el.dataset.type || 'text',
      fieldName: el.dataset.fieldName || ''
    });
  });

  if (items.length === 0) {
    mostrarToast('Nenhum item para exportar');
    btn.textContent = '📋 Exportar CSS';
    return;
  }

  const letterW = 816; // 8.5in @ 96dpi
  const letterH = 1056; // 11in @ 96dpi

  let css = `/* ==========================================
   Job Card — Página ${PAGE_NUM}
   Gerado em ${new Date().toLocaleString('pt-BR')}
   ========================================== */

.letter {
  position: relative;
  width: ${letterW}px;
  height: ${letterH}px;
  margin: 0 auto;
  background: #fff;
  overflow: hidden;
}

`;

  items.forEach((item, i) => {
    const safeId = item.id.replace(/[^a-zA-Z0-9_-]/g, '_');
    const fontWeight = item.bold ? 'bold' : 'normal';
    const isMultiline = item.multiline && item.maxWidth > 0;
    const whitespace = isMultiline ? 'normal' : 'nowrap';
    const maxW = isMultiline ? `\n  max-width: ${item.maxWidth}px;` : '';
    const fontFamily = item.type === 'barcode' ? "'Courier New', Courier, monospace" : 'Arial, Helvetica, sans-serif';

    css += `.item--${safeId} {
  position: absolute;
  left: ${item.left}px;
  top: ${item.top}px;
  font-size: ${item.fontSize}px;
  font-weight: ${fontWeight};
  font-family: ${fontFamily};
  line-height: 1.2;
  white-space: ${whitespace};${maxW}
}

`;
  });

  // Add HTML structure example as CSS comment
  css += `/* Exemplo de uso:
<div class="letter">
${items.map(item => `  <span class="item--${item.id.replace(/[^a-zA-Z0-9_-]/g, '_')}">${item.text.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</span>`).join('\n')}
</div>
*/`;

  // Download
  const blob = new Blob([css], { type: 'text/css;charset=utf-8' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `jobcard-pagina${PAGE_NUM}.css`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);

  btn.textContent = '📋 Exportar CSS';
  mostrarToast(`CSS exportado (${items.length} itens)`);
}

// ===================================================================
// EXPORTAR TCPDF (PHP prØximo para ScriptCase)
// ===================================================================
function exportarTCPDF() {
  const btn = document.getElementById('tcpdfBtn');
  const orig = btn.textContent;
  btn.textContent = '⏳ Gerando...';

  const items = [];
  const lw = letter.offsetWidth;
  const lh = letter.offsetHeight;
  document.querySelectorAll('.placed-item').forEach(el => {
    const left = Math.round(parseFloat(el.style.left));
    const top = Math.round(parseFloat(el.style.top));
    // Ignora itens fora da área do canvas
    if (left >= lw || top >= lh) return;
    items.push({
      id: el.dataset.id,
      text: el.dataset.text || el.querySelector('.placed-text')?.textContent || '',
      left,
      top,
      fontSize: parseInt(el.dataset.fontSize) || 11,
      bold: el.dataset.bold === '1',
      maxWidth: parseInt(el.dataset.maxWidth) || 0,
      multiline: el.dataset.multiline === '1',
      type: el.dataset.type || 'text',
      fieldName: el.dataset.fieldName || ''
    });
  });

  if (items.length === 0) {
    mostrarToast('Nenhum item para exportar');
    btn.textContent = orig;
    return;
  }

  // Convers„o: 816px = 215.9mm
  const pxToMm = 215.9 / 816;
  const pxToPt = 0.75;

  // Agrupa por fonte pra minimizar SetFont
  const groups = {};
  items.forEach(item => {
    const key = item.fontSize + '|' + (item.bold ? 'B' : '');
    if (!groups[key]) groups[key] = [];
    groups[key].push(item);
  });

  let php = '<' + '?php\n\nfunction mGerarPagina' + PAGE_NUM + 'XXX($pdf) {\n    // =========================================================\n    // Pagina ' + PAGE_NUM + ' — gerada do editor em ' + new Date().toLocaleString('pt-BR') + '\n    // Dimensoes LETTER: 215,9 x 279,4 mm\n    // =========================================================\n\n    $pdf->AddPage();\n\n    // --- Imagem de fundo ---\n    $imgFundo = \'../_lib/img/grp__NM__bg__NM__' + PAGE_IMAGE + '\';\n    $pdf->Image($imgFundo, 0, 0, 215.9, 279.4, \'PNG\', \'\', \'\', false, 300, \'\', false, false, 0);\n\n';

  Object.keys(groups).sort().forEach(key => {
    const [fs, b] = key.split('|');
    const pts = Math.round(parseFloat(fs) * pxToPt * 100) / 100;
    const style = b === 'B' ? "'B'" : "''";

    php += `    // Fonte: ${fs}px ${b ? 'bold' : 'normal'} → ${pts}pt
    \$pdf->SetFont('helvetica', ${style}, ${pts});
`;

    groups[key].forEach(item => {
      const mmX = Math.round(item.left * pxToMm * 100) / 100;
      const mmY = Math.round(item.top * pxToMm * 100) / 100;
      const escaped = item.text
        .replace(/\$/g, '\\$')
        .replace(/"/g, '\\"')
        .replace(/'/g, "\\'");

      if (item.type === 'barcode') {
        const hMm = Math.max(5, Math.round(item.fontSize * pxToMm * 100) / 100);
        const fn = item.fieldName || '-';
        php += `    \$pdf->write1DBarcode('${escaped}', 'C39', ${mmX}, ${mmY}, 100, ${hMm}, null, array('text' => true), 'N'); // ${item.id} - ${fn} - ${escaped} (barcode)\n`;
      } else if (item.type === 'date') {
        const ptsNum = parseFloat(pts);
        const rectH = Math.round(ptsNum * 0.3528 * 100) / 100;
        const px15Mm = 15 * 215.9 / 816;
        const rectY = Math.round((mmY - ptsNum * 0.268 + px15Mm) * 100) / 100;
        const fn = item.fieldName || '-';
        php += `    \$pdf->SetTextColor(0, 0, 0);\n`;
        php += `    \$pdf->SetFillColor(255, 255, 255);\n`;
        php += `    \$pdf->SetXY(${mmX}, ${rectY});\n`;
        php += `    \$pdf->Cell(18, ${rectH}, '${escaped}', 0, 0, 'L', true, '', 0, false, 'T', 'T'); // ${item.id} - ${fn} - ${escaped} (date)\n`;
      } else if (item.multiline && item.maxWidth > 0) {
        const mmW = Math.round(item.maxWidth * pxToMm * 100) / 100;
        const fn = item.fieldName || '-';
        php += `    \$pdf->MultiCell(${mmW}, 0, '${escaped}', 0, 'L', false, 1, ${mmX}, ${mmY}, true); // ${item.id} - ${fn} - ${escaped} (multilinha)\n`;
      } else {
        const fn = item.fieldName || '-';
        php += `    \$pdf->Text(${mmX}, ${mmY}, '${escaped}'); // ${item.id} - ${fn} - ${escaped}\n`;
      }
    });
  });

  php += `}
`;

  // Download
  const blob = new Blob([php], { type: 'text/php;charset=utf-8' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `mGerarPagina${PAGE_NUM}.php`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);

  btn.textContent = orig;
  mostrarToast(`TCPDF exportado (${items.length} itens)`);
}

// ===================================================================
// FONT SIZE INPUT
// ===================================================================
document.getElementById('fontSizeInput').addEventListener('input', function() {
  if (!selectedEl) return;
  const val = parseInt(this.value);
  if (isNaN(val) || val < 1) return;
  selectedEl.style.fontSize = val + 'px';
  selectedEl.dataset.fontSize = val;
  atualizarStatus();
  marcarAlterado();
});

document.getElementById('maxWidthInput').addEventListener('input', function() {
  if (!selectedEl) return;
  const val = parseInt(this.value);
  if (isNaN(val) || val < 0) return;
  if (val > 0) {
    selectedEl.style.width = val + 'px';
    selectedEl.dataset.maxWidth = val;
    // Update label
    const mwLabel = selectedEl.querySelector('.maxw-label');
    if (mwLabel) mwLabel.textContent = 'max: ' + val + 'px';
  } else {
    selectedEl.style.width = '';
    delete selectedEl.dataset.maxWidth;
    const mwLabel = selectedEl.querySelector('.maxw-label');
    if (mwLabel) mwLabel.textContent = '';
  }
  marcarAlterado();
});

document.getElementById('multilineCheck').addEventListener('change', function() {
  if (!selectedEl) return;
  if (this.checked) {
    selectedEl.classList.add('multiline');
    selectedEl.dataset.multiline = '1';
    // Auto-captura a largura atual do box
    const w = selectedEl.offsetWidth;
    selectedEl.style.width = w + 'px';
    selectedEl.dataset.maxWidth = w;
    document.getElementById('maxWidthInput').value = w;
    // Atualiza label maxw
    const mwLabel = selectedEl.querySelector('.maxw-label');
    if (mwLabel) mwLabel.textContent = 'max: ' + w + 'px';
    // Adiciona al\u00e7a se n\u00e3o existir
    if (!selectedEl.querySelector('.resize-handle')) {
      const handle = document.createElement('span');
      handle.className = 'resize-handle';
      handle.title = 'Arraste para redimensionar';
      selectedEl.appendChild(handle);
      initResizeHandle(handle);
    }
  } else {
    selectedEl.classList.remove('multiline');
    selectedEl.dataset.multiline = '0';
    selectedEl.style.width = '';
    delete selectedEl.dataset.maxWidth;
    document.getElementById('maxWidthInput').value = 0;
    const mwLabel = selectedEl.querySelector('.maxw-label');
    if (mwLabel) mwLabel.textContent = '';
    // Remove al\u00e7a
    const handle = selectedEl.querySelector('.resize-handle');
    if (handle) handle.remove();
  }
  marcarAlterado();
});

// ===================================================================
// MARQUEE SELECTION (arrastar no letter vazio)
// ===================================================================
let selRect = null;
let selStart = { x: 0, y: 0 };
let _selDragging = false;

letter.addEventListener('mousedown', (e) => {
  // Só inicia se clicou no letter vazio (não em item)
  if (e.target !== letter && !e.target.classList.contains('grid-overlay')) return;
  if (e.button !== 0) return;

  const rect = letter.getBoundingClientRect();
  const sx = letter.offsetWidth / rect.width;
  const sy = letter.offsetHeight / rect.height;

  selStart.x = (e.clientX - rect.left) * sx;
  selStart.y = (e.clientY - rect.top) * sy;
  _selDragging = false;

  // Cria o retângulo visual
  if (!selRect) {
    selRect = document.createElement('div');
    selRect.className = 'sel-rect';
    letter.appendChild(selRect);
  }
  selRect.style.left = selStart.x + 'px';
  selRect.style.top = selStart.y + 'px';
  selRect.style.width = '0px';
  selRect.style.height = '0px';
  selRect.style.display = 'block';

  const onMove = (ev) => {
    const r = letter.getBoundingClientRect();
    const scX = letter.offsetWidth / r.width;
    const scY = letter.offsetHeight / r.height;
    const cx = (ev.clientX - r.left) * scX;
    const cy = (ev.clientY - r.top) * scY;
    const dx = cx - selStart.x;
    const dy = cy - selStart.y;

    if (Math.abs(dx) > 3 || Math.abs(dy) > 3) _selDragging = true;

    if (_selDragging) {
      const l = dx >= 0 ? selStart.x : cx;
      const t = dy >= 0 ? selStart.y : cy;
      selRect.style.left = l + 'px';
      selRect.style.top = t + 'px';
      selRect.style.width = Math.abs(dx) + 'px';
      selRect.style.height = Math.abs(dy) + 'px';
    }
  };

  const onUp = () => {
    document.removeEventListener('mousemove', onMove);
    document.removeEventListener('mouseup', onUp);
    selRect.style.display = 'none';

    if (_selDragging) {
      // Seleciona itens dentro do retângulo
      clearMultiSelect();

      const l = parseFloat(selRect.style.left);
      const t = parseFloat(selRect.style.top);
      const w = parseFloat(selRect.style.width);
      const h = parseFloat(selRect.style.height);

      document.querySelectorAll('.placed-item').forEach(el => {
        const elL = parseFloat(el.style.left);
        const elT = parseFloat(el.style.top);
        const elW = el.offsetWidth;
        const elH = el.offsetHeight;

        // Testa intersecção retangular
        if (elL < l + w && elL + elW > l && elT < t + h && elT + elH > t) {
          multiSelected.add(el);
          el.classList.add('multi-sel');
        }
      });

      if (multiSelected.size > 0) {
        selecionarItem(null);
        document.getElementById('propsPanel').classList.add('show');
        atualizarPropsPanel();
        atualizarStatus();
      } else {
        deselectItem();
      }
    } else {
      // Click simples no letter vazio → deselect
      deselectItem();
    }
    _selDragging = false;
  };

  document.addEventListener('mousemove', onMove);
  document.addEventListener('mouseup', onUp);
});

// ===================================================================
// KEYBOARD
// ===================================================================
document.addEventListener('keydown', (e) => {
  if (e.key === 'Delete' || e.key === 'Backspace') {
    if (selectedEl && document.activeElement?.tagName !== 'INPUT') {
      const el = selectedEl; deselectItem(); el.remove();
      iniciarSidebar(); atualizarStatus(); marcarAlterado();
    }
  }
  if (e.key === 'Escape') {
    if (selectedEl) deselectItem();
  }
  if ((selectedEl || multiSelected.size > 0) && ['ArrowUp','ArrowDown','ArrowLeft','ArrowRight'].includes(e.key)) {
    e.preventDefault();
    const step = e.shiftKey ? 10 : 1;
    // Reúne todos os elementos a mover: multi-selecionados + selecionado individual
    const elementos = multiSelected.size > 0 ? [...multiSelected] : (selectedEl ? [selectedEl] : []);
    elementos.forEach(el => {
      let l = parseFloat(el.style.left) || 0;
      let t = parseFloat(el.style.top) || 0;
      if (e.key === 'ArrowLeft') el.style.left = Math.max(0, l - step) + 'px';
      if (e.key === 'ArrowRight') el.style.left = (l + step) + 'px';
      if (e.key === 'ArrowUp') el.style.top = Math.max(0, t - step) + 'px';
      if (e.key === 'ArrowDown') el.style.top = (t + step) + 'px';
      const coord = el.querySelector('.coord-label');
      if (coord) coord.textContent = `${Math.round(parseFloat(el.style.left))}px, ${Math.round(parseFloat(el.style.top))}px`;
    });
    atualizarStatus(); marcarAlterado();
  }
  // Ctrl+S ou ⌘+S
  if ((e.ctrlKey || e.metaKey) && e.key === 's') {
    e.preventDefault();
    salvar();
  }
});

// ===================================================================
// TOGGLE GRID
// ===================================================================
function toggleGrid() {
  const letter = document.getElementById('letter');
  const btn = document.getElementById('gridBtn');
  letter.classList.toggle('show-grid');
  btn.classList.toggle('active');
  const on = letter.classList.contains('show-grid');
  mostrarToast(on ? 'Grade ativada' : 'Grade desativada');
}

function toggleBorders() {
  const letter = document.getElementById('letter');
  const btn = document.getElementById('borderBtn');
  letter.classList.toggle('show-borders');
  btn.classList.toggle('active');
  const on = letter.classList.contains('show-borders');
  mostrarToast(on ? 'Bordas visíveis' : 'Bordas ocultas');
}

// ===================================================================
// TOAST
// ===================================================================
function mostrarToast(msg) {
  const el = document.getElementById('toast');
  el.textContent = msg;
  el.classList.add('show');
  clearTimeout(el._timer);
  el._timer = setTimeout(() => el.classList.remove('show'), 2000);
}

// ===================================================================
// PERGUNTAR ANTES DE SAIR
// ===================================================================
window.addEventListener('beforeunload', (e) => {
  if (alterado) { e.preventDefault(); e.returnValue = ''; }
});

// ===================================================================
// INICIAR
// ===================================================================
aplicarZoom();
iniciarSidebar();
carregarSalvo();
</script>
</body>
</html>
