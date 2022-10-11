## db-mysql (\HongXunPan\Tools\Db\Mysql\Mysql)

mysql 连接

### 使用方法

#### 传入 mysql 连接配置

```php
$config = [
    'host' => '192.168.65.2',
    'port' => 3306,
    'username' => 'default',
    'password' => 'secret',
];

\HongXunPan\Tools\Db\Mysql\Mysql::setConfig($config);
```

default config
```code
$default = [
    'host' => '127.0.0.1',
    'port' => 3306,
    'username' => '',
    'password' => '',
];
```

#### 调用

```php
/** @var \PDO $res */
$res = \HongXunPan\Tools\Db\Mysql\Mysql::getConnection(); 
$res = \HongXunPan\Tools\Db\Mysql\Mysql::connection()->getConnection();
var_dump($res);
```

`getConnection() 用于代码提示，调试完成可删除。或者用以下方法调用`