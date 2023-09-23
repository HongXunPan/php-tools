<?php
namespace app;

use Illuminate\Database\Eloquent\Builder;

/**
 * 调试组件
 *
 * 在开发过程中，对PHP变量或其它信息（如SQL）进行调试是必不可少的。
 *
 * 调试信息通常直接输出到浏览器上，在非生产环境没有任何问题，
 * 但发布到生产环境后，这是不允许的
 *
 * 调试组件在开发环境下输出信息，其它环境下什么也不输出
 */
class Debug
{
    /**
     * @var bool 调试？
     */
    private $isDebug;
    /**
     * @var string debug的key
     */
    public const DEBUG_KEY = 'APP_DEBUG';

    /**
     * Debug constructor.
     */
    public function __construct()
    {
        $this->isDebug = $this->isDebug();
    }

    /**
     * debug getter
     *
     * @return bool
     */
    public function getIsDebug():bool
    {
        return $this->isDebug;
    }

    /**
     * 是否开启debug
     *
     * @return bool
     */
    private function isDebug(): bool
    {
        return (bool)app()->env->get(static::DEBUG_KEY);
    }

    /**
     * `var_dump`
     *
     * @param mixed ...$args
     * @return $this
     */
    public function dump(...$args)
    {
        return $this->debug('dump', $args);
    }

    /**
     * `var_export`
     *
     * @param mixed ...$args
     * @return $this
     */
    public function export(...$args)
    {
        return $this->debug('export', $args);
    }

    /**
     * `exit`
     *
     * @return $this
     */
    public function end()
    {
        return $this->debug('end', [0]);
    }

    /**
     * 调试
     *
     * @param string $method
     * @param array $args
     * @return $this
     */
    protected function debug(string $method, array $args = [])
    {
        if (! $this->isDebug) {
            return $this;
        }
        echo '<pre>';

        // 调用所在文件和所在行数
        $backtraces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = $backtraces[1];
        $fileLine = $caller['file'] . ':' . $caller['line'];
        echo $method . ': ' . $fileLine, "\n", str_repeat('-', strlen($fileLine)), "\n";

        foreach ($args as $value) {
            $this->{$method . 'Internal'}($value);
        }

        echo '</pre>';
        return $this;
    }

    /**
     * `var_dump`
     *
     * @param mixed $value
     */
    protected function dumpInternal($value)
    {
        var_dump($value);
    }

    /**
     * `var_export`
     *
     * @param mixed $value
     */
    protected function exportInternal($value)
    {
        var_export($value);
    }

    /**
     * `exit`
     *
     * @param int $status
     */
    protected function endInternal(int $status)
    {
        exit($status);
    }

    /**
     * 输出绑定参数的sql
     *
     * @param Builder $query
     */
    public function getEloquentSqlWithBindings(Builder $query)
    {
        $sql = vsprintf(
            str_replace('?', '%s', $query->toSql()),
            collect($query->getBindings())->map(function ($binding) {
                return is_numeric($binding) ? $binding : "'{$binding}'";
            })->toArray()
        );
        $this->dump($sql);
        $this->end();
    }
}
