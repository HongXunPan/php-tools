## redis-lock (\HongXunPan\Tools\Lock\RedisLock)

### 使用方法

```php
\HongXunPan\Tools\Db\Redis\Redis::setConfig($redisConfig);
$redis = \HongXunPan\Tools\Db\Redis\Redis::connection();
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
```