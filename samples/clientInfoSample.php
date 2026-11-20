<?php

require __DIR__.'/../Redsms/RedsmsApiSimple.php';

$config = include __DIR__.'/../config.php';

echo 'REDSMS.RU client information'.PHP_EOL;

$login = $config['login'];
$apiKey = $config['apiKey'];

$redsmsApi = new \Redsms\RedsmsApiSimple($login, $apiKey);
echo 'Client info: '.PHP_EOL;
try {
    echo json_encode($redsmsApi->clientInfo());
} catch (\Exception $e) {
    echo 'error code: '.$e->getCode().PHP_EOL;
    fwrite(STDERR, 'Error: '.$e->getMessage().PHP_EOL);
    exit(1);
}
echo PHP_EOL;