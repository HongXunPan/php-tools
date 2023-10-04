<?php
/**
 * 图表生成类
 *
 * User: Young
 * Date: 2022/6/14
 * Time: 10:39
 */

namespace app\charts;

use app\charts\traits\MergeInto;
use app\helpers\NumberHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Class Chart
 * @package app\charts
 * @method Chart setOccurPeriodKey($occurPeriodKey = Chart::OUTPUT_OCCUR_PERIOD)
 * @method Chart setName(string|array $name)
 * @method Chart setJdzKey(string|array $jdzKey)
 * @method Chart setZsKey(string|array $zsKey)
 * @method Chart setUnit(array|string $unit)
 */
class Chart
{
    use MergeInto;

    /**
     * @var array  二维数组
     */
    private $data;
    /**
     * @var Config 配置项
     */
    private $config;
    /**
     * @const string null时转换成的默认值
     */
    private $null2default = '-';

    /**
     * @const string 输出时`zs`的展示名
     */
    public const OUTPUT_ZS = 'zs';
    /**
     * @const string 输出时`unit`的展示名
     */
    public const OUTPUT_UNIT = 'unit';
    /**
     * @const string 输出时`value`的展示名
     */
    public const OUTPUT_VALUE = 'value';
    /**
     * @const string 输出时`occur_period`的展示名
     */
    public const OUTPUT_OCCUR_PERIOD = 'occur_period';
    /**
     * @const string 输出时`name`的展示名
     */
    public const OUTPUT_NAME = 'name';
    /**
     * 默认表配置
     */
    public const TABLE_CONFIG = [
        //表头的key
        'columnKey' => 'columns',
        //表头中每个字段所使用的名称的key
        'columnName' => 'title',
        //表头中每个字段所使用的唯一值的key
        'uniqueName' => 'dataIndex',
        //表身的key
        'dataKey' => 'dataSource',
        //表身的每行数据是否需要迭代的key
        'iterationKey' => true,
    ];

    public function __construct($data, ?Config $config = null)
    {
        $this->data = $this->asArray($data);
        $this->config = $config ?? new Config();
    }

    /**
     * 创建对象
     *
     * @param array|Collection $data 原始值
     * @param Config|null $config 配置项
     * @return static
     */
    public static function create($data, ?Config $config = null): self
    {
        return new self($data, $config);
    }

