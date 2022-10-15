## redis-time-limit-offers (\HongXunPan\Tools\TimeLimitOffers\RedisTimeLimitOffers)

redis 抢购名额

### 使用方法

```php
\HongXunPan\DB\Redis\Redis::setConfig($redisConfig);

$userId = 9;
$secKill = new \HongXunPan\Tools\TimeLimitOffers\RedisTimeLimitOffers($userId, 10);

$res = $secKill->isHaveChance();//bool or throw
dump($res);

$res = $secKill->getChanceLeft();//获取剩余名额
dump($res);

$res = $secKill->getHadChanceUsers();//已获得名额的用户
dump($res);

//设置场次过期时间 //redis key 自动释放
$secKill->expireAt($timestamp);
$secKill->expire(60 * 30);
```