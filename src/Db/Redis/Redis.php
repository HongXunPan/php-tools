<?php

namespace HongXunPan\Tools\Db\Redis;

use Exception;

class Redis
{
    protected static $instance;

    private $connectConfig;
    private $connection;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return Redis
     * @author HongXunPan <me@kangxuanpeng.com>
     * @date 2022-10-05 16:17
     */
    private static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @param array $config
     * default:
     * 'host' => '127.0.0.1',
     * 'port' => 6379,
     * 'timeout' => 0.0,
     * 'reserved' => null,
     * 'retryInterval' => 0,
     * 'readTimeout' => 0.0
     * @param string $connectName default: default
     * @param array $options
     * @see $redis->setOption()
     * @author HongXunPan <me@kangxuanpeng.com>
     * @date 2022-10-05 16:18
     */
    public static function setConfig(array $config = [], $connectName = 'default', array $options = [])
    {
        $default = [
            'host' => '127.0.0.1',
            'port' => 6379,
            'timeout' => 0.0,
            'reserved' => null,
            'retryInterval' => 0,
            'readTimeout' => 0.0
        ];
        $config = array_merge($default, $config);
        $config['options'] = $options;
        $instance = self::getInstance();
        $instance->connectConfig[$connectName] = $config;
    }

    /**
     * @param string $connectName
     * @return \Redis
     * @throws RedisException
     */
    private static function getRedis($connectName = 'default')
    {
        $instance = self::getInstance();
        if (isset($instance->connection[$connectName])) {
            /** @var \Redis $redis */
            $redis = $instance->connection[$connectName];
            try {
                if ($redis->ping() === true) {
                    return $redis;
                }
            } catch (Exception $e) {
            }
        }

        if (!isset($instance->connectConfig[$connectName])) {
            if ($connectName == 'default') {
                self::setConfig();
            } else {
                throw new RedisException("connection '$connectName' config not set");
            }
        }
        $config = $instance->connectConfig[$connectName];
        $redis = new \Redis();
        try {
            $redis->connect(
                $config['host'],
                $config['port'],
                $config['timeout'],
                $config['reserved'],
                $config['retryInterval'],
                $config['readTimeout']
            );
            $options = $config['options'];
            foreach ($options as $optionKey => $option) {
                $redis->setOption($optionKey, $option);
            }
        } catch (Exception $e) {
            throw new RedisException("connection '$connectName' connect fail, reason: {$e->getMessage()}");
        }
        $instance->connection[$connectName] = $redis;
        return $redis;
    }

    public static function connection($connectName = 'default')
    {
        return new RedisClient(self::getRedis($connectName));
    }
}
