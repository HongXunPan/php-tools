# 更新日志

本文件记录 `hongxunpan/php-tools` 的用户可感知变化。项目采用[语义化版本](https://semver.org/lang/zh-CN/)，编排方式参考 [Keep a Changelog](https://keepachangelog.com/zh-CN/1.1.0/)。

正式版本日期与范围以 Git Tag 和提交历史为准；历史 README 中没有对应 Tag 的内部里程碑会单独说明，不再当作正式发布记录。

## [未发布]

### 新增

- 增加贡献指南、安全策略、支持说明与社区行为准则；
- 为 README、贡献指南、安全策略、支持说明与社区行为准则增加英文版本及双向语言入口；
- 增加中英文缺陷、功能和使用问题模板，以及单份中英双语 Pull Request 模板；
- 增加默认代码所有者配置。

### 调整

- 补全 Composer 英文简介、首页、关键词与支持入口元数据；
- 将 README 收口为项目门面，只保留定位、安装、兼容性、能力总导航与社区入口；
- 建立以 Git Tag 为真相源的独立更新日志。

## [3.0.1] - 2026-08-12

### 新增

- 日志支持 JSONL 格式以及项目级上下文 Provider；
- 支持按注册顺序执行多个上下文 Provider，并以完整类名去重；
- 增加日志写入失败回调和安全的 `error_log()` 回退通道。

### 修复

- 使用排他锁追加日志，并校验实际写入字节数；
- 单个上下文 Provider 异常或返回无效值时，不再中断其余 Provider；
- 失败回退不携带原始日志正文，减少敏感内容向备用通道扩散的风险。

## [3.0.0] - 2026-07-25

### 调整

- 明确 PHP `>=5.6` 为安装与运行兼容边界，并建立 PHP 5.6、7.4、8.0、8.5 验证矩阵；
- 收口通用工具包职责，兼容 `hongxunpan/db` 1.x 与 PSR-3 1.x；
- 限量名额改为 Lua 原子领取，避免并发超发。

### 移除

- 移除 ElasticSearch、DingTalk 等重型或业务渠道能力；
- 移除 `QueryBuilder`、`ModelUtils`、`EnumException`、`SSETrait` 等无稳定契约能力；
- 移除 ServerMonitor、ServerProbe、RateLimit、ValueShare 等未形成可用实现的空壳；
- 移除已由其他包承接的 Config、Env、Event 草稿和旧 Validator。

### 修复

- 修复 Redis 缓存返回值、目录扫描和树转换等核心逻辑错误；
- 保留 `OpensslEncrypt` 历史密文兼容行为，并补充显式密钥与 IV 的使用要求。

## [2.9.0] - 2026-07-01

### 新增

- `RedisLock` 增加 `attemptUserLock()` 结果对象和 `withUserLock()` 托管式接口；
- 失败分支可以读取当前竞争次数。

### 调整

- 使用 Lua 原子执行 `INCR + EXPIRE`，避免加锁计数首次写入时遗漏过期时间；
- 保留 `addUserLock()`、`addUserLockOrThrow()` 和 `transaction()` 兼容入口；
- 托管式调用使用调用方传入的过期时间。

## [2.8.0] - 2025-10-31

### 新增

- 增加 `Performance` 性能记录工具；
- 纳入自 `2.4.2` 后积累的 Cache、OpenSSL 加解密和 SSE 辅助能力。

### 历史说明

- 旧 README 曾将其中部分能力记录为 `2.5.0`、`2.6.0` 和 `2.7.0`；当前 Git 历史中不存在这些版本的 Tag，因此不将其列为独立正式发布。

## 历史未打 Tag 里程碑

以下内容用于保留代码演进线索，不代表对应版本曾完成正式 Tag 发布：

| 旧 README 版本标记 | 代码历史日期 | 主要能力 |
| --- | --- | --- |
| `2.5.0` | 2024-02 | Cache 与 `remember` |
| `2.6.0` | 2024-03 | `OpensslEncrypt` |
| `2.7.0` | 2024-05 | SSE 辅助能力，已在 3.0 移除 |

## 更早版本

| 版本 | 日期 | 主要变化 |
| --- | --- | --- |
| [2.4.2] | 2023-09-23 | 调整子类可扩展方法可见性 |
| [2.4.1] | 2023-06-30 | 修复自动加载路径 |
| [2.4.0] | 2023-06-30 | 增加 Config 与 Env |
| [2.3.0] | 2023-05-19 | 增加目录文件扫描 |
| [2.2.0] | 2023-02-20 | 增加通知接口 |
| [2.1.1] | 2022-12-20 | RedisLock 支持回调流程 |
| [2.1.0] | 2022-10-16 | 增加限量名额能力 |
| [2.0.0] | 2022-10-13 | 数据库连接拆分为独立包 |
| `1.x` | 2022-10 | 初始工具集与 Redis 能力 |

[未发布]: https://github.com/HongXunPan/php-tools/compare/3.0.1...HEAD
[3.0.1]: https://github.com/HongXunPan/php-tools/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/HongXunPan/php-tools/compare/2.9.0...3.0.0
[2.9.0]: https://github.com/HongXunPan/php-tools/compare/2.8.0...2.9.0
[2.8.0]: https://github.com/HongXunPan/php-tools/compare/2.4.2...2.8.0
[2.4.2]: https://github.com/HongXunPan/php-tools/compare/2.4.1...2.4.2
[2.4.1]: https://github.com/HongXunPan/php-tools/compare/2.4.0...2.4.1
[2.4.0]: https://github.com/HongXunPan/php-tools/compare/2.3.0...2.4.0
[2.3.0]: https://github.com/HongXunPan/php-tools/compare/2.2.0...2.3.0
[2.2.0]: https://github.com/HongXunPan/php-tools/compare/2.1.1...2.2.0
[2.1.1]: https://github.com/HongXunPan/php-tools/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/HongXunPan/php-tools/compare/2.0.0...2.1.0
[2.0.0]: https://github.com/HongXunPan/php-tools/releases/tag/2.0.0
