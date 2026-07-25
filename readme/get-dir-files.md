# GetDirFiles（`HongXunPan\Tools\File\GetDirFiles`）

按目录层级扫描文件和子目录。

## 参数

```php
GetDirFiles::getFilesByPath($path, $returnFullPath, $getType, $depth);
```

- `$path`：目标文件或目录；
- `$returnFullPath`：`true` 返回完整路径，`false` 只返回名称；
- `$getType`：`GET_TYPE_ALL`、`GET_TYPE_DIR_ONLY` 或 `GET_TYPE_FILE_ONLY`；
- `$depth`：`-1` 表示不限层级，`0` 表示不读取目录内容，正整数表示最大读取层级。

## 示例

```php
use HongXunPan\Tools\File\GetDirFiles;

$tree = GetDirFiles::getFilesByPath(
    __DIR__ . '/fixtures',
    false,
    GetDirFiles::GET_TYPE_ALL,
    3
);

$files = GetDirFiles::getFilesByPath(
    __DIR__ . '/fixtures',
    true,
    GetDirFiles::GET_TYPE_FILE_ONLY,
    -1
);
```

返回项包含：

```php
[
    'name' => 'example.txt',
    'type' => GetDirFiles::TYPE_FILE,
    'children' => [],
    'depth' => 1,
]
```

文件模式返回扁平文件列表；目录模式保留目录树。符号链接按文件项处理，不继续递归，避免目录循环。
