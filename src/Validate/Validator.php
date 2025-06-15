<?php

/** @noinspection PhpUnused protectedMethodInspection */

/**
 * 仿制laravel的验证器
 * $validator = Validator::make($request->all(), [
 * 'title' => 'required|unique:posts|max:255',
 * 'body' => 'required',
 * ]);
 * Created by PhpStorm At 2022/1/11 16:45.
 * Author: HongXunPan
 * Email: me@kangxuanpeng.com
 */

namespace HongXunPan\Tools\Validate;

use Exception;

class Validator
{
    protected $allValidateType = [
        'required' => '$paramName is required',
        'eq' => '$paramName must equal to $rule',
        'neq' => '$paramName must not equal to $rule',
        'gt' => '$paramName must greater than $rule',
        'egt' => '$paramName must greater than $rule or equal to $rule',
        'lt' => '$paramName must less than $rule',
        'elt' => '$paramName must less than $rule or equal to $rule',
        'in' => '$paramName must in $rule', //options 必须是json字符串格式 eg: ['mid' => 'in_array:[11]']
        'notnull' => '$paramName can not be null',
        'int' => '$paramName must be int',
        'array' => '$paramName must be array',
        'string' => '$paramName must be string',
        'len' => '$paramName\'s length must in $rule',
        'lenMin' => '$paramName\'s length must be bigger than $rule',
        'lenMax' => '$paramName\'s length must be smaller than $rule',
        'time' => '$paramName must be time',
    ];

    /**
     * @param array $data 输入的数组
     * @param array $rules 验证的规则
     * @param false $throwFirst true只抛出第一个错误，默认抛出全部错误
     * @return bool
     * @throws Exception
     */
    public static function validateOrThrow(array $data, array $rules, $throwFirst = false)
    {
        $result = static::validate($data, $rules);
        if ($result['count'] === 0) {
            return true;
        }
        $exception = $result['errors'];
        if ($throwFirst) {
            $exception = $exception[0];
        }
        throw new Exception(json_encode($exception));
    }

    /**
     * @param $data array 要验证的数据
     * @param $options array 验证规则
     * @return array 0 错误数量 1
     */
    public static function validate(array $data, array $options)
    {
        $static = new static();
        $errorCount = 0;
        $message = [];
        $detail = [];
        $validatedData = [];
        if (!empty($options)) {
            foreach ($options as $param => $rules) {
                @list($param, $paramName) = explode(':', $param);
                $paramName = isset($paramName) ? $paramName : $param;
                $rules = explode('|', $rules);

                $value = null;
                if (isset($data[$param])) {
                    $value = $data[$param];
                } else { //值不存在
                    if (in_array('required', $rules)) {
                        $rules = ['required'];
                    } else {
                        continue;
                    }
                }
                foreach ($rules as $rule) {
                    $rule = explode(':', $rule);
                    $ruleKey = $rule[0];
                    $ruleValue = '';
                    if (count($rule) > 1) {
                        $ruleValue = $rule[1];
                    }
                    $methodExist = method_exists($static, $ruleKey);
                    if (!$methodExist) {
                        $errorCount++;
                        $errorMsg = "$paramName rule: $ruleKey does not support, please check or request PR";
                        $message[] = $errorMsg;
                        $detail[] = [
                            'param' => $paramName,
                            'value' => $value,
                            'rule' => $ruleKey,
                            'rule_value' => $ruleValue,
                            'reason' => 'rule not support'
                        ];
                        continue;
                    }
                    $validateResult = $static->$ruleKey($value, $ruleValue);
                    if (!$validateResult) {//验证不通过 记录错误信息
                        $errorCount++;
                        $errorMsg = str_replace('$paramName', $paramName, $static->allValidateType[$ruleKey]);
                        $errorMsg = str_replace('$rule', $ruleValue, $errorMsg);
                        $message[] = $errorMsg;
                        $detail[] = [
                            'param' => $paramName,
                            'value' => $value,
                            'rule' => $ruleKey,
                            'rule_value' => $ruleValue,
                            'reason' => "result: " . json_encode($validateResult),
                        ];
                    } else {
                        $validatedData[$param] = $value;
                    }
                }
            }
        }

        return ['count' => $errorCount, 'errors' => $message, 'detail' => $detail, 'validated_data' => $validatedData];
    }

//    public function __call($name, $arguments)
//    {
//        return true;
//    }

    protected function required($value)
    {
        if (!isset($value)) {
            return false;
        }
        return true;
    }

    protected function eq($value, $expect)
    {
        return $value == $expect;
    }

    protected function neq($value, $expect)
    {
        return $value != $expect;
    }

    protected function gt($value, $expect)
    {
        return $value > $expect;
    }

    protected function egt($value, $expect)
    {
        return $value >= $expect;
    }

    protected function lt($value, $expect)
    {
        return $value < $expect;
    }

    protected function elt($value, $expect)
    {
        return $value <= $expect;
    }

    protected function in($value, $expect)
    {
        return in_array($value, json_decode($expect, true));
    }

    protected function notnull($value)
    {
        return !($value === null || trim($value) === '');
    }

    protected function int($value)
    {
        return ($value == intval($value) && $value !== null);
    }

    /** @noinspection PhpLanguageLevelInspection */
    protected function array($value)
    {
        return is_array($value);
    }

    protected function string($value)
    {
        return is_string($value);
    }

    protected function len($value, $expect)
    {
        list($min, $max) = explode('-', $expect);
        $len = iconv_strlen($value);
        return $len >= $min && $len <= $max;
    }

    protected function lenMin($value, $expect)
    {
        return iconv_strlen($value) > $expect;
    }

    protected function lenMax($value, $expect)
    {
        return iconv_strlen($value) < $expect;
    }

    protected function time($value)
    {
        return strtotime($value) !== false;
    }
}
