<?php

namespace HongXunPan\Tools\Backtrace;

class BacktraceHelper
{
    /**
     * @var array 默认忽略的文件包含路径
     */
    const DEFAULT_IGNORED_PATHS = [
        'vendor',
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
    public static function getCaller($backtrace = null, $ignoredPaths = [])
    {
        if ($backtrace === null) {
            $backtrace = static::getBacktrace();
        }
        if (!is_array($backtrace)) {
            throw new \InvalidArgumentException('backtrace must be array or null');
        }
        $ignoredPaths = array_merge(static::DEFAULT_IGNORED_PATHS, $ignoredPaths);

        foreach ($backtrace as $item) {
            if (isset($item['file']) && !static::containsIgnoredPath($item['file'], $ignoredPaths)) {
                return $item;
            }
        }
    }

    private static function containsIgnoredPath($file, array $ignoredPaths)
    {
        foreach ($ignoredPaths as $ignoredPath) {
            if ($ignoredPath !== '' && strpos($file, $ignoredPath) !== false) {
                return true;
            }
        }

        return false;
    }
}
