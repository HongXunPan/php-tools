## redis-lock (\HongXunPan\Tools\Lock\RedisLock)

redis 独占锁

### 使用方法

```php
\HongXunPan\DB\Redis\Redis::setConfig($redisConfig);
$redis = \HongXunPan\DB\Redis\Redis::connection();
$lock = new \HongXunPan\Tools\Lock\RedisLock($userId, $lockName, $redis);
$res = $lock->addUserLock(100);//超时时间
if ($res === 1) {
    //do your thing
    $lock->clearUserLock(); //用完释放
} else {
    return '没有取得并发独占锁';
}

//or
$lock->addUserLockOrThrow(100);//没有取得锁会直接抛出异常

//or
$lockConfig = ['userId' => $userId, 'lockName' => $lockName, 'redis' => $redis, 'time' => 10];
\HongXunPan\Tools\Lock\RedisLock::transaction($lockConfig, function () use ($lockConfig) {
    //is callable
    return 1;
});

```