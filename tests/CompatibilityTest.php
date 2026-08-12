<?php

use HongXunPan\Tools\Encrypt\OpensslEncrypt;
use HongXunPan\Tools\Log\Log;
use HongXunPan\Tools\Log\LogContextProvider;
use Psr\Log\LoggerInterface;

class FirstTestLogContextProvider implements LogContextProvider
{
    private $context;

    public function __construct(array $context)
    {
        $this->context = $context;
    }

    public function context()
    {
        return $this->context;
    }
}

class SecondTestLogContextProvider implements LogContextProvider
{
    public function context()
    {
        return [
            'scope' => 'second',
            'second_id' => 'second-id',
        ];
    }
}

class FailingTestLogContextProvider implements LogContextProvider
{
    public function context()
    {
        throw new RuntimeException('预期的 Context Provider 异常');
    }
}

class InvalidTestLogContextProvider implements LogContextProvider
{
    public function context()
    {
        return 'invalid';
    }
}

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
        'HongXunPan\\Tools\\Validate\\Validator',
    ];

    foreach ($removedClasses as $removedClass) {
        assertTrueValue(!class_exists($removedClass) && !trait_exists($removedClass), '已移除能力仍可被加载：' . $removedClass);
    }
}

function testPhp56ReservedMethodDeclarations()
{
    $roots = [dirname(__DIR__) . '/src', __DIR__];
    $reservedNames = ['array', 'eval'];

    foreach ($roots as $root) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($files as $file) {
            if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') {
                continue;
            }

            $expectFunctionName = false;
            foreach (token_get_all(file_get_contents($file->getPathname())) as $token) {
                if (is_array($token) && $token[0] === T_FUNCTION) {
                    $expectFunctionName = true;
                    continue;
                }
                if (!$expectFunctionName) {
                    continue;
                }
                if (is_array($token) && $token[0] === T_WHITESPACE) {
                    continue;
                }
                if ($token === '&') {
                    continue;
                }
                if ($token === '(') {
                    $expectFunctionName = false;
                    continue;
                }

                $functionName = is_array($token) ? strtolower($token[1]) : strtolower($token);
                assertTrueValue(
                    !in_array($functionName, $reservedNames, true),
                    '发现 PHP 5.6 保留方法名：' . $functionName . ' @ ' . $file->getPathname()
                );
                $expectFunctionName = false;
            }
        }
    }
}

function testLogPsrCompatibility()
{
    $directory = makeTestDirectory('log');
    try {
        $logger = Log::getInstance();
        assertTrueValue($logger instanceof LoggerInterface, 'Log 必须实现 PSR-3 LoggerInterface');
        $logger->setLogPath($directory);
        $logger->useJsonLines(false);
        $logger->clearContextProviders();
        $logger->setWriteFailureHandler(null);
        $logger->info('基础日志', ['id' => 1]);
        Log::channel('test')->warning('频道日志');

        $day = date('Y-m-d');
        assertTrueValue(is_file($directory . DIRECTORY_SEPARATOR . 'info-' . $day . '.log'), '基础日志文件未生成');
        assertTrueValue(is_file($directory . DIRECTORY_SEPARATOR . 'test-' . $day . '.log'), '频道日志文件未生成');
    } finally {
        removeTestDirectory($directory);
    }
}

function testLogJsonLinesAndOrderedContextProviders()
{
    $directory = makeTestDirectory('log-jsonl');
    $providerErrorLog = $directory . DIRECTORY_SEPARATOR . 'provider-error.log';
    $previousErrorLog = ini_get('error_log');
    try {
        $logger = Log::getInstance();
        $logger->setLogPath($directory);
        $logger->useJsonLines();
        $logger->clearContextProviders();
        $logger->addContextProvider(new FirstTestLogContextProvider([
            'scope' => 'first',
            'request_id' => 'request-jsonl',
        ]));
        $logger->addContextProvider(new FailingTestLogContextProvider());
        $logger->addContextProvider(new InvalidTestLogContextProvider());
        $logger->addContextProvider(new SecondTestLogContextProvider());
        $logger->addContextProvider(new FirstTestLogContextProvider([
            'scope' => 'first-replaced',
            'request_id' => 'request-replaced',
        ]));
        $logger->setWriteFailureHandler(null);
        ini_set('error_log', $providerErrorLog);

        Log::channel('jsonl')->info('结构化日志', ['id' => 7]);
        $logger->removeContextProvider(SecondTestLogContextProvider::class);
        Log::channel('jsonl')->info('移除 Provider 后的日志', ['id' => 8]);

        $file = $directory . DIRECTORY_SEPARATOR . 'jsonl-' . date('Y-m-d') . '.log';
        assertTrueValue(is_file($file), 'JSONL Channel 日志文件未生成');
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        assertSameValue(2, count($lines), 'JSONL 日志条数或单行边界错误');
        $record = json_decode($lines[0], true);
        $recordAfterRemove = json_decode($lines[1], true);
        assertTrueValue(is_array($record), 'JSONL 日志无法独立解析');
        assertSameValue('INFO', $record['level'], 'JSONL 日志级别错误');
        assertSameValue('jsonl', $record['channel'], 'JSONL Channel 错误');
        assertSameValue('结构化日志', $record['message'], 'JSONL message 错误');
        assertSameValue('second', $record['scope'], 'Provider 未按首次注册顺序覆盖字段');
        assertSameValue('request-replaced', $record['request_id'], '同类 Provider 未替换实例');
        assertSameValue('second-id', $record['second_id'], '后续 Provider 上下文缺失');
        assertSameValue(['id' => 7], $record['context'], '调用 context 未原样保留');
        assertSameValue(
            'first-replaced',
            $recordAfterRemove['scope'],
            '按类名移除 Provider 后仍残留上下文'
        );
        assertTrueValue(!isset($recordAfterRemove['second_id']), '已移除 Provider 仍被执行');

        $providerErrors = is_file($providerErrorLog) ? file_get_contents($providerErrorLog) : '';
        assertTrueValue(
            strpos($providerErrors, '[php-tools:log-context-failed]') !== false,
            'Provider 异常未进入独立 error_log 通道'
        );
        assertTrueValue(
            strpos($providerErrors, FailingTestLogContextProvider::class) !== false,
            'Provider 异常缺少完整类名'
        );
        assertTrueValue(
            strpos($providerErrors, '[php-tools:log-context-invalid]') !== false,
            'Provider 非数组结果未被隔离'
        );
    } finally {
        $logger = Log::getInstance();
        ini_set('error_log', $previousErrorLog);
        $logger->useJsonLines(false);
        $logger->clearContextProviders();
        $logger->setWriteFailureHandler(null);
        removeTestDirectory($directory);
    }
}

