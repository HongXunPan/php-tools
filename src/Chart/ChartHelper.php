<?php
/**
 * 图表Helper
 * 返回的字段名称目前限制为：
 * `name`、`occur_period`、`jdz`、`zs`、`value`
 *
 * User: Young
 * Date: 2022/5/21
 * Time: 15:00
 */

namespace app\helpers;

use Illuminate\Support\Collection;

class ChartHelper
{
    /**
     * 对数据进行图表格式的转换，使用一个字段进行当做"名称"字段，一个字段当做"值"字段
     * 适用于：饼图、柱状图、柱状折线图
     *
     * @param array|Collection $data 二维数据
     * @param string $nameKey 要当做`名称`的值
     * @param string $valueKey 要当做`绝对值`或`增速`的值
     * @return array
     */
    public static function singleFieldSingleValue($data, string $nameKey, string $valueKey): array
    {
        return self::multi($data, $nameKey, $valueKey);
    }

    /**
     * 对数据进行图表格式的转换，使用一个字段进行当做"名称"字段，两个字段当做"值"字段（绝对值与增速）
     * 适用于：趋势折线图
     *
     * @param array|Collection $data 二维数据
     * @param string $nameKey 要当做`名称`的值
     * @param string $valueKey 要当做`绝对值`的值
     * @param string $valueKey2 要当做`增速`的值
     * @return array
     */
    public static function singleFieldMultiValue($data, string $nameKey, string $valueKey, string $valueKey2): array
    {
        return self::multi($data, $nameKey, $valueKey, $valueKey2);
    }

    /**
     * 对数据进行图表格式的转换，使用两个字段进行当做"名称"字段（occur_period与name），两个字段当做"值"字段（绝对值与增速）
     * 适用于：多折线图
     *
     * @param array|Collection $data 二维数据
     * @param string $nameKey 要当做`名称`的值
     * @param string $nameKey2 当做另一个`名称`的值
     * @param string $valueKey 要当做`绝对值`或`增速`的值
     * @return array
     */
    public static function multiFieldSingleValue($data, string $nameKey, string $nameKey2, string $valueKey): array
    {
        return self::multi($data, $nameKey, $valueKey, '', $nameKey2);
    }

    /**
     * 对数据进行图表格式的转换，使用两个字段进行当做"名称"字段（occur_period与name），一个字段当做"值"字段
     * 适用于：
     *
     * @param array|Collection $data 二维数据
     * @param string $nameKey 要当做`名称`的值
     * @param string $nameKey2 当做另一个`名称`的值
     * @param string $valueKey 要当做`绝对值`的值
     * @param string $valueKey2 要当做`增速`的值
     * @return array
     */
    public static function multiFieldMultiValue($data,
                                                string $nameKey,
                                                string $nameKey2,
                                                string $valueKey,
                                                string $valueKey2): array
    {
        return self::multi($data, $nameKey, $valueKey, $valueKey2, $nameKey2);
    }

    /**
     * 对数据进行图表格式的转换
     *
     * @param array|Collection $data 二维数据
     * @param string $nameKey 要当做`名称`的值
     * @param string $valueKey 要当做`绝对值`的值
     * @param string $valueKey2 要当做`增速`的值
     * @param string $nameKey2 当做另一个`名称`的值
     * @return array
     */
    public static function multi($data, string $nameKey,
                                 string $valueKey,
                                 string $valueKey2 = '',
                                 string $nameKey2 = ''): array
    {
        $data = is_array($data) ? $data : $data->toArray();
        $result = [];
        foreach ($data as $datum) {
            $value2 = $nameKey2 ? $datum[$nameKey2] : '';
            $arg = [$datum[$nameKey], $value2, $datum[$valueKey]];
            if ($valueKey2) {
                $arg[] = $datum[$valueKey2];
            }
            $result[] = call_user_func('\app\helpers\ChartHelper::base', ...$arg);
        }

        return $result;
    }

    /**
     * 转换格式
     *
     * - [name => 地区生产总值, value => 15]
     * - [name => 地区生产总值, jdz => 15, zs => 22.50]
     * - [occur_period => 201204, value => 15]
     * - [occur_period => 201204, value => 15, zs => 22.50]
     * - [occur_period => 201204, name => 地区生产总值, value => 15]
     * - [occur_period => 201204, name => 地区生产总值, jdz => 15, zs => 22.50]
     *
     * @param string|int $value 当做名称key的值，如果值为数字，则名称key会变为`occur_period`
     * @param string|int $value2 当做名称key2的值，如果`$value(名称key)`为`occur_period`，则名称key2会变为`name`，反之相反
     * @param string|int $jdzValue 绝对值
     * @param string|int|null $zsValue 增速值
     * @return array
     */
    public static function base($value, $value2, $jdzValue, $zsValue = null): array
    {
        //value为数字则当做时间期`occur_period`
        $nameKey = ctype_digit((string)$value) ? 'occur_period' : 'name';
        $info = [$nameKey => $value];
        if ($value2) {
            $temp = $nameKey === 'occur_period' ? 'name' : 'occur_period';
            $info[$temp] = $value2;
        }

        //三个参数时直接使用value
        if (func_num_args() === 3) {
            $info['value'] = $jdzValue;
        } else {
            //四个参数时默认最后一个为增速值
            $info['jdz'] = $jdzValue;
            $info['zs'] = $zsValue;
        }

        return $info;
    }
}
