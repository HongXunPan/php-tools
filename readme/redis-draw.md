## redis-draw (\HongXunPan\Tools\Draw\RedisDraw)

利用 redis 的 Set 类型实现的 php 随机抽奖

### 使用方法

常规抽奖

```php
\HongXunPan\Tools\Db\Redis\Redis::setConfig($config);

$draw = (new \HongXunPan\Tools\Draw\RedisDraw($drawKey));
$arr = [1,2,3,4,5,6,7,8,9,10];
$draw->addUser2Pool($arr);//添加用户到奖池
$res = $draw->getPoolUsers();
$res = $draw->draw(2, $canDuplicate = false);//抽奖，可多次执行 

```

带优先级的抽奖

```php
$arr = [1,2,3,4,5,6,7,8,9,10];
$arr1 = [1,2,10];

$key = 'draw';
$high = 'high';
$normal = 'normal';

$highDraw = new \HongXunPan\Tools\Draw\RedisDraw($high);
$highDraw->addUser2Pool($arr1);

$normalDraw = new \HongXunPan\Tools\Draw\RedisDraw($normal);
$normalDraw->addUser2Pool($arr);

//抽出执行数量的高级奖池名额，普通奖池的抽奖执行常规方式即可
$res = \HongXunPan\Tools\Draw\RedisDraw::drawByWeight($high, 11, $normal, false);
```