function testLogWriteFailureCanBeObserved()
{
    $directory = makeTestDirectory('log-write-failure');
    $failures = [];
    $logger = Log::getInstance();

    try {
        $logger->setLogPath($directory);
        $logger->useJsonLines();
        $logger->clearContextProviders();
        $logger->setWriteFailureHandler(function (array $details) use (&$failures) {
            $failures[] = $details;
        });
        rmdir($directory);

        Log::channel('write-failure')->error('预期写入失败');

        assertSameValue(1, count($failures), '日志写入失败未触发可观测 Handler');
        assertSameValue('write-failure', $failures[0]['channel'], '失败详情缺少 Channel');
        assertSameValue('error', $failures[0]['level'], '失败详情缺少日志级别');
        assertSameValue(null, $failures[0]['written_bytes'], '失败写入字节数错误');
        assertTrueValue($failures[0]['expected_bytes'] > 0, '失败详情缺少预期写入字节数');
    } finally {
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        $logger->setLogPath($directory);
        $logger->useJsonLines(false);
        $logger->clearContextProviders();
        $logger->setWriteFailureHandler(null);
        removeTestDirectory($directory);
    }
}

function testLogWriteFailureFallsBackToErrorLog()
{
    $directory = makeTestDirectory('log-error-fallback');
    $logDirectory = $directory . DIRECTORY_SEPARATOR . 'logs';
    $fallbackFile = $directory . DIRECTORY_SEPARATOR . 'php-error.log';
    $previousErrorLog = ini_get('error_log');
    $logger = Log::getInstance();

    try {
        mkdir($logDirectory, 0777, true);
        $logger->setLogPath($logDirectory);
        $logger->useJsonLines();
        $logger->clearContextProviders();
        $logger->setWriteFailureHandler(null);
        ini_set('error_log', $fallbackFile);
        rmdir($logDirectory);

        Log::channel('error-fallback')->error('预期进入备用通道');

        $fallback = is_file($fallbackFile) ? file_get_contents($fallbackFile) : '';
        assertTrueValue(
            strpos($fallback, '[php-tools:log-write-failed]') !== false,
            '默认 error_log fallback 未写入稳定标记'
        );
        assertTrueValue(
            strpos($fallback, 'channel=error-fallback') !== false,
            '默认 error_log fallback 缺少 Channel'
        );
    } finally {
        ini_set('error_log', $previousErrorLog);
        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0777, true);
        }
        $logger->setLogPath($logDirectory);
        $logger->useJsonLines(false);
        $logger->clearContextProviders();
        $logger->setWriteFailureHandler(null);
        removeTestDirectory($directory);
    }
}

function testLogWriterUsesExclusiveAppend()
{
    $source = file_get_contents(dirname(__DIR__) . '/src/Log/FileLogWriter.php');
    assertTrueValue(
        strpos($source, 'FILE_APPEND | LOCK_EX') !== false,
        '日志 Writer 未启用互斥追加'
    );
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
    'PHP 5.6 保留方法名扫描' => 'testPhp56ReservedMethodDeclarations',
    'PSR 日志兼容' => 'testLogPsrCompatibility',
    'JSONL 与有序上下文 Provider' => 'testLogJsonLinesAndOrderedContextProviders',
    '日志写入失败可观测' => 'testLogWriteFailureCanBeObserved',
    '日志写入失败默认回退' => 'testLogWriteFailureFallsBackToErrorLog',
    '日志文件互斥追加' => 'testLogWriterUsesExclusiveAppend',
    'OpenSSL 加解密兼容' => 'testOpenSslCompatibility',
];
