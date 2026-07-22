<?php
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['page']) || !isset($input['image'])) {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
    exit;
}

$page = (int)$input['page'];
$image = preg_replace('/[^a-zA-Z0-9_.-]/', '', $input['image']);

if ($page < 1 || $page > 5) {
    echo json_encode(['success' => false, 'error' => 'Página inválida']);
    exit;
}

// Valida se a imagem existe
$imgPath = __DIR__ . '/../assets/images/' . $image;
if (!file_exists($imgPath)) {
    echo json_encode(['success' => false, 'error' => 'Imagem não encontrada: ' . $image]);
    exit;
}

// Salva no pages_data.json
$dataPath = __DIR__ . '/../pages_data.json';
$data = [];
if (file_exists($dataPath)) {
    $data = json_decode(file_get_contents($dataPath), true);
    if (!is_array($data)) $data = [];
}

$data[$page]['image'] = $image;

file_put_contents($dataPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode(['success' => true, 'page' => $page, 'image' => $image]);
