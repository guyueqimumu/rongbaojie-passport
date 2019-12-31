<?php

namespace Passport\Client\Http;

/**
 * Created by QiLin.
 * User: NO.01
 * Date: 2019/12/30
 * Time: 17:16
 */
class Request
{

    public $requestInfo;

    public $logFile;

    protected $httpTimeout = 60;

    protected $connectTimeout = 8;

    public function __construct()
    {
        $this->logFile = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Log/request.log';
    }

    public function debug()
    {
        return $this->requestInfo;
    }

    public function curl($url, string $postBodyString, $method = 'POST')
    {
        try {
            if (in_array($method, ['GET']) && $postBodyString) {
                $url .= '?' . $postBodyString;
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FAILONERROR, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//
            //        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);//
            if ($this->httpTimeout) {
                curl_setopt($ch, CURLOPT_TIMEOUT, $this->httpTimeout);
            }
            if ($this->connectTimeout) {
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
            }
            curl_setopt($ch, CURLOPT_USERAGENT, "X.Y R&D Apollo Program");
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
            switch ($method) {
                case "GET":
                    curl_setopt($ch, CURLOPT_HTTPGET, true);
                    break;
                case "POST":
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postBodyString);
                    $header = array("content-type: application/x-www-form-urlencoded; charset=UTF-8");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    break;
                case "PUT":
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postBodyString);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                    break;
                case "DELETE":
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postBodyString);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                    break;
            }
            $response = curl_exec($ch);
            $this->requestInfo = curl_getinfo($ch);
            if ($this->requestInfo) {
                $this->requestInfo['body'] = $postBodyString;
            }
            if ($this->logFile) {
                $this->writeLog('REQUEST' . json_encode($this->requestInfo) . PHP_EOL . 'URL:' . $url . PHP_EOL . 'RESPONSE:' . $response);
            }
            if (curl_errno($ch)) {
                throw new \Exception(curl_error($ch), 0);
            } else {
                $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if (200 !== $httpStatusCode) {
                    throw new \Exception($response, $httpStatusCode);
                }
            }
            curl_close($ch);
            return $response;
        } catch (\Exception $exception) {
            $this->writeLog('Exception Error Msg:' . $exception->getMessage() . PHP_EOL . 'URL:' . $url);
        }
    }

    public function setLogPath()
    {
        echo dirname(__DIR__);
    }

    public function writeLog($msg)
    {
        $folder = dirname($this->logFile);
        if (!is_dir($folder)) {
            mkdir($folder, 0777);
        }
        error_log('[' . date('Y-m-d  H:i:s') . ']' . $msg . PHP_EOL, 3, $this->logFile);
    }

}