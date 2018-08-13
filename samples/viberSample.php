<?php

require __DIR__.'/../Redsms/RedsmsApiSimple.php';

$config = include __DIR__.'/../config.php';

echo 'REDSMS.RU api test viber'.PHP_EOL;

$login = $config['login'];
$apiKey = $config['apiKey'];

$testNumber = $config['phone'];
$viberSenderName = $config['viberSenderName'];

$redsmsApi = new \Redsms\RedsmsApiSimple($login, $apiKey);
$lastMessageUuid = '';
try {
    $path = __DIR__.'/../data/image/REDSMS.png';
    $files = $redsmsApi->uploadFile($path);

    $file = array_shift($files['items']);
    $buttonText = 'Кнопка';
    $buttonUrl = 'https://cp.redsms.ru/';
    $textViber = 'Тестовое сообщение';

    if(isset($file)) {
        $imageUrl = $file['url'];
    } else {
        $imageUrl = '';
    }

    $lastMessageUuid = '';
    $data = [
        'to' => $testNumber,
        'text' => $textViber,
        'from' => $viberSenderName,
        'route' => \Redsms\RedsmsApiSimple::VIBER_TYPE,
        'viber.btnText' => $buttonText,
        'viber.btnUrl' => $buttonUrl,
        'viber.imageUrl' => $imageUrl,
    ];
    $sendResult = $redsmsApi->sendMessage($data);

    if (!empty($sendResult['items']) && $messages = $sendResult['items'] ) {
        foreach ($messages as $message) {
            echo $message['to'].':'.$message['uuid'].PHP_EOL;
            $lastMessageUuid = $message['uuid'];
        }
    }
    if ($lastMessageUuid) {
        echo 'Get message info: ';
        echo json_encode($redsmsApi->messageInfo($lastMessageUuid)).PHP_EOL;
        echo 'wait 10 sec... ';
        sleep(10);
        echo json_encode($redsmsApi->messageInfo($lastMessageUuid)).PHP_EOL;
    }
} catch (\Exception $e) {
    echo 'error code: '.$e->getCode().PHP_EOL;
    echo 'error message: '.$e->getMessage().PHP_EOL;
}

echo 'complete.'.PHP_EOL;