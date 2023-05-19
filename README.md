## php 工具包

### install 

`composer require hongxunpan/php-tools`

### usage

- [cli/Progress](readme/cli-progress.md) cli 终端下的任务进度显示
- [validate/Validator](readme/validate-validator.md) 验证器
- [lock/RedisLock](readme/redis-lock.md) redis 分布独占锁
- [draw/RedisDraw](readme/redis-draw.md) redis 抽奖
- [timeLimitOffers/RedisTimeLimitOffers](readme/redis-time-limit-offers.md) redis 抢购名额
- [notice/DingTalk] 钉钉消息推送
- [file/GetDirFiles](readme/get-dir-files.md) 扫描文件夹下的文件

### update-log

 - `2.3.0` 2023-05-19 add get-dir-files
 - `2.2.0` 2023-02-20 add ServerMonitor & notice Interface
 - `2.1.1` 2022-12-20 redis-lock transaction callable  
 - `2.1.0` 2022-10-16 add redis-time-limit-offers  
 - `2.0.0` 2022-10-13 separate out the db-connection  
 - `1.4.0` 2022-10-11 add mysql pdo connect & abstract db connect
 - `1.3.2` 2022-10-08 add function isUserInPool
 - `1.3.1` 2022-10-06 fix high draw bugs
 - `1.3.0` 2022-10-06 add redis-draw
 - `1.2.0` 2022-10-05 add redis && redis-lock
 - `1.1.0` 2022-10-04 add validator
 - `1.0.1` 2022-10-03 add readme doc
 - `1.0.0` 2022-10-03 cli/progress

### todo

- Monolog
- Server Ping/Pong