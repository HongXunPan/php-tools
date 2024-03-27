<?php

namespace HongXunPan\Tools\Backtrace;

class BacktraceHelper
{
    /**
     * @var array 默认忽略的文件包含路径
     */
    const DEFAULT_IGNORED_PATHS = [
        'vendor',
        'system',
        'core',
    ];

    /**
     * 获取调用回溯跟踪
     *
     * @return array
     */
    public static function getBacktrace()
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        unset($backtrace[0]);
        return $backtrace;
    }

    /**
     * 获取第一个调用回溯跟踪的项目文件
     *
     * 默认忽略掉文件名包含以下路径：
     *
     * - vendor：vendor路径
     * - system：ci目录
     * - core：核心文件
     *
     * @param array $backtrace
     * @param array $ignoredPaths
     * @return array|void
     */
    public static function getCaller(array $backtrace = null, $ignoredPaths = [])
    {
        $backtrace = $backtrace ?: static::getBacktrace();
        $ignoredPaths = array_merge(static::DEFAULT_IGNORED_PATHS, $ignoredPaths);

        foreach ($backtrace as $item) {
            if (isset($item['file']) && !\HongXunPan\Tools\TreeAndList\str_contains($item['file'], $ignoredPaths)) {
                return $item;
            }
        }
    }
}
