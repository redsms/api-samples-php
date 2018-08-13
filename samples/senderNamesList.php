<?php

require __DIR__.'/../Redsms/RedsmsApiSimple.php';

$config = include __DIR__.'/../config.php';

echo 'REDSMS.RU senderNames list'.PHP_EOL;

$login = $config['login'];
$apiKey = $config['apiKey'];
$redsmsApi = new \Redsms\RedsmsApiSimple($login, $apiKey);

try {
    $data = [
        'limit' => '100',
        'fields' => "updated",
    ];

    $result = $redsmsApi->senderNameList($data);
    $senderNames = $result['items'];
    echo "available name count ".count($senderNames).PHP_EOL;
    foreach ($senderNames as $name) {
        echo json_encode($name).PHP_EOL;
    }
} catch (\Exception $e) {
    echo 'error code: '.$e->getCode().PHP_EOL;
    echo 'error message: '.$e->getMessage().PHP_EOL;
}