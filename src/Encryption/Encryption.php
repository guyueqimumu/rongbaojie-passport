<?php

namespace Passport\Client\Encryption;

/**
 * 对称加密算法
 * Author:Robert
 *
 * Class Encryption
 */
class Encryption
{

    /**
     * 默认加密方式
     */
    const  DEFAULT_CIPHER = 'des-ede3-cbc';
    //    const  DEFAULT_CIPHER = 'AES-256-CFB';

    /**
     * @var
     */
    public $cipher;
    /**
     * 向量
     * @var
     */
    public $iv;
    /**
     * 向量长
     * @var
     */
    public $ivSize;

    /**
     * Encryption constructor.
     * @param array $options
     * @throws \Exception
     */
    public function __construct(array $options = [])
    {
        if (isset($options['cipher'])) {
            $this->cipher = $options['cipher'];
        }
        $this->cipher = $this->cipher ? $this->cipher : self::DEFAULT_CIPHER;
        $ciphers = openssl_get_cipher_methods(false);
        if (!in_array($this->cipher, $ciphers)) {
            throw new \Exception('不支持的加密算法');
        }
        $this->ivSize = openssl_cipher_iv_length($this->cipher);
    }

    /**
     * 计算向量
     * @return string
     * @throws \Exception
     * @author Robert
     *
     */
    public function makeIv()
    {
        $this->iv = openssl_random_pseudo_bytes($this->ivSize); //随机生成向量
        return $this->iv;
    }

    /**
     * 将加密后的字符串以及向量一并返回
     * Author:Robert
     *
     * @param $data
     * @param string $encryptKey
     * @return string
     * @throws \Exception
     */
    public function encode($data, $encryptKey = '')
    {
        $iv = $this->makeIv();
        $encryptedData = openssl_encrypt($data, $this->cipher, $encryptKey, OPENSSL_RAW_DATA, $iv);
        return $iv . $encryptedData;
    }

    /**
     * 从加密串中分离向量
     * @param $encryptedData
     * @return string
     * @author Robert
     *
     */
    public function parseIv($encryptedData)
    {

        $this->iv = substr($encryptedData, 0, $this->ivSize);
        return $this->iv;
    }

    /**
     * Author:Robert
     *
     * @param $encryptedData
     * @param string $encryptKey
     * @return false|string
     */
    public function decode($encryptedData, $encryptKey = '')
    {
        return openssl_decrypt(substr($encryptedData, $this->ivSize), $this->cipher, $encryptKey, OPENSSL_RAW_DATA, $this->parseIv($encryptedData));
    }
}
