<?php

require __DIR__.'/../Redsms/RedsmsApiSimple.php';

$config = include __DIR__.'/../config.php';

echo 'REDSMS.RU sms send test '.PHP_EOL;

$login = $config['login'];
$apiKey = $config['apiKey'];

$redsmsApi = new \Redsms\RedsmsApiSimple($login, $apiKey, isset($config['apiUrl']) ? $config['apiUrl'] : null);

$testNumber = $config['phone'];
$smsSenderName = $config['smsSenderName'];

$pending = [];
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
        $pending[$message['uuid']] = true;
    }
    for ($attempt = 0; $attempt < 2 && $pending; ++$attempt) {
        if ($attempt) {
            sleep(10);
        }
        foreach (array_keys($pending) as $uuid) {
            $response = $redsmsApi->messageInfo($uuid, ['uuid', 'status', 'errCode']);
            if (empty($response['item']['status'])) {
                throw new RuntimeException($uuid.': missing status');
            }
            $item = $response['item'];
            echo json_encode($item, JSON_UNESCAPED_UNICODE).PHP_EOL;
            if (!in_array($item['status'], ['created', 'moderation', 'progress'], true)) {
                unset($pending[$uuid]);
                if ($item['status'] !== 'delivered') {
                    $failed = true;
                }
            }
        }
    }
    if ($pending) {
        echo 'Check these UUIDs again later: '.implode(', ', array_keys($pending)).PHP_EOL;
    }
    if ($failed) {
        exit(1);
    }
} catch (\Exception $e) {
    echo 'error code: '.$e->getCode().PHP_EOL;
    fwrite(STDERR, 'Error: '.$e->getMessage().PHP_EOL);
    exit(1);
}

echo 'complete.'.PHP_EOL;