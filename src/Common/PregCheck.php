<?php

namespace app\helpers;

/**
 * 正则，结果为：是 返回true
 * Class PregHelper
 *
 * @package app\helpers
 */
class PregHelper
{
    /**
     * 验证日期格式
     *
     * @param string|null $date string  日期 eg. 2018-05-08
     *
     * @return bool
     */
    public static function checkDateFormat(?string $date): bool
    {
        if (empty($date)) {
            return false;
        }
        //匹配日期格式
        $pattern = "/^([0-9]{4})[-\/]([0-9]{1,2})([-\/]([0-9]{1,2}))?\s*([0-9]{1,2}:[0-9]{1,2}(:[0-9]{1,2})?)?$/";
        if (preg_match($pattern, $date, $parts)) {
            //检测是否为日期
            if (isset($parts[4])) {
                if (checkdate($parts[2], $parts[4], $parts[1])) {
                    return true;
                }
            } else {
                return true;
            }
        }
        return false;
    }

    /**
     * 判断地区代码是否是片区代码
     *
     * @param string|null $areaCode
     *
     * @return bool
     */
    public static function isDistrict(?string $areaCode): bool
    {
        return self::common("/^(disrict|mdistrict).*?/", $areaCode);
    }

    private static function common(string $reg, $input): bool
    {
        if (empty($input)) {
            return false;
        }

        return preg_match($reg, $input);
    }

    /**
     * 判断地区代码是否是园区代码
     *
     * @param string|null $parkCode
     *
     * @return bool
     */
    public static function isPark(?string $parkCode): bool
    {
        return self::common("/^park.*?/", $parkCode);
    }

    /**
     * 手机号验证，不正确返回false
     *
     * @param $mobile
     *
     * @return bool
     */
    public static function isMobile($mobile): bool
    {
        return self::common('/^1[3456789]\d{9}$/', $mobile);
    }

    /**
     * 邮箱验证，不正确返回false
     *
     * @param string|null $email
     *
     * @return bool
     */
    public static function isEmail(?string $email): bool
    {
        $reg = '/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/';
        return self::common($reg, $email);
    }

    /**
     * 中文、英文和数字
     *
     * @param string|null $name
     *
     * @return bool
     */
    public static function isMixedName(?string $name): bool
    {
        return self::common('/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u', $name);
    }

    /**
     * 字母和数字
     *
     * @param string|null $name
     *
     * @return bool
     */
    public static function isAlNum(?string $name): bool
    {
        return self::common('/^[a-zA-Z0-9]+$/', $name);
    }

    /**
     * 是否包含特殊字符
     *
     * @param string|null $str
     *
     * @return bool
     */
    public static function hasSpecialCharacters(?string $str): bool
    {
        return self::common('/[\'\"%!@#\$%^&*()_+]+/', $str);
    }

    /**
     * 是否为组织机构代码(东莞企业报数，长度9或10位)
     *
     * @param string|null $orgCode
     *
     * @return bool
     */
    public static function isOrgCode(?string $orgCode): bool
    {
        if (9 == strlen($orgCode) || strlen($orgCode) == 10) {
            return true;
        }

        return false;
    }

    /**
     * 纯数字
     *
     * @param int|null $input
     *
     * @return bool
     */
    public static function isNumber(?int $input): bool
    {
        return self::common('/^\d+$/', $input);
    }

    /**
     * 两位小数
     *
     * @param int|null $input
     *
     * @return bool
     */
    public static function isDecimal2(?int $input): bool
    {
        return self::common('/^\d+(\.\d{0,2})$/', $input);
    }

    /**
     * 日期（格式：2019-01-01）
     *
     * @param int|null $input
     *
     * @return bool
     */
    public static function isDate(?int $input): bool
    {
        return self::common('/^\d{4}-\d{2}-\d{2}$/', $input);
    }

    /**
     * 中文
     *
     * @param string|null $input
     *
     * @return bool
     */
    public static function isChinese(?string $input): bool
    {
        return self::common('/^[\x{4e00}-\x{9fa5}]+$/u', $input);
    }

    /**
     * 英文字母
     *
     * @param string|null $input
     *
     * @return bool
     */
    public static function isEnglish(?string $input): bool
    {
        return self::common('/^[a-zA-Z]+$/', $input);
    }

    /**
     * url
     *
     * @param string|null $input
     *
     * @return bool
     */
    public static function isUrl(?string $input): bool
    {
        return self::common('/^(https?|ftp|file):\/\/[-A-Za-z0-9+&@#/%?=~_|!:,.;]+[-A-Za-z0-9+&@#/%=~_|]$/', $input);
    }

    /**
     * 身份证号
     *
     * @param string|null $input
     *
     * @return bool
     */
    public static function isIdCard(?string $input): bool
    {
        return IdCard::validateIDCard($input);
    }

    /**
     * QQ号
     *
     * @param string|null $input
     *
     * @return bool
     */
    public static function isQQ(?string $input): bool
    {
        return self::common('/^\d{6,}$/', $input);
    }

    /**
     * 电话号码
     *
     * @param string|null $input
     *
     * @return bool
     */
    public static function isPhoneNum(?string $input): bool
    {
        return self::common('/^[0-9\-]+$/', $input);
    }
}
