<?php

namespace HongXunPan\Tools\Lock;

use HongXunPan\Tools\Db\Redis\Redis;

class RedisLock
{
    //一定时间内最大请求次数 超过则列为刷子
    private $maxTimes = 5;

    private $userId;
    private $lockName;
    private $redisKey;
    /** @var \Redis $redis */
    private $redis;

    public function __construct($userId, $lockName = 'lock', $redis = null, $maxTimes = 5)
    {
        $this->userId = $userId;
        $this->lockName = $lockName;
        $this->redisKey = $this->lockName . ':' . $this->userId;
        $this->maxTimes = $maxTimes;
        if ($redis instanceof \Redis) {
            $this->redis = $redis;
        } else {
            $this->redis = Redis::connection();
        }
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

        $result = $this->incrLockTimes();
        if ($time != 0 && $result == 1) {
            $this->redis->expire($this->redisKey, $time);
        }
        return $result;
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
        $result = $this->addUserLock($time);
        if ($result !== 1) {
            throw new LockException("Lock Fail: $this->userId -> $this->lockName");
        }
        return $result;
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
     * 一定时间内频繁请求的用户
     * 超过设定次数则特别标记重点观察
     * @return int
     */
    private function incrLockTimes()
    {
        $times = $this->redis->incr($this->redisKey);
        /** @noinspection PhpStatementHasEmptyBodyInspection */
        if ($times >= $this->maxTimes) {
            //一定时间内发多个请求 超过最大限制
            //do some log
        }
        return $times;
    }
}
