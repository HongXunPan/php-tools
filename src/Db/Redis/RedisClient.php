<?php

namespace HongXunPan\Tools\Db\Redis;

class RedisClient
{
    private $redis;

    /**
     * @param \Redis $redis
     */
    public function __construct($redis)
    {
        $this->redis = $redis;
        return $this;
    }

    /**
     * @deprecated 仅用于代码提示
     * @return \Redis
     */
    public function getRedis()
    {
        return $this->redis;
    }

    public function __call($name, $arguments)
    {
        $exist = method_exists($this->redis, $name);
        if (!$exist) {
            throw new RedisException("method '$name' does not exist");
        }
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $this->redis->$name(...$arguments);
        //do some log
        return $result;
    }
}
