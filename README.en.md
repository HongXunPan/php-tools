[简体中文](README.md) | **English**

# PHP Tools

[![PHP Compatibility](https://github.com/HongXunPan/php-tools/actions/workflows/php-compatibility.yml/badge.svg)](https://github.com/HongXunPan/php-tools/actions/workflows/php-compatibility.yml)
[![Latest Stable Version](https://img.shields.io/packagist/v/hongxunpan/php-tools.svg)](https://packagist.org/packages/hongxunpan/php-tools)
[![PHP Version](https://img.shields.io/packagist/dependency-v/hongxunpan/php-tools/php.svg)](https://packagist.org/packages/hongxunpan/php-tools)
[![License](https://img.shields.io/packagist/l/hongxunpan/php-tools.svg)](LICENSE)

`hongxunpan/php-tools` is a reusable foundation library for PHP `>=5.6`. It focuses on stable compatibility, clear responsibilities, and reuse across projects without depending on an application container.

## Installation and Runtime Requirements

```bash
composer require hongxunpan/php-tools
```

- PHP `>=5.6`;
- PHP 5.6 environments should use Composer 2.2 LTS;
- GitHub Actions continuously verifies PHP `5.6`, `7.4`, `8.0`, and `8.5`.

## Version Support

| Package version | Maintenance status | PHP requirement |
| --- | --- | --- |
| `3.x` | Actively maintained | PHP `>=5.6` |
| `2.x` | Legacy compatibility line; issues are evaluated by impact | See the constraint of the relevant release |
| `<2.0` | No longer maintained | Not applicable |

PHP `>=5.6` is a formal compatibility boundary of this project. It will not be narrowed by splitting the package into separate modern and legacy runtime lines. Production users should still choose a PHP runtime that receives upstream security maintenance whenever their application constraints allow it.

## Capability Index

| Category | Documentation |
| --- | --- |
| Logging | [Log](readme/log.md) |
| Redis coordination | [RedisLock](readme/redis-lock.md) · [RedisDraw](readme/redis-draw.md) · [RedisTimeLimitOffers](readme/redis-time-limit-offers.md) |
| Encryption | [OpensslEncrypt](readme/openssl-encrypt.md) |
| Files and CLI | [GetDirFiles](readme/get-dir-files.md) · [Progress](readme/cli-progress.md) |
| Other public classes | `Cache` · `List2Tree` · `Tree2List` · `Performance` |

## Upgrading to 3.x

Version 3.0 is a breaking cleanup release. Before upgrading, review the [changelog](CHANGELOG.md), identify removed capabilities, and run the consumer application's regression suite.

## Local Verification

```bash
composer validate --strict
composer lint
composer test
```

PHP 5.6 syntax and runtime compatibility are enforced by GitHub Actions. Passing on a single newer PHP version does not replace the full compatibility matrix.

## Community and Support

- Read the [support policy](SUPPORT.en.md) before filing a bug or feature request;
- Follow the [contribution guide](CONTRIBUTING.en.md) when proposing changes;
- Report vulnerabilities privately according to the [security policy](SECURITY.en.md);
- Participation in this repository is governed by the [Code of Conduct](CODE_OF_CONDUCT.en.md);
- User-visible changes are recorded in the [changelog](CHANGELOG.md).

## License

This project is open source under the [MIT License](LICENSE).
