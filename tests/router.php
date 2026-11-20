<?php
$fields = $_SERVER['REQUEST_METHOD'] === 'GET' ? $_GET : $_POST;
ksort($fields);
if ($_SERVER['HTTP_LOGIN'] !== 'example' ||
    $_SERVER['HTTP_SIG'] !== md5(implode('', $fields).$_SERVER['HTTP_TS'].'example-key')) {
    http_response_code(403);
    echo json_encode(['error_message' => 'Invalid signature']);
    return;
}
header('Content-Type: application/json');
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === '/message' && isset($_POST['text']) && $_POST['text'] === 'reject') {
    http_response_code(420);
    echo json_encode(['success' => false, 'error_message' => 'Insufficient funds']);
} else {
    echo json_encode(['success' => true, 'path' => $path, 'fields' => $fields]);
}
