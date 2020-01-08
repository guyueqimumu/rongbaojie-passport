<?php

namespace Passport\Client;

use Passport\Client\Encryption\Encryption;
use Passport\Client\Http;

/**
 * Created by QiLin.
 * User: NO.01
 * Date: 2019/12/30
 * Time: 16:37
 */
class Handle
{

    public $errorMsg = '';

    public $errorCode = '200';

    public $appId = '10000';

    public $appSecret = '4b797e878798db8353bdbcc9a631398c';

    public function __construct(array $option)
    {
        if (!isset($option['appId']) or !isset($option['appSecret'])) {
            throw new \Exception('appId、appSecret 不能为空');
        }
        $this->appSecret = $option['appSecret'];
        $this->appId = $option['appId'];
    }

    public function getUserInfo($url, $ticket)
    {
        $postData = [
            'algorithm' => 'sha1',
            'appId' => $this->appId,
            'timestamp' => time(),
            'ticket' => $ticket,
            'noncer' => rand(1000, 99999)
        ];
        $postData['sign'] = $this->sign($postData);
        $postBodyString = http_build_query($postData);
        $postBodyString .= '&appSecret=' . $this->appSecret;
        $request = new Http\Request();
        return $request->curl($url, $postBodyString);
    }

    public function sign(array $data, $algorithm = 'sha1')
    {
        $sign = '';
        ksort($data);
        if ($algorithm == 'sha1') {
            $sign = sha1(http_build_query($data));
        }
        return $sign;
    }

    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    public function setAppSecret($appSecret)
    {
        $this->appSecret = $appSecret;
    }
}

