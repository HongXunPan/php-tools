<?php

namespace HongXunPan\Tools\Log;

use HongXunPan\Tools\Abstracts\SingletonAbstract;

class Log extends SingletonAbstract
{
    private $logPath = __DIR__ . '/../../../logs';

    public static function info($msg, $data = [])
    {
        self::getInstance()->write('info', $msg, $data);
    }

    protected function write($level, $msg, $data = [])
    {
        $log = [
            'level' => $level,
            'msg' => $msg,
            'data' => $data,
            'time' => date('Y-m-d H:i:s'),
        ];
        $log = json_encode($log, JSON_UNESCAPED_UNICODE);
        @file_put_contents($this->logPath . '/' . date('Y-m-d') . '.log', $log . PHP_EOL, FILE_APPEND);
    }
}