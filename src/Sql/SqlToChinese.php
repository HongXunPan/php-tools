<?php

namespace app\helpers;

use QH_Exception;

class SqlToChinese
{
    /** @var string[] 需转换成中文的错误提示 */
    public const CONVERT = [
        'You have an error in your SQL syntax;' => '您的SQL语法中有错误;',
        'check the manual that corresponds to your MySQL server version for the right syntax to use near' =>
            '检查与您的MySQL服务器版本相对应的手册，以获取要在附近使用的正确语法',
    ];

    /** @var string[] 需抛出异常信息的消息 */
    public const THROW_MSG = [
        'INSERT command denied to user' => '数据库账号没有新增权限',
        'DELETE command denied to user' => '数据库账号没有删除权限',
        'UPDATE command denied to user' => '数据库账号没有修改权限',
        'SELECT command denied to user' => '数据库账号没有查询权限',
    ];

    /**
     * 执行sql的错误信息转换成中文，部分敏感的sql错误信息会被上级系统拦截
     *
     * @param $sql
     *
     * @return string
     */
    public static function match($sql): string
    {
        foreach (self::CONVERT as $k => $v) {
            $sql = str_replace($k, $v, $sql);
        }

        return $sql;
    }

    /**
     * 部分sql错误需抛出对应的中文异常信息
     *
     * @param $errorMsg
     *
     * @return void
     * @throws QH_Exception
     */
    public static function matchThrow($errorMsg)
    {
        foreach (self::THROW_MSG as $k => $v) {
            if (stristr($errorMsg, $k) !== false) {
                throw new QH_Exception($v, QH_Exception::RET_CODE_OP_FAIL);
            }
        }
    }
}
