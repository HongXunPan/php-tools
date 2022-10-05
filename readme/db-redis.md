## db-redis (\HongXunPan\Tools\Db\Redis\Redis)

redis 连接

### 使用方法

#### 传入 redis 连接配置

```php
$config = ['host' => '192.168.0.1'];
\HongXunPan\Tools\Db\Redis\Redis::setConfig($config);//array $config = [], $connectName = 'default', array $options = []
```

default config
```code
$default = [
    'host' => '127.0.0.1',
    'port' => 6379,
    'timeout' => 0.0,
    'reserved' => null,
    'retryInterval' => 0,
    'readTimeout' => 0.0
];
```

#### 调用

```code
$res = \HongXunPan\Tools\Db\Redis\Redis::connection()->set('test', 'test');
$res = \HongXunPan\Tools\Db\Redis\Redis::connection()->getRedis()->set('test', 'test1');
$res = \HongXunPan\Tools\Db\Redis\Redis::connection()->incr('testIncr');
var_dump($res);
```
`getRedis() 用于代码提示，调试完成可删除。或者用以下方法调用`
```code
/** @var \Redis $redis */
$redis = \HongXunPan\Tools\Db\Redis\Redis::connection('connectName');
$redis->set();
$redis->get();
```