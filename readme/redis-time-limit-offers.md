# RedisTimeLimitOffers（`HongXunPan\Tools\TimeLimitOffers\RedisTimeLimitOffers`）

使用 Redis 原子领取限量名额。

## 使用方法

```php
HongXunPan\DB\Redis\Redis::setConfig($redisConfig);

$userId = 9;
$offers = new HongXunPan\Tools\TimeLimitOffers\RedisTimeLimitOffers(
    $userId,
    10,
    '2026-summer'
);

$offers->isHaveChance();
$left = $offers->getChanceLeft();
$users = $offers->getHadChanceUsers();

$offers->expire(30 * 60);
// 或使用绝对时间：$offers->expireAt($timestamp);
```

## 并发语义

- 领取过程由单段 Lua 完成“检查用户、检查余量、计数、记录用户”，避免并发超发；
- 同一用户在同一场次重复调用具有幂等性，不重复占用名额；
- 没有剩余名额时抛出 `TimeLimitOffersException`；
- 计数键与用户键应设置相同过期时间；
- 构造函数第四个参数可注入兼容的 Redis 客户端，未注入时使用 `hongxunpan/db` 默认连接。
