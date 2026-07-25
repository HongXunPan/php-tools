# OpensslEncrypt（`HongXunPan\Tools\Encrypt\OpensslEncrypt`）

提供兼容历史消费者的 OpenSSL 加解密能力。静态调用和配置实例调用均可用。

## 新代码推荐写法

新项目必须从环境变量或安全配置获取密钥与 IV，不要依赖包内历史默认值：

```php
use HongXunPan\Tools\Encrypt\OpensslEncrypt;

$encryptor = OpensslEncrypt::setConfig(
    getenv('APP_ENCRYPT_KEY'),
    getenv('APP_ENCRYPT_IV')
);

$cipherText = $encryptor->encrypt('plain text');
$plainText = $encryptor->decrypt($cipherText);
```

## 历史兼容写法

```php
$cipherText = OpensslEncrypt::encrypt('plain text');
$plainText = OpensslEncrypt::decrypt($cipherText);
```

静态写法继续保留，以避免已存量密文无法解密；它不应成为新项目的配置方式。

## AEAD

PHP 7.1 及以上可使用带 nonce 与 tag 的 AEAD 能力：

```php
$cipherText = $encryptor->encryptFixedLengthWithNonceTag(
    'plain text',
    OpensslEncrypt::AES_256_GCM
);

$plainText = $encryptor->decryptFixedLengthWithNonceTag(
    $cipherText,
    OpensslEncrypt::AES_256_GCM
);
```

## 安全边界

- 包内历史默认密钥与 IV 仅用于兼容既有密文，不代表安全的新项目方案；
- 不要直接更换已有数据使用的密钥、IV、算法或密文格式，否则历史数据会失去可解密性；
- 密钥轮换应采用“版本化密文 + 旧版本可解密 + 新写入使用新版本 + 渐进迁移”的独立方案；
- 解密失败返回 `false`，调用方必须显式处理。
