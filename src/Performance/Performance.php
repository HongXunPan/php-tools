<?php

namespace HongXunPan\Tools\Performance;

class Performance
{
    public static function getNowPerformance()
    {
        return [
            'time' => microtime(true),
            'memory' => memory_get_usage(),
            'peak_memory' => memory_get_peak_usage(),
            'cpu' => function_exists('getrusage') ? getrusage() : [],
            'files' => count(get_included_files()),
        ];
    }

    public static function diffPerformance(array $before, array $after)
    {
        $diff = [];
        if (isset($after['time'])) {
            $diff['time'] = $after['time'] - (isset($before['time']) ? $before['time'] : 0);
        }
        if (isset($after['memory'])) {
            $diff['memory'] = self::bytes2Human($after['memory'] - (isset($before['memory']) ? $before['memory'] : 0));
        }
        if (isset($after['peak_memory'])) {
            $diff['peak_memory'] = self::bytes2Human($after['peak_memory'] - (isset($before['peak_memory']) ? $before['peak_memory'] : 0));
        }
        if (isset($after['cpu']) && isset($before['cpu'])) {
            $diff['cpu'] = [];
            foreach ($after['cpu'] as $name => $value) {
                $beforeValue = isset($before['cpu'][$name]) ? $before['cpu'][$name] : 0;
                $diff['cpu'][$name] = $value - $beforeValue;
            }
        }
        if (isset($after['files'])) {
            $diff['files'] = $after['files'] - (isset($before['files']) ? $before['files'] : 0);
        }
        return $diff;
    }

    public static function bytes2Human($filesize)
    {
        if ($filesize >= 1073741824) {
            //转成GB
            $filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
        } elseif ($filesize >= 1048576) {
            //转成MB
            $filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
        } elseif ($filesize >= 1024) {
            //转成KB
            $filesize = round($filesize / 1024 * 100) / 100 . ' KB';
        } else {
            //不转换直接输出
            $filesize = $filesize . ' bytes';
        }
        return $filesize;
    }
}
