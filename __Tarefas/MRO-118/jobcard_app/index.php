<?php
require_once __DIR__ . '/pages_config.php';
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Job Card Editor - App</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    background: #e5e5e5;
    padding: 30px;
  }
  .header {
    max-width: 900px;
    margin: 0 auto 30px;
    text-align: center;
  }
  .header h1 { font-size: 28px; color: #2c3e50; margin-bottom: 8px; }
  .header p { color: #7f8c8d; font-size: 14px; }

  .grid {
    max-width: 900px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
  }
  .page-card {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform .2s, box-shadow .2s;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
    display: block;
  }
  .page-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
  }
  .page-card .thumb {
    width: 100%;
    aspect-ratio: 612/792;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    background-color: #fff;
    border-bottom: 1px solid #eee;
  }
  .page-card .info {
    padding: 14px 16px;
  }
  .page-card .info h3 { font-size: 16px; color: #2c3e50; }
  .page-card .info .sub {
    font-size: 12px; color: #95a5a6; margin-top: 4px;
  }
  .page-card .info .status {
    display: inline-block;
    margin-top: 6px;
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 10px;
  }
  .status-saved { background: #d5f5e3; color: #27ae60; font-weight: 600; }
  .status-unsaved { background: #fdebd0; color: #e67e22; font-weight: 600; }
</style>
</head>
<body>

<div class="header">
  <h1>📋 Job Card Editor</h1>
  <p>Selecione uma página para editar o posicionamento dos textos</p>
</div>

<div class="grid" id="pageGrid">
  <?php foreach ($PAGES as $num => $page): 
    $saveFile = __DIR__ . '/assets/data/page' . $num . '.json';
    $saved = file_exists($saveFile);
  ?>
  <a class="page-card" href="editor.php?page=<?= $num ?>">
    <div class="thumb" style="background-image: url('assets/images/<?= htmlspecialchars($page['image']) ?>');"></div>
    <div class="info">
      <h3><?= htmlspecialchars($page['title']) ?></h3>
      <div class="sub"><?= count($page['items']) ?> itens</div>
      <div class="status <?= $saved ? 'status-saved' : 'status-unsaved' ?>">
        <?= $saved ? '✓ Salvo' : '⚬ Não editado' ?>
      </div>
    </div>
  </a>
  <?php endforeach; ?>
</div>

</body>
</html>
