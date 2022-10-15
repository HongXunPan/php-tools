<?php

namespace HongXunPan\Tools\TimeLimitOffers;

use HongXunPan\DB\Redis\Redis as HongXunPanRedis;
use Redis;

class RedisTimeLimitOffers
{
    private $roundName;
    private $redisKeyCount;
    private $redisKeyUsers;
    private $userId;
    /** @var Redis */
    private $redis;

    private $limits;

    public function __construct($userId, $limit = 0, $roundName = 'default', $redis = null)
    {
        $this->userId = $userId;
        $this->roundName = $roundName;
        $this->redisKeyCount = 'TimeLimitOffers:Count:' . $roundName;
        $this->redisKeyUsers = 'TimeLimitOffers:User:' . $roundName;
        $this->limits = $limit;
        if (!$redis instanceof Redis) {
            $redis = HongXunPanRedis::connection();
        }
        $this->redis = $redis;
    }

    /**
     * 检查用户是否有名额参与秒杀
     * @return bool
     * @throws TimeLimitOffersException
     * @author HongXunPan <me@kangxuanpeng.com>
     * @date 2022-10-15 14:58
     */
    public function isHaveChance()
    {
        if ($this->redis->hExists($this->redisKeyUsers, $this->userId)) {
            //已经抢到过名额,在用户池里
            return true;
        }
        $count = $this->redis->get($this->redisKeyCount);//用户总数
        if ($count === false) {
            $count = 0;
        }
        if ($count >= $this->limits) {
            throw new TimeLimitOffersException(
                TimeLimitOffersException::NO_CHANCE_LEFT,
                json_encode(
                    [
                        'userId' => $this->userId,
                        'roundName' => $this->roundName,
                        'nowCount' => $count, 'limits' => $this->limits
                    ]
                )
            );
//            return false;
        }
        $newCount = $this->redis->incr($this->redisKeyCount);
        if ($newCount > $this->limits) {
            //超卖
//            oo::logs()->positionLog('超卖,newCount='.$newCount.',time='.$time, 'isHaveChance');
            throw new TimeLimitOffersException(
                TimeLimitOffersException::OUT_OF_LIMIT,
                json_encode(
                    [
                        'userId' => $this->userId,
                        'roundName' => $this->roundName,
                        'newCount' => $newCount,
                        'limits' => $this->limits
                    ]
                )
            );
//            return false;
        }
        //将用户添加到用户池
        $this->redis->hSet($this->redisKeyUsers, $this->userId, date('Ymd H:i:s'));

        return true;
    }

    public function expire($ttl = 30 * 60)
    {
        $this->redis->expire($this->redisKeyCount, $ttl);
        $this->redis->expire($this->redisKeyUsers, $ttl);
    }

    public function expireAt($timestamp)
    {
        $this->redis->expireAt($this->redisKeyCount, $timestamp);
        $this->redis->expireAt($this->redisKeyUsers, $timestamp);
    }
}
