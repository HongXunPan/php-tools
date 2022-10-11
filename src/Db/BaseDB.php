<?php

namespace HongXunPan\Tools\Db;

use Exception;

abstract class BaseDB implements DBInterface
{
    protected static $instance;

    protected $connectConfig;
    protected $connection;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return BaseDB
     * @author HongXunPan <me@kangxuanpeng.com>
     * @date 2022-10-11 15:30
     */
    protected static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    protected static function saveConfig($config, $connectName)
    {
        $instance = self::getInstance();
        $instance->connectConfig[$connectName] = $config;
    }

    abstract protected function ping($connection);

    abstract protected function connect(array $config);

    final public static function getConnection($connectName = 'default')
    {
        $instance = self::getInstance();
        if (isset($instance->connection[$connectName])) {
            $connection = $instance->ping($instance->connection[$connectName]);
            if ($connection !== false) {
                return $connection;
            }
        }

        if (!isset($instance->connectConfig[$connectName])) {
            if ($connectName == 'default') {
                $instance::setConfig();
            } else {
                throw new DBException("connection '$connectName' config not set");
            }
        }
        $config = $instance->connectConfig[$connectName];
        try {
            $connection = $instance->connect($config);
        } catch (Exception $e) {
            $type = get_class($e);
            throw new DBException("connection '$connectName' connect fail, reason: {$e->getMessage()}, type: $type");
        }
        $instance->connection[$connectName] = $connection;
        return $connection;
    }
}
