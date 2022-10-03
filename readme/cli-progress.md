## cli/Progress (\HongXunPan\Tools\Cli\Progress)

作用于 cli 模式下的进度条显示，解决 cli 脚本执行时间过长而无法判断是否卡住

### 使用方法

####  常规

`\HongXunPan\Tools\Cli\Progress::echoCliProgress($current, $total);`
`\HongXunPan\Tools\Cli\Progress::endCliProgress();`

示例：

```php
$count = 1000;

for ($i = 1; $i <= $count; $i++) {

    \HongXunPan\Tools\Cli\Progress::echoCliProgress($i, $count);
    ... do your jobs
}
\HongXunPan\Tools\Cli\Progress::endCliProgress();//如果没执行到这行代码光标闪烁会不见

```

控制台输出:

```bash
[root@7ee7e1d16bd9 php-tools]# 
[root@7ee7e1d16bd9 php-tools]# php test1.php 
[####################################################################################################] [1000/1000] [100.00%]

[root@7ee7e1d16bd9 php-tools]# 

```

#### curl 下载文件进度条

- 直接下载

```php
$result =  (new \HongXunPan\Tools\Cli\Progress())->curlDownloadProgress($url, $savePath, $saveName = '', $unit = 'kb', );
var_dump($result);
```

- 自行下载

```php
$fp = fopen($saveFile, 'wb'); // 本地文件保存路径
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_NOPROGRESS, false);
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, '\HongXunPan\Tools\Cli\Progress::downloadProgress');

echo "Downloading \033[1;31m" . $url . "\033[0m to \033[1;32m$saveFile\033[0m \n";

$response = curl_exec($ch);

curl_close($ch);

fclose($fp);
\HongXunPan\Tools\Cli\Progress::endCliProgress();
```