# 日志（`HongXunPan\Tools\Log\Log`）

提供轻量文件日志与 PSR-3 Logger，默认保持历史文本格式，也可显式切换为单行 JSONL。

## 基础用法

```php
use HongXunPan\Tools\Log\Log;

$logger = Log::getInstance();
$logger->setLogPath('/path/to/logs');
$logger->info('任务完成', ['task_id' => 1001]);

Log::channel('payment')->warning('支付结果待确认', ['order_id' => 2001]);
```

`setLogPath()` 会在目录不存在时尝试创建目录，并要求目录可写。没有显式设置路径时，日志写入包目录相邻的历史默认 `logs/` 路径。

## JSONL 与公共上下文

现有 `Log::channel(...)` 与 PSR-3 调用无需迁移。项目如需单行 JSONL，可在启动 Provider 中显式配置：

```php
use HongXunPan\Tools\Log\Log;
use HongXunPan\Tools\Log\LogContextProvider;

final class RequestLogContextProvider implements LogContextProvider
{
    public function context()
    {
        return [
            'request_id' => 'request-id',
        ];
    }
}

$logger = Log::getInstance();
$logger->setLogPath('/path/to/logs');
$logger->useJsonLines();
$logger->addContextProvider(new RequestLogContextProvider());
```

JSONL 固定包含 `timestamp`、`level`、`channel`、`message` 和 `context`。Context Provider 返回的项目级追踪字段位于顶层，调用方原有 context 原样保留在 `context`。

## Provider 规则

- 可以注册多个 `LogContextProvider`；
- 使用 Provider 完整类名去重，并按首次注册顺序执行；
- 同类重复注册只替换实例，不改变顺序；
- 后执行的 Provider 覆盖同名字段；
- 单个 Provider 抛出异常或返回非数组时，会写入 `error_log()` 并继续执行其余 Provider；
- Provider 只应读取当前上下文，不应在 `context()` 内再次写日志。

可使用以下方法维护 Provider：

```php
$logger->addContextProvider($provider);
$logger->removeContextProvider(RequestLogContextProvider::class);
$logger->clearContextProviders();
```

## 写入失败处理

文件写入使用 `FILE_APPEND | LOCK_EX` 并检查实际写入字节数。写入失败默认以 `[php-tools:log-write-failed]` 标记回退到 `error_log()`，且不包含原日志正文，避免在备用通道重复扩散业务数据。

测试或项目监控可以通过 `setWriteFailureHandler()` 接管失败通知：

```php
$logger->setWriteFailureHandler(function (array $failure) {
    // 将失败元数据转交给项目监控；不要在这里再次调用同一日志实例。
});
```

失败处理器用于观察写入异常，不会改变原日志调用的返回值。
