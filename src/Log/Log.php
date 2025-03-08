<?php

namespace HongXunPan\Tools\Log;

use HongXunPan\Tools\Abstracts\SingletonAbstract;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * @method static void log($level, $message, array $context = [])
 */
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

    public static function __callStatic($name, $arguments)
    {
        return self::getInstance()->$name(...$arguments);
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
        $day = date('Ymd');
        $log = "[" . strtoupper($level) . "] " . $time . ' - ' . posix_getpid();
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
        $fileName = $this->channel ? $this->channel . '-' . $day . "log" : $level . "-" . $day . "log";
        @file_put_contents($this->logPath . '/' . $fileName, $log . PHP_EOL, FILE_APPEND);
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