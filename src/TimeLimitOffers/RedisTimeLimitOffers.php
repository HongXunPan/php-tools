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

    public function isHaveChance()
    {
        if ($this->redis->hExists($this->redisKeyUsers, $this->userId)) {
            return true;
        }
        $count = $this->redis->get($this->redisKeyCount);
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
        $this->redis->hSet($this->redisKeyUsers, $this->userId, date('Ymd H:i:s'));

        $this->redis->expire($this->redisKeyCount, 60 * 60 * 12);
        $this->redis->expire($this->redisKeyUsers, 60 * 60 * 12);
        return true;
    }
}
