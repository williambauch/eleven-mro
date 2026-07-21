<?php
header('Content-Type: application/json; charset=utf-8');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1 || $page > 5) {
    echo json_encode(['saved' => false, 'error' => 'Página inválida']);
    exit;
}

$savePath = __DIR__ . '/../assets/data/page' . $page . '.json';

if (!file_exists($savePath)) {
    echo json_encode(['saved' => false, 'items' => []]);
    exit;
}

$data = json_decode(file_get_contents($savePath), true);

if (!$data || !isset($data['items'])) {
    echo json_encode(['saved' => false, 'items' => []]);
    exit;
}

echo json_encode([
    'saved' => true,
    'page' => $page,
    'savedAt' => $data['savedAt'] ?? null,
    'items' => $data['items'],
    'customItems' => $data['customItems'] ?? []
]);
