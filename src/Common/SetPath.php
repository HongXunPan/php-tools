<?php

namespace HongXunPan\Tools\Common;

use ReflectionClass;

abstract class SetPath
{
    const JSON_CONFIG_FILE = 'HongXunPan-config.json';
    protected static $configFile;

    /**
     * 设置 或者 获取路径
     * @param $get
     * @param array $config
     * @return false|int|mixed|void
     * @author HongXunPan <me@kangxuanpeng.com>
     * @date 2023-06-30 11:08
     */
    public static function cliConfig($get = true, array $config = [])
    {
        if (php_sapi_name() != 'cli') {
            exit();
        }
        $jsonFile = self::getConfigFile();
        if ($get) {
            return json_decode(file_get_contents($jsonFile), true);
        }
        return file_put_contents($jsonFile, json_encode($config));
    }

    private static function getConfigFile()
    {
        if (self::$configFile) {
            return self::$configFile;
        }
        $childClassFileName = (new ReflectionClass(get_called_class()))->getFileName();
        return self::$configFile = dirname($childClassFileName) . DIRECTORY_SEPARATOR . self::JSON_CONFIG_FILE;
    }

    final public static function getConfigFromJsonFile()
    {
        return json_decode(file_get_contents(self::getConfigFile()), true);
    }

    protected static function initConfig(){}
}