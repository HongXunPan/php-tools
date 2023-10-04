<?php
/**
 * 图表配置
 *
 * User: Young
 * Date: 2022/6/14
 * Time: 10:50
 */

namespace app\charts;

use RuntimeException;

class Config
{
    /**
     * @var null|string 名称字段
     */
    private $name;
    /**
     * @var null|string 时间期字段
     */
    private $occurPeriodKey;
    /**
     * @var array|string|null 绝对值字段
     */
    private $jdzKey;
    /**
     * @var array|string|null 增速字段
     */
    private $zsKey;
    /**
     * @var string|array 单位字段，数组形式时，可使用`app\constants\Unit`类中的数组常量格式
     */
    private $unit;

    /**
     * @param string $occurPeriodKey
     * @return Config
     */
    public function setOccurPeriodKey($occurPeriodKey = Chart::OUTPUT_OCCUR_PERIOD): self
    {
        $this->occurPeriodKey = $occurPeriodKey;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getOccurPeriodKey(): ?string
    {
        return $this->occurPeriodKey;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Config
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array|string|null
     */
    public function getJdzKey()
    {
        return $this->jdzKey;
    }

    /**
     * @param string|array $jdzKey
     * @return Config
     */
    public function setJdzKey($jdzKey): self
    {
        $this->jdzKey = $jdzKey;
        return $this;
    }

    /**
     * @return array|string|null
     */
    public function getZsKey()
    {
        return $this->zsKey;
    }

    /**
     * @param string|array $zsKey
     * @return Config
     */
    public function setZsKey($zsKey): self
    {
        $this->zsKey = $zsKey;
        return $this;
    }

    /**
     * @return string|array
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param string|array $unit
     */
    public function setUnit($unit): self
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * 校验参数
     */
    public function check(): void
    {
        if (!$this->name && !$this->occurPeriodKey) {
            throw new RuntimeException('至少需要设置一个名称值');
        }
        if (!$this->jdzKey && !$this->zsKey) {
            throw new RuntimeException('至少需要设置一个数值key');
        }
        if (!$this->unit) {
            throw new RuntimeException('unit单位未设置');
        }
    }
}
