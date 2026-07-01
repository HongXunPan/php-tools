## redis-lock (\HongXunPan\Tools\Lock\RedisLock)

redis 独占锁

### 推荐用法

```php
\HongXunPan\DB\Redis\Redis::setConfig($redisConfig);
$redis = \HongXunPan\DB\Redis\Redis::connection();

$result = (new \HongXunPan\Tools\Lock\RedisLock($userId, $lockName, $redis))
    ->attemptUserLock(10);

if ($result->isRejected()) {
    return '没有取得并发独占锁，当前竞争次数：' . $result->count();
}

// do your thing
```

### 托管式写法

```php
\HongXunPan\Tools\Lock\RedisLock::withUserLock(
    ['userId' => $userId, 'lockName' => $lockName, 'redis' => $redis, 'time' => 10],
    function () {
        return 1;
    },
    function (\HongXunPan\Tools\Lock\RedisLockAttemptResult $attempt, $count) {
        return '没有取得并发独占锁，当前竞争次数：' . $count;
    }
);
```

### 兼容写法

```php
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
\HongXunPan\Tools\Lock\RedisLock::transaction($lockConfig, function () {
    return 1;
});
```

### 说明

- `attemptUserLock()` 会返回 `RedisLockAttemptResult`，既能判断是否拿到锁，也能读取当前竞争次数；
- `withUserLock()` 是推荐的新代码入口，负责托管加锁、释放与失败分支；
- `withUserLock()` 的失败回调会收到 `$attempt` 与 `$count` 两个参数，按需取用；
- `addUserLock()`、`addUserLockOrThrow()`、`transaction()` 继续保留，用于兼容历史调用；
- 当前底层使用 Lua 保证 `INCR + EXPIRE` 原子执行；
- `count` 表示当前 TTL 窗口内的竞争次数，不代表真实同时并发数。