    public function __call($name, $arguments)
    {
        if (Str::start($name, 'set') && method_exists(Config::class, $name)) {
            $this->config->$name(...$arguments);
            return $this;
        }

        throw new BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()', static::class, $name
        ));
    }

    /**
     * 根据配置生成文字图表
     *
     * @param array $config 名称与单位配置，key => [name => xx, 'unit' => xx]，每一项的名称(`name`)与单位(`unit`)需在此设置
     * @return array
     */
    public function getAsText(array $config): array
    {
        if (!$this->data || !$config) {
            return [];
        }

        $result = [];
        foreach ($config as $attr => $item) {
            if (!isset($item['name']) || !isset($item['unit'])) {
                throw new RuntimeException("{$attr}未设置对应配置");
            }
            $this->config->setUnit($item['unit']);
            $result[] = $this->transfer($item['name'], $this->data[$attr], false, $item['extra']);
        }

        return $result;
    }

    /**
     * 根据配置生成基础图表
     * 适用于：饼图、单柱状图、单折线图等单值单名称图表
     *
     * @return array 图表数据结构（二维数组）
     */
    public function getAsPie(): array
    {
        $this->config->check();
        if ($this->config->getJdzKey() && $this->config->getZsKey()) {
            throw new RuntimeException('单值图表仅允许设置一个`jdz`或`zs`字段');
        }
        if (!$this->data) {
            return [];
        }

        //此处的默认值使用null
        $this->null2default = null;
        $result = [];
        foreach ($this->data as $datum) {
            $name = $this->config->getName() === null
                ? $datum[$this->config->getOccurPeriodKey()]
                : $datum[$this->config->getName()];
            [$jdzValue, $zsValue] = $this->config->getJdzKey()
                ? [$datum[$this->config->getJdzKey()], false]
                : [false, $datum[$this->config->getZsKey()]];

            $result[] = $this->transfer($name, $jdzValue, $zsValue, $this->process($datum));
        }

        return $this->sortByValueDesc($result);
    }

    /**
     * 根据配置生成柱状折线图图表
     * 适用于：多折线图、多柱状图、柱状折线图
     *
     * @return array 图表数据结构（二维数组）
     */
    public function getAsBarsLines(): array
    {
        $this->config->check();
        if (!is_array($this->config->getJdzKey()) && !is_array($this->config->getZsKey())) {
            throw new RuntimeException('`jdz`或`zs`字段必须为关联数组');
        }
        if (!$this->data) {
            return [];
        }

        $result = [];
        //此处的nameKey即为x轴
        $nameKey = $this->config->getName() ?? $this->config->getOccurPeriodKey();
        foreach ([$this->config->getJdzKey(), $this->config->getZsKey()] as $key => $elements) {
            $config = $key === 0 ? ['bar', 'jdz'] : ['line', 'zs'];
            foreach ($elements as $field => $name) {
                $temp['name'] = $name;
                $temp['type'] = $config[0];
                $data = [];
                foreach ($this->data as $datum) {
                    //柱状时，使用绝对值作为`value`
                    if ($config[1] === 'jdz') {
                        $jdzValue = $datum[$field];
                        $zsValue = false;
                    } else {
                        //折线时，使用增速作为`value`
                        $jdzValue = false;
                        $zsValue = $datum[$field];
                    }
                    $data[] = $this->transfer($datum[$nameKey], $jdzValue, $zsValue, $this->process($datum));
                }
                $temp['data'] = $data;
                $result[] = $temp;
            }
        }

        return $result;
    }

    /**
     * 根据配置生成表格
     * 适用于：表格
     *
     * @param array $headers 表头，一维数组：['字段索引' => '展示标题']
     * @param array $config 表配置
     * @return array
     */
    public function getAsTable(array $headers, array $config = []): array
    {
        if (!$headers) {
            throw new RuntimeException('表头不允许为空');
        }
        $config = $config ?: static::TABLE_CONFIG;
        $columns = [];
        foreach ($headers as $key => $value) {
            $head = [$config['columnName'] => $value, $config['uniqueName'] => $key];
            if ($config['iterationKey']) {
                $head['key'] = $key;
            }
            $columns[] = $head;
        }

        $source = [];
        $key = 1;
        foreach ($this->data as $item) {
            $temp = [];
            //组装dataSource的字段与数据
            foreach ($columns as $column) {
                $value = '';
                if (is_array($item) && (array_key_exists($column[$config['uniqueName']], $item))) {
                    $value = $item[$column[$config['uniqueName']]];
                } elseif (property_exists($item, $column[$config['uniqueName']])) {
                    $value = $item->{$column[$config['uniqueName']]};
                }

                $temp[$column[$config['uniqueName']]] = $value;
            }
            //迭代key，前端展示需要，唯一值即可无实际意义
            if ($config['iterationKey']) {
                $temp['key'] = $key++;
            }

            $source[] = $temp;
        }
        $result[$config['columnKey']] = $columns;
        $result[$config['dataKey']] = $source;

        return $result;
    }

    /**
     * 转换格式
     *
     * - [name => 地区生产总值, value => 15, unit => 亿元]
     * - [name => 地区生产总值, value => 15, zs => 22.50, unit => 亿元]
     *
     * @param string|int $name 当做`name`的值
     * @param string|int|null|bool $jdzValue 绝对值，false时则此参数不参与返回参数判断
     * @param string|int|null|bool $zsValue 增速值，false时则此参数不参与返回参数判断
     * @param array|null $extra 额外需要merge的数组
     * @return array
     */
    public function transfer($name, $jdzValue, $zsValue, ?array $extra): array
    {
        $info[static::OUTPUT_NAME] = $name;

        if ($this->config->getZsKey() === null || $zsValue === false) {
            [$info[static::OUTPUT_VALUE], $info[static::OUTPUT_UNIT]] = $this->handleValueAndUnit($jdzValue);
        } elseif ($this->config->getJdzKey() === null || $jdzValue === false) {
            $info[static::OUTPUT_VALUE] = $this->getValue($zsValue);
            $info[static::OUTPUT_UNIT] = '%';
        } else {
            [$info[static::OUTPUT_VALUE], $info[static::OUTPUT_UNIT]] = $this->handleValueAndUnit($jdzValue);
            $info[static::OUTPUT_ZS] = $this->getValue($zsValue);
        }

        if ($extra) {
            $info = array_merge($info, $extra);
        }

        return $info;
    }

    /**
     * 转换数值与单位
     *
     * @param string|int|null $jdzValue 绝对值
     * @return array
     */
    private function handleValueAndUnit($jdzValue): array
    {
        $unit = $this->config->getUnit();
        //string类型无需转换
        if (is_string($unit)) {
            return [$this->getValue($jdzValue), $unit];
        }
        if ($jdzValue === null) {
            return [$this->null2default, is_array($unit) ? $unit['name'] : $unit];
        }

        return [NumberHelper::times($jdzValue, $unit['times'], isset($unit['noDecimal'])), $unit['name']];
    }

    /**
     * 获取array形式的`$data`
     *
     * @param array|Collection $data 原始值
     * @return array
     */
    private function asArray($data): array
    {
        if (is_array($data)) {
            return $data;
        }
        if ($data instanceof Collection || $data instanceof Model) {
            return $data->toArray();
        }
        return (array)$data;
    }

    /**
     * @param array $data
     * @return self
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param Config $config
     * @return self
     */
    public function setConfig(Config $config): self
    {
        $this->config = $config;
        return $this;
    }

    /**
     * 获取数值
     *
     * @param string|int|null $value 原始数值
     * @return string|int|null
     */
    private function getValue($value)
    {
        return $value ?? $this->null2default;
    }

    /**
     * @param string $null2default
     * @return self
     */
    public function setNull2default(string $null2default): self
    {
        $this->null2default = $null2default;
        return $this;
    }

    /**
     * 对数据集进行降序排序
     *
     * @param array $result 待排序数据集
     * @return array
     */
    private function sortByValueDesc(array $result): array
    {
        array_multisort(array_column($result, 'value'), SORT_DESC, $result);
        return $result;
    }
}
