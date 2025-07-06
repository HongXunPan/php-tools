<?php

namespace HongXunPan\Tools\Log;

use HongXunPan\Tools\Abstracts\SingletonAbstract;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class Log extends SingletonAbstract implements LoggerInterface
{
    use LoggerTrait;

    protected $channel = [];
    private $fileName = '';
    private $logPath = __DIR__ . '/../../../logs';

    public function setLogPath($logPath)
    {
        if (!is_dir($logPath)) {
            mkdir($logPath, 0777, true);
        }
        if (!is_writable($logPath)) {
            throw new \Exception("log path is not writable");
        }
        //自动添加/
        $logPath = rtrim($logPath, '/') . '/';
        $this->logPath = $logPath;
    }

    public function getLogPath()
    {
        return $this->logPath;
    }

    public static function channel($channel = '')
    {
        if (!$channel) {
            return self::getInstance();
        }
        if (isset(self::$instance[$channel])) {
            return self::$instance[$channel];
        }
        $log = new static();
        self::$instance[$channel] = $log;
        $log->setLogPath(self::getInstance()->getLogPath());
        $log->channel = $channel;
        return $log;
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

        $now = time();
        $time = date('Y-m-d H:i:s', $now);
        $day = date('Y-m-d', $now);
        $log = "[" . strtoupper($level) . "] " . $time . ' - ' . posix_getpid();
        if (php_sapi_name() != 'cli') {
            $uri = $_SERVER['REQUEST_URI'];
            $host = $_SERVER['HTTP_HOST'];
            $http = $_SERVER['REQUEST_SCHEME'];
            $url = $http . '://' . $host . $uri;
            $log .= ' - [' . $_SERVER['REQUEST_METHOD'] . '] - ' . $url;
        }
        if ($msg) {
            $log .= PHP_EOL . $msg;
        }
        if ($data) {
            $log .= PHP_EOL . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        $log .= PHP_EOL . PHP_EOL;
        $fileName = $this->channel ? $this->channel . '-' . $day . ".log" : $level . "-" . $day . ".log";
        @file_put_contents($this->logPath . $fileName, $log, FILE_APPEND);
    }

    /** @noinspection PhpElementIsNotAvailableInCurrentPhpVersionInspection
     * @noinspection PhpUndefinedClassInspection
     * @noinspection PhpLanguageLevelInspection
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $this->write($level, $message, $context);
    }
}