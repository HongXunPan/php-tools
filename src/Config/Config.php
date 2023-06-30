<?php

namespace HongXunPan\Tools\Config;

use HongXunPan\Tools\Common\SetPath;

/**
 * 配置类
 * Created by PhpStorm At 2023/6/30 09:43.
 * Author: HongXunPan
 * Email: me@kangxuanpeng.com
 */
class Config extends SetPath
{
    /** @var Config|null $instance */
    private static $instance = null;
    /** @var array|null $config */
    public static $config = false;

    private static $configPath = '';
    private static $cachePath = '';

    private function __construct()
    {
        //
    }

    private function __clone()
    {
        //
    }

    /**
     * @return Config
     * @author HongXunPan <me@kangxuanpeng.com>
     * @date 2023-06-29 11:53
     */
    public static function getInstance()
    {
        //判断$instance
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    protected static function initConfig()
    {
        if (file_exists(self::JSON_CONFIG_FILE)) {
            $pathConfig = self::getConfigFromJsonFile();
            if (isset($pathConfig['configPath'])) {
                self::$configPath = $pathConfig['configPath'];
            }
            if (isset($pathConfig['cachePath'])) {
                self::$cachePath = $pathConfig['cachePath'];
            }
            $canCache = !isset($pathConfig['canCache']) || (bool)$pathConfig['canCache'];
            self::loadConfig($canCache);
        }
    }

    public function getConfig($key = '')
    {
        if (self::$config === false) {
            $this->initConfig();
            self::loadConfig();
            return $this->getConfig($key);
        }
        if (empty($key)) {
            return self::$config;
        }
        $keyArr = explode('.', $key);
        $config = self::$config;
        foreach ($keyArr as $configName) {
//            $config = $config[$configName] ?? '';
            $config = isset($config[$configName]) ? $config[$configName] : '';
        }
        return $config;
    }

    public static function loadConfig($canCache = true, $configPath = '', $cachePath = '')
    {
        if (self::$config !== false) {
            return;
        }
//        $cachePath = app()->bootstrapPath('cache');
        if (!$cachePath) {
            $cachePath = self::$cachePath;
        }
        $cacheFile = $cachePath . '/config.php';
        if ($canCache && file_exists($cacheFile)) {
            $config = require_once $cacheFile;
            if ($canCache) {
                self::$config = $config;
                return;
            }
        }

        $files = glob(self::$configPath . '/*.php');
        $config = [];
        foreach ($files as $file) {
            $key = str_replace('.php', '', basename($file));
            $config[$key] = require_once $file;
        }
        if ($canCache) {
            if (!file_exists($cachePath)) {
                mkdir($cachePath);
            }
            file_put_contents($cacheFile, '<?php return ' . var_export($config, true) . ';');
        } else {
            @unlink($cacheFile);
        }
        self::$config = $config;
    }
}
