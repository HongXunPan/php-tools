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
        'eq' => '$paramName must equal to $data',
        'neq' => '$paramName must not equal to $data',
        'gt' => '$paramName must greater than $data',
        'egt' => '$paramName must greater than $data or equal to $data',
        'lt' => '$paramName must less than $data',
        'elt' => '$paramName must less than $data or equal to $data',
        'in' => '$paramName must in $data', //options 必须是json字符串格式 eg: ['mid' => 'in_array:[11]']
        'notnull' => '$paramName can not be null',
        'int' => '$paramName must be int',
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
                            'rule' => $ruleKey,
                            'value' => $value,
                            'reason' => 'rule not support'
                        ];
                        continue;
                    }
                    $validateResult = $static->$ruleKey($value, $ruleValue);
                    if (!$validateResult) {//验证不通过 记录错误信息
                        $errorCount++;
                        $errorMsg = str_replace('$paramName', $paramName, $static->allValidateType[$ruleKey]);
                        $errorMsg = str_replace('$data', $ruleValue, $errorMsg);
                        $message[] = $errorMsg;
                        $detail[] = [
                            'param' => $paramName,
                            'rule' => $ruleKey,
                            'value' => $value,
                            'reason' => "result: " . json_encode($validateResult),
                        ];
                    }
                }
            }
        }

        return ['count' => $errorCount, 'errors' => $message, 'detail' => $detail];
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
}
