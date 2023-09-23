<?php
/**
 * 时间处理
 * User: Young
 * Date: 2021/6/2
 * Time: 12:00
 */

namespace app\helpers;

use Carbon\Carbon;
use Exception;

class DateTimeHelper
{
    /**
     * 格式化数据为`Ym`格式，主要用于匹配`occur_period`字段的格式
     * 支持年（Y）月（m|j）日(d)格式以及年月格式以及年格式（返回对应年的01）
     * 仅支持`/`、`-`分隔符或无分隔符的时间格式
     * 不符合既定格式则返回最新年月
     *
     * @param string|null $date 待格式化的日期
     * @return int
     */
    public static function formatYM(?string $date): int
    {
        if (!$date) {
            return date('Ym');
        }

        $date = preg_replace(['/-/', '/\//'], '', $date);
        $len = strlen($date);
        switch ($len) {
            case 4:
                $result = $date . '01';
                break;
            case 5:
                $result = substr($date, 0, 4) . '0' . substr($date, 4);
                break;
            case 6:
                $result = $date;
                break;
            default:
                $result = $len > 6 ? substr($date, 0, 6) : date('Ym');
                break;

        }
        return (int)$result;
    }

    /**
     * 获取季度年月，默认获取当前季度
     *
     * @param int $period 时间周期Ym，默认当前年月
     * @param int $offset 偏移量，1则为获取上个季度
     * @return int
     */
    public static function getSeasonYearMonth(int $period = 0, int $offset = 0): int
    {
        if (!$period) {
            $period = date('Ym');
        }
        //上季度是第几季度
        $year_season = date('Y-n', strtotime($period . '01'));
        [$year, $season] = explode('-', $year_season);
        $session = ceil(($season) / 3) - $offset;
        return (int)date('Ym', mktime(0, 0, 0, $session * 3, 1, $year));
    }

    /**
     * 获取两个时间的差，并转换为对应等级的秒、分、时、天
     *
     * @param string|Carbon $dateTime 被比对时间
     * @param string|Carbon $diffDateTime 比对时间
     * @param string $default 默认时间
     * @return string
     * @throws Exception
     */
    public static function dateTimeDiff($dateTime, $diffDateTime, string $default = '0'): string
    {
        if (!$dateTime || !$diffDateTime) {
            return $default;
        }
        if (!($dateTime instanceof Carbon)) {
            $dateTime = Carbon::parse($dateTime);
        }
        if (!($diffDateTime instanceof Carbon)) {
            $diffDateTime = Carbon::parse($diffDateTime);
        }
        $diff = $dateTime->diffAsCarbonInterval($diffDateTime)->forHumans();
        $diff = str_replace(['seconds', 'second'], '秒', $diff);
        $diff = str_replace(['minutes', 'minute'], '分钟', $diff);
        $diff = str_replace(['hours', 'hour'], '小时', $diff);
        $diff = str_replace(['days', 'day'], '天', $diff);
        $diff = str_replace(['weeks', 'week'], '周', $diff);
        $diff = str_replace(['months', 'month'], '月', $diff);
        return str_replace(' ', '', $diff);
    }

    /**
     * 获取近一年的年月，例如现为2020年1月，则返回[201901,202001]
     *
     * @return array
     */
    public static function lastYMAndCurrentYM(): array
    {
        $past = Carbon::now()->subYear(1)->format('Ym');
        $now = date('Ym');
        return [(int)$past, (int)$now];
    }

    /**
     * 获取近一月的日期，例如现为2020年1月5日，则返回[20191205,20200105]
     *
     * @return array
     */
    public static function lastMonthDateAndCurrentMonthDate(): array
    {
        $past = Carbon::now()->subMonth(1)->format('Ymd');
        $now = date('Ymd');
        return [(int)$past, (int)$now];
    }

    /**
     * 获取前X年的月份
     *
     * @param int $period 年月
     * @param int $offset 偏移年份，默认1
     * @return int
     */
    public static function lastYearMonth(int $period = 0, int $offset = 1): int
    {
        $format = 'Ym';
        if (!$period) {
            $period = date($format);
        }

        return Carbon::createFromFormat($format, $period)->subYears($offset)->format($format);
    }

    /**
     * 获取前X年的月份
     *
     * @param int $time      时间
     * @param string $format 格式
     * @param int $offset    偏移年份，默认1
     * @return int
     */
    public static function lastYearMonthByTime(int $time, string $format, int $offset = 1): int
    {
        try {
            $date = Carbon::createFromFormat($format, $time)->subYears($offset)->format($format);
        } catch (\Exception $e) {
            $date = $time;
        }
        return $date;
    }
}
