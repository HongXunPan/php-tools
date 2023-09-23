<?php

namespace app\helpers;

class Sort
{
    /**
     * 根据指定数组排序
     *
     * @param array       $data 原始数据，一维数组或二维数组
     * @param array       $sort 需要按照此一维数组排序
     * @param string|null $key  $data 为二维数组时，根据 key 匹配 $sort
     *
     * @return array
     */
    public static function byArray(array $data, array $sort, ?string $key = ''): array
    {
        if (empty($data)) {
            return [];
        }
        if (empty($sort)) {
            return $data;
        }

        $haystack = [];
        // 一维数组
        if (count($data) != count($data, true)) {
            if (empty($key)) {
                return [];
            }
            foreach ($data as $k => $item) {
                $haystack[$k] = $item[$key];
            }
        } else {
            $haystack = $data;
        }

        if (!$haystack) {
            return [];
        }

        $sortAfter = [];
        foreach ($sort as $v) {
            if ($v === null) {
                continue;
            }

            $index = array_search($v, $haystack);
            if ($index === false) {
                continue;
            }
            $sortAfter[] = $data[$index];
            unset($data[$index]);
            // 数据中有重复值时会有影响
            unset($haystack[$index]);
        }

        return array_merge($sortAfter, $data);
    }
}
