<?php

namespace HongXunPan\Tools\Cache;

use HongXunPan\DB\Redis\Redis;

class Cache
{
    /** @var null|static $instance */
    private static $instance = null;

    private function __construct()
    {
        //
    }

    private function __clone()
    {
        //
    }

    /**
     * @return static|null
     */
    public static function getInstance()
    {
        //判断$instance
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /** @var \Redis $redis */
    private $redis;
    private $prefix = 'cache:';

    public function setConfig($prefix = 'cache:', $redis = null)
    {
        $this->prefix = $prefix;
        if ($redis instanceof \Redis) {
            $this->redis = $redis;
        } else {
            $this->redis = Redis::connection();
        }
        return static::getInstance();
    }

    private $getCache = true;

    public function setCacheMode($getCache = true)
    {
        $this->getCache = $getCache;
        return static::getInstance();
    }

    public static function remember($redisKey, $ttl, callable $function)
    {
        $cache = static::get($redisKey);
        if ($cache !== false) {
            return $cache;
        }
        $res = $function();
        static::set($redisKey, $res, $ttl);
        return $cache;
    }

    public static function get($redisKey)
    {
        $instance = static::getInstance();
        if (!$instance->getCache) {
            return false;
        }
        $redisKey = $instance->prefix . $redisKey;
        return $instance->redis->get($redisKey);
    }

    public static function set($redisKey, $data, $ttl)
    {
        $instance = static::getInstance();
        $redisKey = $instance->prefix . $redisKey;
        $instance->redis->set($redisKey, $data);
    }
}
