<?php

require 'Redsms/RedsmsApiSimple.php';

echo "redsms.ru api test viber \n";

$login = 'you_login';
$apiKey = 'you_api_key';
$testNumber = 'you_number';

$redsmsApi = new \Redsms\RedsmsApiSimple($login, $apiKey);
$lastMessageUuid = '';
try {

    echo "Client info: \n";
    print_r($redsmsApi->clientInfo());

    $path = "./data/image/REDSMS.png";
    $files = $redsmsApi->uploadFile($path);

    echo "Файлов отправлено" . count($files['items'])  . PHP_EOL;

    $file = array_shift($files['items']);
    $buttonText = "Кнопка";
    $buttonUrl = "https://cp.redsms.ru/";
    $textViber = "Тестовое сообщение";

    if(isset($file)) {
        $imageUrl = $file['url'];
    } else {
        $imageUrl = "";
    }

    $lastMessageUuid = '';
    $data = [
        'to' => $testNumber,
        'text' => $textViber,
        'from' => 'REDSMS.RU',
        'route' => \Redsms\RedsmsApiSimple::VIBER_TYPE,
        'viber.btnText' => $buttonText,
        'viber.btnUrl' => $buttonUrl,
        'viber.imageUrl' => $imageUrl,
    ];
    $sendResult = $redsmsApi->sendMessage($data);

    if (!empty($sendResult['items']) && $messages = $sendResult['items'] ) {
        foreach ($messages as $message) {
            echo $message['to'].":".$message['uuid']."\n";
            $lastMessageUuid = $message['uuid'];
        }
    }
    if ($lastMessageUuid) {
        echo "Get message info: \n";
        print_r($redsmsApi->messageInfo($lastMessageUuid));
        echo "wait 10 sec... \n";
        sleep(10);
        print_r($redsmsApi->messageInfo($lastMessageUuid));
    }
} catch (\Exception $e) {
    echo "error code: ".$e->getCode()."\n";
    echo "error message: ".$e->getMessage()."\n";
}

echo "complete.\n";
