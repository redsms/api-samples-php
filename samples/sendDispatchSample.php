<?php

require __DIR__.'/../Redsms/RedsmsApiSimple.php';

$config = include __DIR__.'/../config.php';

echo 'REDSMS.RU create dispatch '.PHP_EOL;

$login = $config['login'];
$apiKey = $config['apiKey'];

$redsmsApi = new \Redsms\RedsmsApiSimple($login, $apiKey, isset($config['apiUrl']) ? $config['apiUrl'] : null);

$smsSenderName = $config['smsSenderName'];
$data = [
    'route' => \Redsms\RedsmsApiSimple::SMS_TYPE,
    'from' => $smsSenderName,
    'to' => $config['phone'],
    'name' => 'Тестовая SMS-рассылка',
    'text' => 'Тестовое сообщение от redsms.ru',
    'validity' => \Redsms\RedsmsApiSimple::VALIDITY_PERIOD_MAX,
];

try {
    if ($response = $redsmsApi->createDispatch($data)) {
        $dispatch = $response['item'];
        $id = $dispatch['id'];
        $name = $dispatch['name'];
        $status = $dispatch['status'];
        if ($status == 'accept') {
            echo 'dispatch with id - '.$id.' started'.PHP_EOL;
        } else {
            echo 'status'.$status.PHP_EOL;
        }
    };
} catch (\Exception $e) {
    echo 'error code: '.$e->getCode().PHP_EOL;
    fwrite(STDERR, 'Error: '.$e->getMessage().PHP_EOL);
    exit(1);
}

echo 'complete.'.PHP_EOL;
