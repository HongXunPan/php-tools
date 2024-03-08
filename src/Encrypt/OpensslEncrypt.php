<?php

namespace HongXunPan\Tools\Encrypt;

/**
 *
 * @method static bool encrypt(string $str, string $algo)
 * @method static bool decrypt($data, $algo)
 */
class OpensslEncrypt
{
    protected $secretKey = 'OpHokKZ7oN';
    protected $iv = 'YEl8QAkf3QTDhVcs';

    const AES_256_CBC = 'aes-256-cbc';
    const AES_128_CBC = 'aes-128-cbc';

    public static $algo = [
        "aes-128-cbc", "aes-128-cbc-hmac-sha1", "aes-128-cbc-hmac-sha256", "aes-128-ccm", "aes-128-cfb", "aes-128-cfb1", "aes-128-cfb8", "aes-128-ctr",
        "aes-128-ecb", "aes-128-gcm", "aes-128-ocb", "aes-128-ofb", "aes-128-xts",
        "aes-192-cbc", "aes-192-ccm", "aes-192-cfb", "aes-192-cfb1", "aes-192-cfb8", "aes-192-ctr",
        "aes-192-ecb", "aes-192-gcm", "aes-192-ocb", "aes-192-ofb",
        "aes-256-cbc", "aes-256-cbc-hmac-sha1", "aes-256-cbc-hmac-sha256", "aes-256-ccm", "aes-256-cfb", "aes-256-cfb1", "aes-256-cfb8", "aes-256-ctr",
        "aes-256-ecb", "aes-256-gcm", "aes-256-ocb", "aes-256-ofb", "aes-256-xts",
        "aria-128-cbc", "aria-128-ccm", "aria-128-cfb", "aria-128-cfb1", "aria-128-cfb8", "aria-128-ctr",
        "aria-128-ecb", "aria-128-gcm", "aria-128-ofb", "aria-192-cbc", "aria-192-ccm", "aria-192-cfb", "aria-192-cfb1", "aria-192-cfb8", "aria-192-ctr",
        "aria-192-ecb", "aria-192-gcm", "aria-192-ofb",
        "aria-256-cbc", "aria-256-ccm", "aria-256-cfb", "aria-256-cfb1", "aria-256-cfb8", "aria-256-ctr",
        "aria-256-ecb", "aria-256-gcm", "aria-256-ofb",
        "bf-cbc", "bf-cfb", "bf-ecb", "bf-ofb",
        "camellia-128-cbc", "camellia-128-cfb", "camellia-128-cfb1", "camellia-128-cfb8", "camellia-128-ctr", "camellia-128-ecb", "camellia-128-ofb",
        "camellia-192-cbc", "camellia-192-cfb", "camellia-192-cfb1", "camellia-192-cfb8", "camellia-192-ctr", "camellia-192-ecb", "camellia-192-ofb",
        "camellia-256-cbc", "camellia-256-cfb", "camellia-256-cfb1", "camellia-256-cfb8", "camellia-256-ctr", "camellia-256-ecb", "camellia-256-ofb",
        "cast5-cbc", "cast5-cfb", "cast5-ecb", "cast5-ofb", "chacha20", "chacha20-poly1305",
        "des-cbc", "des-cfb", "des-cfb1", "des-cfb8", "des-ecb", "des-ede", "des-ede-cbc", "des-ede-cfb", "des-ede-ofb",
        "des-ede3", "des-ede3-cbc", "des-ede3-cfb", "des-ede3-cfb1", "des-ede3-cfb8", "des-ede3-ofb", "des-ofb", "desx-cbc",
        "id-aes128-CCM", "id-aes128-GCM", "id-aes128-wrap", "id-aes128-wrap-pad",
        "id-aes192-CCM", "id-aes192-GCM", "id-aes192-wrap", "id-aes192-wrap-pad",
        "id-aes256-CCM", "id-aes256-GCM", "id-aes256-wrap", "id-aes256-wrap-pad", "id-smime-alg-CMS3DESwrap",
        "rc2-40-cbc", "rc2-64-cbc", "rc2-cbc", "rc2-cfb", "rc2-ecb", "rc2-ofb",
        "rc4", "rc4-40", "rc4-hmac-md5"
    ];

    /**
     * @param string $secretKey
     * @param string $iv
     */
    public function __construct($secretKey = '', $iv = '')
    {
        !empty($secretKey) && $this->secretKey = $secretKey;
        !empty($iv) && $this->iv = $iv;
    }

    public static function __callStatic($name, $arguments)
    {
        $static = new static();
        if (method_exists($static, $name)) {
            return $static->$name(...$arguments);
        }
        throw new EncryptException('method no exists:' . $name);
    }

    /**
     * @param string $secretKey
     * @param string $iv
     * @return static
     */
    public static function setConfig($secretKey, $iv)
    {
        return new static($secretKey, $iv);
    }

    /**
     * @param string $str
     * @param string $algo
     * @return bool|string
     */
    public function encrypt($str, $algo = self::AES_256_CBC)
    {
        if (empty($str)) {
            return '';
        }
        if (!in_array($algo, self::$algo)) {
            return false;
        }
        return openssl_encrypt($str, $algo, $this->secretKey, 0, $this->iv);
    }

    //解密

    /**
     * @param $data
     * @param $algo
     * @return bool|string
     */
    public function decrypt($data, $algo = self::AES_256_CBC)
    {
        if (empty($data)) {
            return '';
        }
        if (!in_array($algo, self::$algo)) {
            return false;
        }
        return openssl_decrypt($data, $algo, $this->secretKey, 0, $this->iv);
    }
}