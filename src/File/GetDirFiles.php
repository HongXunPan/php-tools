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
        if (!in_array($getType, [self::GET_TYPE_ALL, self::GET_TYPE_DIR_ONLY, self::GET_TYPE_FILE_ONLY], true)) {
            throw new Exception('get type is invalid');
        }
        $depth = (int)$depth;
        if ($depth < -1) {
            throw new Exception('depth must be -1 or a non-negative int');
        }

        if (!is_dir($path) || is_link($path)) {
            if ($getType === self::GET_TYPE_DIR_ONLY) {
                return [];
            }

            return self::buildItem($path, $returnFullPath, self::TYPE_FILE, 1, []);
        }

        return self::scanDirectory($path, $returnFullPath, $getType, $depth, 1);
    }

    private static function scanDirectory($path, $returnFullPath, $getType, $depth, $currentDepth)
    {
        if ($depth !== -1 && $currentDepth > $depth) {
            return [];
        }

        $entries = scandir($path);
        if ($entries === false) {
            throw new Exception('path:' . $path . 'can not be scanned');
        }

        $result = [];
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $childPath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $entry;
            $isDirectory = is_dir($childPath) && !is_link($childPath);
            if ($isDirectory) {
                $children = self::scanDirectory(
                    $childPath,
                    $returnFullPath,
                    $getType,
                    $depth,
                    $currentDepth + 1
                );
                if ($getType === self::GET_TYPE_FILE_ONLY) {
                    $result = array_merge($result, $children);
                    continue;
                }
                $result[] = self::buildItem(
                    $childPath,
                    $returnFullPath,
                    self::TYPE_DIR,
                    $currentDepth,
                    $children
                );
                continue;
            }

            if ($getType !== self::GET_TYPE_DIR_ONLY) {
                $result[] = self::buildItem(
                    $childPath,
                    $returnFullPath,
                    self::TYPE_FILE,
                    $currentDepth,
                    []
                );
            }
        }

        return $result;
    }

    private static function buildItem($path, $returnFullPath, $type, $depth, array $children)
    {
        return [
            'name' => $returnFullPath ? $path : basename($path),
            'type' => $type,
            'children' => $children,
            'depth' => $depth,
        ];
    }
}
