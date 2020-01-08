<?php
/**
 * Created by QiLin.
 * User: NO.01
 * Date: 2019/12/30
 * Time: 18:13
 */

namespace Passport\Demo;

use Passport\Client\Encryption\Encryption;
use Passport\Client\Handle;
use Passport\Client\Http\Request;


class Index
{
    public $passportDomain = 'http://api.hql.passport.xy.cn';
    public $openInsuranceDomain = 'http://api.insurance.open.platform.xy.cn/';
    public $appId = '10000';
    public $appSecret = '4b797e878798db8353bdbcc9a631398c';
    public $testAccount = 'administrator_1';
    public $testPassword = 'hxy_123456';
    public $request = '';

    public function __construct()
    {
        $this->request = new Request();
    }

    public function login()
    {
        $loginParam = [
            'password' => $this->testPassword,
            'mobile' => $this->testAccount,
        ];
        $loginParam = http_build_query($loginParam);
        $url = $this->passportDomain . '/userSession';
        $loginRes = $this->request->curl($url, $loginParam);
        $loginRes = json_decode($loginRes, true);
        if ($loginRes['code'] != '200') {
            die($loginRes['message']);
        }
        return $loginRes;
    }

    public function get($ticket, $siteId = '1')
    {
        //url 地址用于测试
        $url = $this->passportDomain . "/Tests/ThirdPartSite/authorize/{$siteId}";
        $this->request->setHeader("Access-Ticket: {$ticket}");
        $authorizeRes = $this->request->curl($url, '', "GET");
        $authorizeRes = json_decode($authorizeRes, true);
        if ($authorizeRes['code'] != '200') {
            die($authorizeRes['message']);
        }
        return $authorizeRes;
    }

    public function encode($authorizeRes)
    {
        $parseUrlInfo = parse_url($authorizeRes['data']['redirect']);
        parse_str($parseUrlInfo['query'], $data);
        $encryption = new Encryption();
        $res = $encryption->decode(base64_decode($data['setCookie']));
        parse_str($res, $data);
        return $data;
    }

    public function getUserInfo($ticket)
    {
        $handle = new Handle([
            'appId' => $this->appId,
            'appSecret' => $this->appSecret,
        ]);
        $url = $this->openInsuranceDomain . "/Session/getUserInfo";
        return $handle->getUserInfo($url, $ticket);
    }

    public static function execute()
    {
        $demo = new self();
        echo "=========登录========\r\n";
        $loginRes = $demo->login();
        var_dump($loginRes);
        echo "=========获取加密信息========\r\n";
        $authorizeRes = $demo->get($loginRes['data']['ticket']);
        var_dump($authorizeRes);
        echo "=========解析加密信息========\r\n";
        $data = $demo->encode($authorizeRes);
        var_dump($data);
        echo "=========验证ticket========\r\n";
        $res = $demo->getUserInfo($data['ticket']);
        var_dump($res);
    }
}