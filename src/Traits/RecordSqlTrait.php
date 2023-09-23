<?php
/**
 * 记录sql日志
 * User: Young
 * Date: 2021/4/14
 * Time: 18:17
 */

namespace app\traits\orm\boot;

use app\helpers\BacktraceHelper;
use app\helpers\RecordSqlHelper;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;

trait RecordSql
{
    /**
     * @var bool
     */
    private static $bootedRecordSql = false;

    /**
     * 启动
     */
    final public static function bootRecordSql()
    {
        // 只需要启动一次即可
        if (self::$bootedRecordSql) {
            return;
        }

        self::$bootedRecordSql = true;
        DB::listen(function (QueryExecuted $event) {
            if (!static::shouldRecordQuery() && !static::isSlowQuery($event->time / 1000)) {
                return;
            }
            if (!$caller = BacktraceHelper::getCaller(null, [__FILE__])) {
                return;
            }

            $arr = [
                'sql' => static::replaceBindings($event),
                'connection' => $event->connectionName,
                'time' => (float)number_format($event->time / 1000, 4, '.', ''),
                'slow' => static::isSlowQuery($event->time / 1000),
                'file' => $caller['file'],
                'line' => $caller['line'],
            ];
            RecordSqlHelper::recordSql($event->connection->getDatabaseName(), $arr['sql'], $arr['time']);
            log('error', json_encode($arr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'sql');
        });
    }

    /**
     * Replace the placeholders with the actual bindings.
     *
     * @param QueryExecuted $event
     * @return string
     */
    protected static function replaceBindings($event): string
    {
        $sql = $event->sql;
        $format = $event->connection->prepareBindings($event->bindings);

        foreach ($format as $key => $binding) {
            $regex = is_numeric($key)
                ? "/\?(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/"
                : "/:{$key}(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/";

            if ($binding === null) {
                $binding = 'null';
            } elseif (!is_int($binding) && !is_float($binding)) {
                $binding = $event->connection->getPdo()->quote($binding);
            }

            $sql = preg_replace($regex, (string)$binding, $sql, 1);
        }

        return $sql;
    }

    /**
     * 慢查询？
     *
     * @param float $queryTime
     * @return bool
     */
    private static function isSlowQuery($queryTime)
    {
        return $queryTime > 2;
    }

    /**
     * 记录sql至日志文件
     *
     * @return bool
     */
    private static function shouldRecordQuery(): bool
    {
        return config_item('logging.record_query')
            || isset($_GET['debug'])
            || isset($_GET['record_query'])
            || isset($_GET['recordSql']);
    }
}
