<?php

namespace HongXunPan\Tools\Log;

use HongXunPan\Tools\Abstracts\SingletonAbstract;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class Log extends SingletonAbstract implements LoggerInterface
{
    use LoggerTrait;

    private $logPath = __DIR__ . '/../../../logs';

    public function setLogPath($logPath)
    {
        if (!is_dir($logPath)) {
            mkdir($logPath, 0777, true);
        }
        if (!is_writable($logPath)) {
            throw new \Exception("log path is not writable");
        }
        $this->logPath = $logPath;
    }

    protected function write($level, $msg, $data = [])
    {
//        $log = [
//            'level' => $level,
//            'msg' => $msg,
//            'data' => $data,
//            'time' => date('Y-m-d H:i:s'),
//        ];
//        $log = json_encode($log, JSON_UNESCAPED_UNICODE);
//        @file_put_contents($this->logPath . '/' . $level . date('Y-m-d') . '.log', $log . PHP_EOL, FILE_APPEND);

        $time = date('Y-m-d H:i:s');
        $log = $time . ' - ' . posix_getpid();
        if (php_sapi_name() != 'cli') {
            $uri = $_SERVER['REQUEST_URI'];
            $host = $_SERVER['HTTP_HOST'];
            $http = $_SERVER['REQUEST_SCHEME'];
            $url = $http . '://' . $host . $uri;
            $log .= ' - ' . $url;
        }
        if ($msg) {
            $log .= PHP_EOL . $msg;
        }
        if ($data) {
            $log .= PHP_EOL . json_encode($data, JSON_PRETTY_PRINT);
        }
        $log .= PHP_EOL;
        @file_put_contents($this->logPath . '/' . $level . substr($time, 0, 10) . '.log', $log . PHP_EOL, FILE_APPEND);
    }

    public function log($level, $message, array $context = [])
    {
        $this->write($level, $message, $context);
    }
}