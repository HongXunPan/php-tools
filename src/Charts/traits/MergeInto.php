<?php
/**
 * 合并Trait
 * 附加额外字段至二维数组中的每一项
 *
 * User: Young
 * Date: 2022/6/14
 * Time: 11:41
 */

namespace app\charts\traits;

use RuntimeException;

trait MergeInto
{
    /**
     * @var array|callable 需要合并至二维数组中的每一项的额外值
     */
    protected $merge;

    /**
     * @return array|callable
     */
    public function getMerge()
    {
        return $this->merge;
    }

    /**
     * @param array|callable $merge
     */
    public function setMerge($merge): self
    {
        if (!is_array($merge) && !is_callable($merge)) {
            throw new RuntimeException('merge仅允许为数组或回调函数');
        }
        $this->merge = $merge;
        return $this;
    }

    /**
     * 转换`merge`，数组则直接返回，否则执行回调函数获取数组
     *
     * @param array $datum 二维数组中的每一项
     * @return array|null 需要合并的数组
     */
    protected function process(array $datum): ?array
    {
        if (!$this->merge) {
            return null;
        }

        if (is_callable($this->merge)) {
            $callback = $this->merge;
            return $callback($datum);
        }

        return $this->merge;
    }
}
