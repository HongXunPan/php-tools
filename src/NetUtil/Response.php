<?php
namespace app;

class Response
{
    /**
     * @var array
     */
    private $data;

    /**
     * Response constructor.
     *
     * @param int $code
     * @param string $msg
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function toJson()
    {
        $result = json_encode($this->data, JSON_UNESCAPED_UNICODE);
        return $this->convertPriceToBillionUnit($result);
    }

    /**
     * 转化价格为亿元单位
     *
     * 临时解决方案，最终需要前端处理
     *
     * @param string $jsonResponse
     * @return string
     */
    private function convertPriceToBillionUnit($jsonResponse)
    {
        if (! config_item('auto_convert_to_billion_unit')) {
            return $jsonResponse;
        }
        // "value": "123456789.00"
        // "unit": "元"
        if (! $match = preg_match('/(-?\d{9,}\.\d{2})/', $jsonResponse)) {
            return $jsonResponse;
        }

        $jsonResponse = preg_replace_callback(
            '/(-?\d{7,}\.\d{2})/',
            function ($match) {
                return number_format($match[1] / 100000000, 2, '.','');
            },
            $jsonResponse
        );
        return str_replace(
            '亿亿元', 
            '亿元',
            str_replace('元', '亿元', $jsonResponse)
        );
    }
}
