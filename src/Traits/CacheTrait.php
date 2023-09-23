<?php

namespace app\traits;

use Cache_service;

/**
 * 缓存服务
 * Trait CacheTrait
 * @package app\traits
 */
trait CacheTrait
{
    /**
     * 缓存时间
     * @var int
     */
    protected $cacheSecond = 86400;
    /**
     * 缓存前缀
     * @var string
     */
    protected $cachePrefix = '';

    protected static $cacheInstance;

    /**
     * 缓存服务
     * @return Cache_service
     */
    public static function cacheService(): Cache_service
    {
        if (null === static::$cacheInstance) {
            $CI = &get_instance();
            static::$cacheInstance = $CI->cache_service;
        }
        return static::$cacheInstance;
    }


    /**
     * @param ...$param
     * @return string
     */
    public function cacheKey(...$param): string
    {
        $paramKey = '';
        foreach ($param as $v) {
            $paramKey .= json_encode($v);
        }
        return "{$this->cachePrefix}:" . md5($paramKey);
    }
}
