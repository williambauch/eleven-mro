<?php
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['page']) || !isset($input['items'])) {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
    exit;
}

$page = (int)$input['page'];
if ($page < 1 || $page > 5) {
    echo json_encode(['success' => false, 'error' => 'Página inválida']);
    exit;
}

$dataDir = __DIR__ . '/../assets/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

$savePath = $dataDir . '/page' . $page . '.json';

$data = [
    'page' => $page,
    'savedAt' => date('Y-m-d H:i:s'),
    'items' => $input['items'],
    'customItems' => $input['customItems'] ?? []
];

$result = file_put_contents($savePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

if ($result === false) {
    echo json_encode(['success' => false, 'error' => 'Erro ao escrever arquivo']);
    exit;
}

echo json_encode(['success' => true, 'page' => $page, 'count' => count($input['items'])]);
