<?php

namespace HongXunPan\Tools\File;

use Exception;

class GetDirFiles
{
    const GET_TYPE_ALL = 0;
    const GET_TYPE_DIR_ONLY = 1;
    const GET_TYPE_FILE_ONLY = 2;

    const TYPE_DIR = 0;
    const TYPE_FILE = 1;

    public static function getFilesByPath($path, $returnFullPath = false, $getType = self::GET_TYPE_ALL, $depth = -1)
    {
        if (!file_exists($path)) {
            throw new Exception('path:' . $path . 'does not exists');
        }
        static $currentDepth = 1;
        $file = [
            'name' => '',
            'type' => 0,
            'children' => [],
            'depth' => $currentDepth,
        ];
        if ($currentDepth > $depth && $depth != -1) {
            return [];
        }
        if (!is_dir($path)) {
            $pathInfo = pathinfo($path);
            $file['name'] = $returnFullPath ? $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['basename'] : $pathInfo['basename'];
            $file['type'] = self::TYPE_FILE;
            return $file;
        } else {
            $childrenPath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*';
            $children = glob($childrenPath);
            $return = [];
            foreach ($children as $child) {
                $pathInfo = pathinfo($child);
                $file['name'] = $returnFullPath ? $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['basename'] : $pathInfo['basename'];
                $file['type'] = is_dir($file['name']) ? self::TYPE_DIR : self::TYPE_FILE;
                $currentDepth++;
                $file['children'] = self::getFilesByPath($child, $returnFullPath, $getType, $depth);
                $currentDepth--;
                $return[] = $file;
            }
            return $return;
        }
    }
}
