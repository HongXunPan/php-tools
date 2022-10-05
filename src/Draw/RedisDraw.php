<?php

namespace HongXunPan\Tools\Draw;

use Exception;
use HongXunPan\Tools\Db\Redis\Redis;

class RedisDraw
{
    /** @var \Redis */
    private $redis;
    private $redisKey;

    public function __construct($poolName = 'default', $redis = null)
    {
        if (!$redis instanceof \Redis) {
            $redis = Redis::connection();
        }
        $this->redis = $redis;
        $this->redisKey = 'draw:' . $poolName;
    }

    public function addUser2Pool(array $userIds)
    {
        $this->redis->sAddArray($this->redisKey, $userIds);
    }

    public function removeUserFromPool(array $userIds)
    {
        $this->redis->sRem($this->redisKey, $userIds);
    }

    public function getPoolUserCount()
    {
        return $this->redis->sCard($this->redisKey);
    }

    public function getPoolUsers()
    {
        return $this->redis->sMembers($this->redisKey);
    }

    /**
     * @param int $count
     * @param bool $canDuplicate
     * @return array|bool|mixed|string
     * @author HongXunPan <me@kangxuanpeng.com>
     * @date 2022-10-05 21:45
     */
    public function draw($count = 1, $canDuplicate = false)
    {
        if ((int)$count != $count) {
            throw new Exception('count must be int');
        }
        //先判断个数
//        $total = $this->redis->sCard($this->redisKey);
        if ($this->getPoolUserCount() < $count) {
            throw new Exception('pool member is not enough');
        }

        if ($canDuplicate) {
            $result = $this->redis->sRandMember($this->redisKey, $count);
        } else {
            $result = $this->redis->sPop($this->redisKey, $count);
        }
        return $result;
    }

    public function freePool()
    {
        $this->redis->unlink($this->redisKey);
    }

    public function expirePool($time)
    {
        $this->redis->expire($this->redisKey, $time);
    }

    public function expirePoolAt($timestamp)
    {
        $this->redis->expireAt($this->redisKey, $timestamp);
    }

}
