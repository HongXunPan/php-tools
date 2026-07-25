# PHP 工具包

`hongxunpan/php-tools` 提供不依赖业务容器的通用 PHP 工具。3.0 线以 **PHP 5.6 可安装、PHP 8.x 可运行** 为兼容边界。

## 安装

```bash
composer require hongxunpan/php-tools
```

## 运行要求

- PHP `>=5.6`；
- 使用日志能力时遵循 PSR-3 1.x 契约；
- Redis 工具默认可使用 `hongxunpan/db` 的 Redis 连接，也允许注入兼容的 Redis 客户端；
- GitHub Actions 持续验证 PHP `5.6`、`7.4`、`8.0`、`8.5`。

## 能力索引

- `Log`：轻量文件日志与 PSR-3 Logger；
- `Cache`：Redis 缓存辅助；
- [RedisLock](readme/redis-lock.md)：Redis 分布式独占锁；
- [RedisDraw](readme/redis-draw.md)：Redis 抽奖；
- [RedisTimeLimitOffers](readme/redis-time-limit-offers.md)：Redis 限量名额；
- [Validator](readme/validate-validator.md)：轻量数据验证；
- [OpensslEncrypt](readme/openssl-encrypt.md)：兼容历史密文的 OpenSSL 加解密；
- [GetDirFiles](readme/get-dir-files.md)：目录扫描；
- [Progress](readme/cli-progress.md)：CLI 进度显示与下载进度。

## 3.0 变更边界（待发布）

3.0 是破坏性清理版本：

- 移除 ElasticSearch、DingTalk，避免通用工具包携带重型或业务渠道依赖；
- 移除无完整实现或无稳定契约的 `QueryBuilder`、`ModelUtils`、`EnumException`、`SSETrait`；
- 移除未形成可用能力的 ServerMonitor、ServerProbe、RateLimit、ValueShare 等空壳；
- 移除已归 simple-framework core 的 Config / Env，以及已由 simple-event 承接的历史 Event 草稿；
- 保留 `OpensslEncrypt` 的历史默认行为和密文格式，但新项目必须显式配置密钥与 IV；
- 修复 Redis 缓存返回值、目录扫描、树转换、验证器等实际逻辑错误；
- 限量名额改为 Lua 原子领取，避免并发超发。

升级前应先搜索项目对已移除类的直接引用；旧项目可继续锁定 2.x，新项目与完成迁移的项目再接入 3.x。

## 本地验证

```bash
composer validate --strict
composer lint
composer test
```

PHP 5.6 语法与运行兼容性由 GitHub Actions 承担，本地开发环境无需额外拉取旧版 PHP 镜像。

## 更新记录

- `3.0.0` 待发布：PHP 5.6 兼容回退、废弃能力清理、核心逻辑修复与兼容矩阵；
- `2.8.0` 2024-05-24：Performance；
- `2.7.0` 2024-05-24：SSE supporter；
- `2.6.0` 2024-03-06：OpensslEncrypt；
- `2.5.0` 2024-02-18：Cache remember；
- `2.4.0` 2023-06-30：Config 与 Env；
- `2.3.0` 2023-05-19：GetDirFiles；
- `2.0.0` 2022-10-13：拆分数据库连接。
