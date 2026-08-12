<?php

namespace HongXunPan\Tools\Log;

use HongXunPan\Tools\Abstracts\SingletonAbstract;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class Log extends SingletonAbstract implements LoggerInterface
{
    use LoggerTrait;

    protected $channel = '';
    private static $logPath = '';
    private static $jsonLinesEnabled = false;
    private static $contextProviders = [];
    private static $writeFailureHandler;

    public function setLogPath($logPath)
    {
        if (!is_dir($logPath) && !@mkdir($logPath, 0777, true) && !is_dir($logPath)) {
            throw new \Exception("log path can not be created");
        }
        if (!is_writable($logPath)) {
            throw new \Exception("log path is not writable");
        }
        //自动添加/
        $logPath = rtrim($logPath, '/') . '/';
        self::$logPath = $logPath;
    }

    public function getLogPath()
    {
        if (self::$logPath === '') {
            self::$logPath = rtrim(__DIR__ . '/../../../logs', '/') . '/';
        }

        return self::$logPath;
    }

    public function useJsonLines($enabled = true)
    {
        self::$jsonLinesEnabled = (bool)$enabled;
    }

    public function addContextProvider(LogContextProvider $provider)
    {
        self::$contextProviders[get_class($provider)] = $provider;
    }

    public function removeContextProvider($providerClass)
    {
        if (!is_string($providerClass) || $providerClass === '') {
            throw new \InvalidArgumentException('context provider class must be a non-empty string');
        }

        unset(self::$contextProviders[ltrim($providerClass, '\\')]);
    }

    public function clearContextProviders()
    {
        self::$contextProviders = [];
    }

    public function setWriteFailureHandler($handler = null)
    {
        if ($handler !== null && !is_callable($handler)) {
            throw new \InvalidArgumentException('write failure handler must be callable or null');
        }

        self::$writeFailureHandler = $handler;
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
        $log->channel = $channel;
        return $log;
    }

    protected function write($level, $msg, $data = [])
    {
        $now = time();
        $day = date('Y-m-d', $now);
        $log = self::$jsonLinesEnabled
            ? (new JsonLineFormatter())->format(
                $level,
                $this->channel,
                $msg,
                $data,
                $this->resolveSharedContext()
            )
            : $this->formatLegacyLog($level, $msg, $data, $now);
        $fileName = $this->channel
            ? $this->channel . '-' . $day . '.log'
            : $level . '-' . $day . '.log';

        (new FileLogWriter())->write(
            $this->getLogPath() . $fileName,
            $log,
            self::$writeFailureHandler,
            [
                'channel' => $this->channel,
                'level' => (string)$level,
            ]
        );
    }

    private function formatLegacyLog($level, $msg, array $data, $now)
    {
        $time = date('Y-m-d H:i:s', $now);
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $log = "[" . strtoupper($level) . "] " . $time . ' - ' . getmypid() . ' - ' . $ip;
        if (php_sapi_name() != 'cli') {
            $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
            $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
            $http = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
            $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
            $url = $http . '://' . $host . $uri;
            $log .= ' - [' . $method . '] - ' . $url;
        }
        if ($msg) {
            $log .= PHP_EOL . $msg;
        }
        if ($data) {
            $log .= PHP_EOL . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        $log .= PHP_EOL . PHP_EOL;

        return $log;
    }

    private function resolveSharedContext()
    {
        $context = [];
        foreach (self::$contextProviders as $provider) {
            $providerClass = get_class($provider);
            try {
                $providedContext = $provider->context();
            } catch (\Exception $exception) {
                error_log(
                    '[php-tools:log-context-failed] provider=' . $providerClass
                    . ' exception=' . get_class($exception)
                );
                continue;
            } catch (\Throwable $throwable) {
                error_log(
                    '[php-tools:log-context-failed] provider=' . $providerClass
                    . ' exception=' . get_class($throwable)
                );
                continue;
            }

            if (!is_array($providedContext)) {
                error_log(
                    '[php-tools:log-context-invalid] provider=' . $providerClass
                    . ' must return array'
                );
                continue;
            }

            $context = array_replace($context, $providedContext);
        }

        return $context;
    }

    public function log($level, $message, array $context = [])
    {
        $this->write($level, $message, $context);
    }
}
