<?php

use HongXunPan\Tools\Encrypt\OpensslEncrypt;
use HongXunPan\Tools\Log\Log;
use Psr\Log\LoggerInterface;

function testRemovedCapabilities()
{
    $removedClasses = [
        'HongXunPan\\Tools\\ElasticSearch\\ElasticSearch',
        'HongXunPan\\Tools\\Notice\\DingTalk',
        'HongXunPan\\Tools\\ValueShare\\Ftok',
        'HongXunPan\\Tools\\RateLimit\\RedisRateLimit',
        'HongXunPan\\Tools\\ServerMonitor\\Dashboard',
        'HongXunPan\\Tools\\ServerProbe\\Server',
        'HongXunPan\\Tools\\DB\\QueryBuilder',
        'HongXunPan\\Tools\\Model\\ModelUtils',
        'HongXunPan\\Tools\\Exception\\Enums\\EnumException',
        'HongXunPan\\Tools\\SSE\\SSETrait',
        'HongXunPan\\Tools\\Common\\SetPath',
        'HongXunPan\\Tools\\Config\\Config',
        'HongXunPan\\Tools\\Env\\Env',
        'HongXunPan\\Tools\\Event\\Event',
        'HongXunPan\\Tools\\Event\\EventDispatcher',
        'HongXunPan\\Tools\\Event\\EventSubscriber',
    ];

    foreach ($removedClasses as $removedClass) {
        assertTrueValue(!class_exists($removedClass) && !trait_exists($removedClass), '已移除能力仍可被加载：' . $removedClass);
    }
}

function testLogPsrCompatibility()
{
    $directory = makeTestDirectory('log');
    try {
        $logger = Log::getInstance();
        assertTrueValue($logger instanceof LoggerInterface, 'Log 必须实现 PSR-3 LoggerInterface');
        $logger->setLogPath($directory);
        $logger->info('基础日志', ['id' => 1]);
        Log::channel('test')->warning('频道日志');

        $day = date('Y-m-d');
        assertTrueValue(is_file($directory . DIRECTORY_SEPARATOR . 'info-' . $day . '.log'), '基础日志文件未生成');
        assertTrueValue(is_file($directory . DIRECTORY_SEPARATOR . 'test-' . $day . '.log'), '频道日志文件未生成');
    } finally {
        removeTestDirectory($directory);
    }
}

function testOpenSslCompatibility()
{
    $legacyCipher = OpensslEncrypt::encrypt('兼容数据');
    assertTrueValue(is_string($legacyCipher), '静态兼容加密失败');
    assertSameValue('兼容数据', OpensslEncrypt::decrypt($legacyCipher), '静态兼容解密失败');

    $encryptor = OpensslEncrypt::setConfig('custom-key', '1234567890123456');
    $cipher = $encryptor->encrypt('自定义配置');
    assertSameValue('自定义配置', $encryptor->decrypt($cipher), '实例配置加解密失败');

    if (version_compare(PHP_VERSION, OpensslEncrypt::MIN_PHP_VERSION_FOR_AEAD, '<')) {
        assertThrows(
            'RuntimeException',
            function () {
                OpensslEncrypt::encryptFixedLengthWithNonceTag('AEAD');
            },
            'PHP 5.6 调用 AEAD 时必须明确失败'
        );
        return;
    }

    $aeadCipher = $encryptor->encryptFixedLengthWithNonceTag('AEAD');
    assertTrueValue(is_string($aeadCipher), 'AEAD 加密失败');
    assertSameValue('AEAD', $encryptor->decryptFixedLengthWithNonceTag($aeadCipher), 'AEAD 解密失败');
}

return [
    '已移除能力不可加载' => 'testRemovedCapabilities',
    'PSR 日志兼容' => 'testLogPsrCompatibility',
    'OpenSSL 加解密兼容' => 'testOpenSslCompatibility',
];
