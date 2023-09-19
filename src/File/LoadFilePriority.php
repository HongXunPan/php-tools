<?php
namespace app\helpers;

use RuntimeException;

class FileHelper
{
    /**
     * 加载文件
     *
     * 默认加载顺序
     *
     * - `application/`
     * - `projects/<project_name>`
     *
     * @param string $path
     * @param bool $halt
     * @return int
     */
    public static function require($path, $halt = true)
    {
        $result = 0;
        foreach (app()->getAppPaths() as $item) {
            if (is_file($file = $item . $path)) {
                require_once $file;
                $result++;
            }
        }

        if (! $result && $halt) {
            throw new RuntimeException('加载文件"' . $path . '"失败');
        }
        return $result;
    }
}
