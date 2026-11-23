<?php

require __DIR__.'/../Redsms/RedsmsApiSimple.php';

if ($argc !== 2 || !preg_match('/\A[0-9a-f]{8}(?:-[0-9a-f]{4}){3}-[0-9a-f]{12}\z/i', $argv[1])) {
    fwrite(STDERR, "Использование: php samples/messageStatusSample.php UUID".PHP_EOL);
    exit(1);
}

$config = require __DIR__.'/../config.php';
$api = new \Redsms\RedsmsApiSimple(
    $config['login'],
    $config['apiKey'],
    isset($config['apiUrl']) ? $config['apiUrl'] : null
);

try {
    $result = $api->messageInfo($argv[1], ['uuid', 'status', 'errCode']);
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT).PHP_EOL;
} catch (Exception $e) {
    fwrite(STDERR, 'Ошибка '.$e->getCode().': '.$e->getMessage().PHP_EOL);
    exit(1);
}
