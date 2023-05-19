## file/GetDirFiles (\HongXunPan\Tools\File\GetDirFiles)

作用于 cli 模式下的进度条显示，解决 cli 脚本执行时间过长而无法判断是否卡住

### 使用方法

#### 常规

`$res = \HongXunPan\Tools\File\GetDirFiles::getFilesByPath(__DIR__.'/test/', \HongXunPan\Tools\File\GetDirFiles::GET_TYPE_ALL, 0, 3);`

示例：

```php
$res = \HongXunPan\Tools\File\GetDirFiles::getFilesByPath(__DIR__.'/test/', \HongXunPan\Tools\File\GetDirFiles::GET_TYPE_ALL, 0, 3);
```

```json
[
  {
    "name": "test1.1",
    "type": 1,
    "children": [],
    "depth": 1
  },
  {
    "name": "test1.2",
    "type": 1,
    "children": [],
    "depth": 1
  },
  {
    "name": "test1.3",
    "type": 1,
    "children": [],
    "depth": 1
  },
  {
    "name": "test1.4",
    "type": 1,
    "children": [],
    "depth": 1
  }
]
```