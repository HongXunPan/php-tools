<?php

namespace HongXunPan\Tools\Env;

use Exception;
use HongXunPan\Tools\Common\SetPath;

class Env extends SetPath
{
    /** @var bool $loaded */
    private static $loaded = false;
    private static $configPath;
    const ENV_PREFIX = '';

    protected static function initConfig()
    {
        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . self::JSON_CONFIG_FILE)) {
            $pathConfig = self::getConfigFromJsonFile();
            if ($pathConfig['envPath']) {
                self::$configPath = $pathConfig['envPath'];
            }
        } else {
            if (PHP_VERSION >= '7.0') {
                self::$configPath = dirname(__DIR__, 5);
            } else {
                self::$configPath = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
            }
        }
    }

    /**
     * 加载配置文件
     * @access public
     * @param string $filePath 配置文件路径
     * @return void
     * @throws Exception
     */
    public static function loadFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw new Exception('配置文件: ' . $filePath . '不存在');
        }
        $env = parse_ini_file($filePath, true);

        foreach ($env as $key => $val) {
            $prefix = static::ENV_PREFIX . strtoupper($key);
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    $item = $prefix . '_' . strtoupper($k);
                    putenv("$item=$v");
                }
            } else {
                putenv("$prefix=$val");
            }
        }
    }

    /**
     * 获取环境变量值
     * @access public
     * @param string $name 环境变量名(支持二级 . 号分割)
     * @param string|array|null $default 默认值
     * @return bool|array|string|null
     * @throws Exception
     */
    public static function get($name, $default = '')
    {
        if (!self::$loaded) {
            if (!self::$configPath) {
                self::initConfig();
            }
            self::loadFile(self::$configPath . '/.env');
            self::$loaded = true;
        }
        $result = getenv(static::ENV_PREFIX . strtoupper(str_replace('.', '_', $name)));
        if (false !== $result) {
            if ('false' === $result) {
                $result = false;
            } elseif ('true' === $result) {
                $result = true;
            }
            return $result;
        }
        return $default;
    }
}
