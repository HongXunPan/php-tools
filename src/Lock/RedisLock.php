<?php

namespace HongXunPan\Tools\Lock;

use HongXunPan\DB\Redis\Redis;

class RedisLock
{
    //一定时间内最大请求次数 超过则列为刷子
    private $maxTimes;

    private $userId;
    private $lockName;
    private $redisKey;
    /** @var \Redis $redis */
    private $redis;

    public function __construct($userId, $lockName = 'lock', $redis = null, $maxTimes = 5)
    {
        $this->userId = $userId;
        $this->lockName = $lockName ?: 'lock';
        $this->redisKey = $this->lockName . ':' . $this->userId;
        $this->maxTimes = $maxTimes;
        if ($redis !== null) {
            $this->redis = $redis;
        } else {
            $this->redis = Redis::connection();
        }
    }

    public static function transaction(array $lockConfig, callable $function)
    {
        return static::withUserLock($lockConfig, $function);
    }

    public static function withUserLock(array $lockConfig, callable $onAcquired, $onRejected = null)
    {
        $default = [
            'lockName' => 'lock',
            'redis' => null,
            'time' => 10,
            'maxTimes' => 5,
        ];
        $lockConfig = array_merge($default, $lockConfig);
        if (!array_key_exists('userId', $lockConfig) || $lockConfig['userId'] === null) {
            throw new \InvalidArgumentException('userId is required');
        }

        $lock = new static(
            $lockConfig['userId'],
            $lockConfig['lockName'],
            $lockConfig['redis'],
            $lockConfig['maxTimes']
        );

        $attempt = $lock->attemptUserLock($lockConfig['time']);
        if ($attempt->isAcquired()) {
            try {
                return call_user_func($onAcquired);
            } finally {
                $lock->clearUserLock();
            }
        }

        if (is_callable($onRejected)) {
            return call_user_func($onRejected, $attempt, $attempt->count());
        }

        throw LockException::forRejected($lock->userId, $lock->lockName, $attempt->count());
    }

    /**
     * 添加用户独占锁
     * @param int $time
     * @return bool|int
     */
    public function addUserLock($time = 10)
    {
        if (empty($this->userId)) {
            return false;
        }

        return $this->attemptUserLock($time)->count();
    }

    /**
     * @param int $time
     * @return RedisLockAttemptResult
     */
    public function attemptUserLock($time = 10)
    {
        if (empty($this->userId)) {
            return new RedisLockAttemptResult(0);
        }
        $time = (int)$time;
        if ($time <= 0) {
            throw new \InvalidArgumentException('lock ttl must be greater than zero');
        }

        $count = $this->incrementLockTimesAtomically($time);
        /** @noinspection PhpStatementHasEmptyBodyInspection */
        if ($count >= $this->maxTimes) {
            //一定时间内发多个请求 超过最大限制
            //do some log
        }

        return new RedisLockAttemptResult($count);
    }

    private function incrementLockTimesAtomically($time)
    {
        return (int)$this->redis->eval(
            $this->lockAttemptLuaScript(),
            [$this->redisKey, (int)$time],
            1
        );
    }

    private function lockAttemptLuaScript()
    {
        return "local count = redis.call('INCR', KEYS[1])\n"
            . "local ttl = tonumber(ARGV[1])\n"
            . "if count == 1 and ttl > 0 then\n"
            . "    redis.call('EXPIRE', KEYS[1], ttl)\n"
            . "end\n"
            . "return count";
    }

    /**
     * 清除用户独占锁
     * @return bool|int
     */
    public function clearUserLock()
    {
        if (empty($this->userId)) {
            return false;
        }
        return $this->redis->del($this->redisKey);
    }

    /**
     * @param int $time
     * @return int
     * @throws LockException
     * @author HongXunPan <me@kangxuanpeng.com>
     * @date 2022-10-05 16:27
     */
    public function addUserLockOrThrow($time = 10)
    {
        $attempt = $this->attemptUserLock($time);
        if (!$attempt->isAcquired()) {
            throw LockException::forRejected($this->userId, $this->lockName, $attempt->count());
        }
        return $attempt->count();
    }
}
