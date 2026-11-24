<?php

require __DIR__.'/../Redsms/RedsmsApiSimple.php';

$config = include __DIR__.'/../config.php';

echo 'REDSMS.RU sms send test '.PHP_EOL;

$login = $config['login'];
$apiKey = $config['apiKey'];

$redsmsApi = new \Redsms\RedsmsApiSimple($login, $apiKey, isset($config['apiUrl']) ? $config['apiUrl'] : null);

$testNumber = isset($config['phone']) ? $config['phone'] : '';
if (!is_string($testNumber) || !preg_match('/\A[+]?[1-9][0-9]{1,14}\z/', $testNumber)) {
    fwrite(STDERR, 'Укажите один номер в международном формате, например +12025550123.'.PHP_EOL);
    exit(1);
}
$smsSenderName = $config['smsSenderName'];

try {
    echo 'Send sms message: '.PHP_EOL;
    $sendResult = $redsmsApi->sendSMS($testNumber, 'Тестовое сообщение от redsms.ru', $smsSenderName);
    $failed = !empty($sendResult['errors']);
    if ($failed) {
        fwrite(STDERR, json_encode($sendResult['errors'], JSON_UNESCAPED_UNICODE).PHP_EOL);
    }
    if (empty($sendResult['items'])) {
        throw new RuntimeException('No accepted messages');
    }
    foreach ($sendResult['items'] as $message) {
        if (empty($message['uuid'])) {
            throw new RuntimeException('Missing message UUID');
        }
        echo $message['to'].': '.$message['uuid'].PHP_EOL;
    }
    if ($failed) {
        exit(1);
    }
} catch (Exception $e) {
    fwrite(STDERR, 'Ошибка '.$e->getCode().': '.$e->getMessage().PHP_EOL);
    exit(1);
}
