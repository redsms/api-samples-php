<?php
require __DIR__.'/../Redsms/RedsmsApiSimple.php';

$api = new \Redsms\RedsmsApiSimple('example', 'example-key', 'http://127.0.0.1:8087');
$info = $api->clientInfo();
$list = $api->senderNameList(['limit' => '2', 'fields' => 'updated']);
$sms = $api->sendSMS('70000000000', 'Тестовое сообщение от redsms.ru', 'example');
$status = $api->messageInfo('example-id');
if ($info['path'] !== '/client/info' || $list['fields']['limit'] !== '2' ||
    $sms['path'] !== '/message' || $sms['fields']['text'] !== 'Тестовое сообщение от redsms.ru' ||
    $status['path'] !== '/message/example-id') {
    throw new RuntimeException('Unexpected request');
}
try {
    $api->sendSMS('70000000000', 'reject', 'example');
} catch (Exception $e) {
    if ($e->getCode() === 420 && $e->getMessage() === 'Insufficient funds') {
        exit(0);
    }
    throw $e;
}
throw new RuntimeException('Expected API error');
