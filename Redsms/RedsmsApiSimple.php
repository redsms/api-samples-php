<?php

namespace Redsms;

class RedsmsApiSimple
{
    const SMS_TYPE = 'sms';

    const VIBER_TYPE = 'viber';

    const RESEND_TYPE = 'viber,sms';

    protected $login;
    protected $apiKey;

    private $apiUrl = 'https://cp.redsms.ru/api';

    public function __construct($login, $apiKey, $apiUrl = null)
    {
        $this->login = $login;
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl ? $apiUrl : $this->apiUrl;
    }

    public function clientInfo()
    {
        $methodUrl = 'client/info';

        return $this->sendGet($methodUrl);
    }

    public function deleteFile($idFile)
    {
        $methodUrl = 'storage/'.$idFile;

        return $this->sendDelete($methodUrl);
    }

    public function fileInfo()
    {
        $methodUrl = 'storage';

        return $this->sendGet($methodUrl);
    }

    public function uploadFile($fileNAME)
    {
        $methodUrl = 'storage';

        return $this->postFile($methodUrl, $fileNAME);
    }

    public function sendSMS($to, $text, $from, $route = RedsmsApiSimple::SMS_TYPE)
    {
        $methodUrl = 'message';
        $to = is_array($to) ? $to : [$to];

        $data = [
            'to' => implode(',', $to),
            'text' => $text,
            'from' => $from,
            'route' => $route,
        ];

        return $this->sendPost($methodUrl, $data);
    }

    public function sendViber($to, $text, $from, $btnText, $btnUrl, $imageUrl)
    {
        $methodUrl = 'message';

        $to = is_array($to) ? $to : [$to];
        $data = [
            'to' => implode(',', $to),
            'text' => $text,
            'from' => $from,
            'route' => RedsmsApiSimple::VIBER_TYPE,
            'viber.btnText' => $btnText,
            'viber.btnUrl' => $btnUrl,
            'viber.imageUrl' => $imageUrl,
        ];

        return $this->sendPost($methodUrl, $data);
    }

    public function sendMessage($data)
    {
        $methodUrl = 'message';

        return $this->sendPost($methodUrl, $data);
    }

    public function messageInfo($uuid)
    {
        $methodUrl = 'message/'.$uuid;

        return $this->sendGet($methodUrl);
    }

    public function senderNameList($data = [])
    {
        $methodUrl = 'sender-name';

        return $this->sendGet($methodUrl, $data);
    }

    public function getSenderName($id, $data = [])
    {
        $methodUrl = 'sender-name/'.$id;

        return $this->sendGet($methodUrl, $data);
    }

    protected function sendGet($url, $data = [])
    {
        $curlResource = curl_init();
        $vars = http_build_query($data, '', '&');
        curl_setopt($curlResource, CURLOPT_URL, $this->apiUrl."/".$url."?$vars");
        curl_setopt($curlResource, CURLOPT_HTTPHEADER, $this->getHeaders($data));
        curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, 1);

        return $this->getCurlResult($curlResource);
    }

    protected function sendPost($url, $data = [])
    {
        $curlResource = curl_init();
        curl_setopt($curlResource, CURLOPT_URL, $this->apiUrl."/".$url);
        curl_setopt($curlResource, CURLOPT_POST, 1);
        curl_setopt($curlResource, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curlResource, CURLOPT_HTTPHEADER, $this->getHeaders($data));
        curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, 1);

        return $this->getCurlResult($curlResource);
    }

    protected function sendDelete($url)
    {
        $curlResource = curl_init();
        curl_setopt($curlResource, CURLOPT_URL, $this->apiUrl."/".$url);
        curl_setopt($curlResource, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($curlResource, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, true);

        return $this->getCurlResult($curlResource);
    }

    protected function postFile($url, $name)
    {
        $curlResource = curl_init();
        curl_setopt($curlResource, CURLOPT_URL, $this->apiUrl."/".$url);
        curl_setopt($curlResource, CURLOPT_POST, true);
        curl_setopt($curlResource, CURLOPT_POSTFIELDS, ['file' => new \CURLFile($name)]);
        curl_setopt($curlResource, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, true);

        return $this->getCurlResult($curlResource);
    }

    protected function getCurlResult($curlResource)
    {
        $response = curl_exec($curlResource);
        $info = curl_getinfo($curlResource);
        curl_close($curlResource);
        $responseArray = json_decode($response, true);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Exception('Error response format', $info['http_code']);
        }

        if ($info['http_code'] != 200) {
            throw new \Exception($responseArray['error_message'], $info['http_code']);
        }

        return $responseArray;
    }

    protected function getHeaders($data = [])
    {
        ksort($data);
        reset($data);
        $ts = microtime().rand(0, 10000);

        return [
            'login: '.$this->login,
            'ts: '.$ts,
            'sig: '.md5(implode('', $data).$ts.$this->apiKey),
        ];
    }
}