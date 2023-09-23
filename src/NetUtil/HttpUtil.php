<?php
/**
 * Created by api
 * User: Young
 * Date: 2021/02/09
 * Time: 14:37
 */

namespace app\helpers;

use Exception;

class HttpHelper
{
    public static $connectTimeout = 30;//30 second
    public static $readTimeout = 80;//80 second

    public static function get(string $url, array $headers = [])
    {
        return static::curl($url, 'GET', null, $headers);
    }

    public static function curl(string $url, string $httpMethod = "GET", $postFields = null, $headers = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $httpMethod);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($postFields) ? self::getQueryString($postFields) : $postFields);

        if (self::$readTimeout) {
            curl_setopt($ch, CURLOPT_TIMEOUT, self::$readTimeout);
        }
        if (self::$connectTimeout) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$connectTimeout);
        }
        //https request
        if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == "https") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (is_array($headers) && 0 < count($headers)) {
            $httpHeaders = self::getHttpHeaders($headers);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
        }
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $message = "Server unreachable: Errno: " . curl_errno($ch) . " " . curl_error($ch);
            qh_log_message('error', $message, 'curl');
            throw new Exception($message);
        }
        curl_close($ch);
        return $result;
    }

    public static function getQueryString(array $postFields): string
    {
        $content = '';
        foreach ($postFields as $apiParamKey => $apiParamValue) {
            $content .= "$apiParamKey=" . urlencode($apiParamValue) . '&';
        }
        return substr($content, 0, -1);
    }

    public static function getHttpHeaders($headers): array
    {
        $httpHeader = [];
        foreach ($headers as $key => $value) {
            array_push($httpHeader, $key . ":" . $value);
        }
        return $httpHeader;
    }
}
