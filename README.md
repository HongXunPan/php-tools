**简体中文** | [English](README.en.md)

# PHP 工具包

[![PHP 兼容性](https://github.com/HongXunPan/php-tools/actions/workflows/php-compatibility.yml/badge.svg)](https://github.com/HongXunPan/php-tools/actions/workflows/php-compatibility.yml)
[![Packagist 稳定版本](https://img.shields.io/packagist/v/hongxunpan/php-tools.svg)](https://packagist.org/packages/hongxunpan/php-tools)
[![PHP 版本](https://img.shields.io/packagist/dependency-v/hongxunpan/php-tools/php.svg)](https://packagist.org/packages/hongxunpan/php-tools)
[![许可证](https://img.shields.io/packagist/l/hongxunpan/php-tools.svg)](LICENSE)

`hongxunpan/php-tools` 是面向 PHP `>=5.6` 的通用基础工具包，强调稳定兼容、职责清晰和跨项目复用，不依赖具体业务容器。

## 安装与运行要求

```bash
composer require hongxunpan/php-tools
```

- PHP `>=5.6`；
- PHP 5.6 环境应使用 Composer 2.2 LTS 安装依赖；
- GitHub Actions 持续验证 PHP `5.6`、`7.4`、`8.0` 和 `8.5`。

## 版本支持

| 包版本 | 维护状态 | PHP 要求 |
| --- | --- | --- |
| `3.x` | 当前维护版本 | PHP `>=5.6` |
| `2.x` | 历史兼容版本，按问题影响评估 | 以对应版本约束为准 |
| `<2.0` | 不再维护 | 不适用 |

PHP `>=5.6` 是当前项目的正式支持范围，不会通过拆分现代版与兼容版来缩减这一边界。生产环境仍应在业务兼容范围内选用受上游安全维护的 PHP 运行时。

## 能力导航

| 分类 | 文档入口 |
| --- | --- |
| 日志 | [Log](readme/log.md) |
| Redis 协作 | [RedisLock](readme/redis-lock.md) · [RedisDraw](readme/redis-draw.md) · [RedisTimeLimitOffers](readme/redis-time-limit-offers.md) |
| 加解密 | [OpensslEncrypt](readme/openssl-encrypt.md) |
| 文件与命令行 | [GetDirFiles](readme/get-dir-files.md) · [Progress](readme/cli-progress.md) |
| 其他公共类 | `Cache` · `List2Tree` · `Tree2List` · `Performance` |

## 升级到 3.x

3.0 是破坏性清理版本。升级前应先阅读[更新日志](CHANGELOG.md)，核对已移除能力并完成调用方回归。

## 本地验证

```bash
composer validate --strict
composer lint
composer test
```

PHP 5.6 语法与运行兼容性由 GitHub Actions 承担。单一高版本环境通过不能替代完整兼容矩阵。

## 社区与支持

- 提交缺陷或建议前请阅读[支持说明](SUPPORT.md)；
- 贡献代码请遵循[参与贡献](CONTRIBUTING.md)；
- 安全漏洞请按[安全策略](SECURITY.md)私下报告；
- 参与仓库互动即表示同意[社区行为准则](CODE_OF_CONDUCT.md)；
- 所有用户可感知变化统一记录在[更新日志](CHANGELOG.md)。

## 许可证

本项目按 [MIT License](LICENSE) 开源。
