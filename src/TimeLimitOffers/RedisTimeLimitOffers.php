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
        if ($redis === null) {
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
        $result = $this->redis->eval(
            $this->claimLuaScript(),
            [
                $this->redisKeyCount,
                $this->redisKeyUsers,
                (string)$this->userId,
                (int)$this->limits,
                date('Ymd H:i:s'),
            ],
            2
        );

        if (!is_array($result) || count($result) < 2) {
            throw new TimeLimitOffersException(
                TimeLimitOffersException::OUT_OF_LIMIT,
                '抢购名额 Redis 返回结果无效'
            );
        }

        if ((int)$result[0] !== 1) {
            throw new TimeLimitOffersException(
                TimeLimitOffersException::NO_CHANCE_LEFT,
                json_encode(
                    [
                        'userId' => $this->userId,
                        'roundName' => $this->roundName,
                        'nowCount' => (int)$result[1],
                        'limits' => $this->limits,
                    ]
                )
            );
        }

        return true;
    }

    private function claimLuaScript()
    {
        return "if redis.call('HEXISTS', KEYS[2], ARGV[1]) == 1 then\n"
            . "    return {1, tonumber(redis.call('GET', KEYS[1]) or '0')}\n"
            . "end\n"
            . "local count = tonumber(redis.call('GET', KEYS[1]) or '0')\n"
            . "local limits = tonumber(ARGV[2])\n"
            . "if count >= limits then\n"
            . "    return {0, count}\n"
            . "end\n"
            . "local newCount = redis.call('INCR', KEYS[1])\n"
            . "redis.call('HSET', KEYS[2], ARGV[1], ARGV[3])\n"
            . "return {1, newCount}";
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

    public function getChanceLeft()
    {
        $count = $this->redis->get($this->redisKeyCount);//用户总数
        if ($count === false) {
            $count = 0;
        }
        $left = $this->limits - $count;
        if ($left < 0) {
            $left = 0;
        }
        return $left;
    }

    public function getHadChanceUsers()
    {
        $users = $this->redis->hGetAll($this->redisKeyUsers);
        return array_keys($users);
    }
}
