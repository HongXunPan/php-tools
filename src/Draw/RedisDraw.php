<?php

namespace HongXunPan\Tools\Draw;

use Exception;
use HongXunPan\Tools\Db\Redis\Redis;

class RedisDraw
{
    /** @var \Redis */
    private $redis;
    private $poolName;
    private $redisKey;

    public function __construct($poolName = 'default', $redis = null)
    {
        if (!$redis instanceof \Redis) {
            $redis = Redis::connection();
        }
        $this->redis = $redis;
        $this->poolName = $poolName;
        $this->redisKey = 'draw:' . $poolName;
    }

    public function addUser2Pool(array $userIds)
    {
        $this->redis->sAddArray($this->redisKey, $userIds);
    }

    public function removeUserFromPool(array $userIds)
    {
        $this->redis->sRem($this->redisKey, ...$userIds);
    }

    /**
     * 用户是否在奖池内
     * @param $userId
     * @return bool
     * @author HongXunPan <me@kangxuanpeng.com>
     * @date 2022-10-08 17:59
     */
    public function isUserInPool($userId)
    {
        return $this->redis->sIsMember($this->redisKey, $userId);
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
     * @return array
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
            throw new Exception("pool: $this->poolName member is not enough");
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

    /**
     * 一定要保证奖池大于抽奖数
     * 复杂点的 带有优先级的抽奖属性 //一个优先级比较高的池子，另一个是普通的
     * @param string $highPoolName 高级奖池名字，需提前添加好成员
     * @param int $highCount 有几个高级中奖名额
     * @param string $normalPoolName 普通奖池名额，也需要提前添加成员
     * @param bool $canDuplicate 中了高级之后是否还能中普通的
     * @param null $redis
     * @return array
     * @throws Exception
     * @author HongXunPan <me@kangxuanpeng.com>
     * @date 2022-10-06 00:20
     */
    public static function drawByWeight($highPoolName, $highCount, $normalPoolName, $canDuplicate = false, $redis = null)
    {
        $highPool = new self($highPoolName, $redis);
        $normalPool = new self($normalPoolName, $redis);

        $highPoolTotal = $highPool->getPoolUserCount();
        if ($highPoolTotal < $highCount) {
//            $highUser = $highPool->draw($highPoolTotal, $canDuplicate);
            $highUser = $highPool->getPoolUsers();//优先奖池全中了
            //优先的奖池都比要抽的数量少 则需要去普通奖池借几个人
            $borrowUserCount = $highCount - $highPoolTotal;
            $normalPool->removeUserFromPool($highUser);//去重防止高级池子重复

            $borrowUser = $normalPool->draw($borrowUserCount, $canDuplicate);
            if (!$canDuplicate) {
                $highPool->freePool();
            }
            $drawHighUser = array_merge($highUser, $borrowUser);
        } else {
            $drawHighUser = $highPool->draw($highCount, $canDuplicate);
            if (!$canDuplicate) {
                $normalPool->removeUserFromPool($drawHighUser); //中了高等级的奖就不能再中普通的奖了，从普通奖池拿掉
            }
        }
        return $drawHighUser;
    }
}
