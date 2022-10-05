## validate-validator (\HongXunPan\Tools\Validate\Validator)

一款仿制 laravel 验证规则的验证器

### 使用方法

- 得到验证结果

```php
$array = ['test' => 1, 'string' => 'string', 'aa' => '2123a'];

$rules = [
    'aa' => 'int',
    'test' => 'required|in:[2,3]',
];

$result = \HongXunPan\Tools\Validate\Validator::validate($array, $rules);

var_dump($result);

```

结果 result

```code
array(2) {
  ["count"]=>                           //错误数
  int(2)
  ["errors"]=>                          //错误原因
  array(2) {
    [0]=>
    string(18) "test must in [2,3]"
    [1]=>
    string(14) "aa must be int"
  }
}
```

- 直接抛出错误

```php
$array = ['test' => 1, 'string' => 'string', 'aa' => '2123a'];

$rules = [
    'aa' => 'int',
    'test' => 'required|in:[2,3]',
];

$result = \HongXunPan\Tools\Validate\Validator::validateOrThrow($array, $rules, true); //可选参数 true只抛出第一个错误，默认抛出全部错误
```

结果 result

```code
验证通过返回 true
错误则直接抛出异常
PHP Fatal error:  Uncaught Exception: "test must in [2,3]" in /data/wwwroot/HongXunPan/php-tools/src/Validate/Validator.php:52
Stack trace:
#0 /data/wwwroot/HongXunPan/php-tools/testValidate.php(16): HongXunPan\Tools\Validate\Validator::validateOrThrow()

```

### 贡献者 

更多规则待完善,欢迎提交PR

- [HongXunPan](https://github.com/HongXunPan/)