<?php

/** @noinspection PhpUnusedPrivateMethodInspection */

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
    private $allValidateType = [
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
    public static function validateAndThrow(array $data, array $rules, $throwFirst = false)
    {
        $result = self::validate($data, $rules);
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
        $static = new self();
        $errorCount = 0;
        $message = [];
        if (!empty($options)) {
            foreach ($options as $param => $rules) {
                if (!isset($data[$param])) {
                    $errorCount++;
                    $errorMsg = $param . ' does not exist';
                    $message[] = $errorMsg;
                    continue;
                }
                $rules = explode('|', $rules);
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
                        $errorMsg = "$param rule: $ruleKey does not support, please check or request PR";
                        $message[] = $errorMsg;
                        continue;
                    }
                    $validateResult = $static->$ruleKey($data[$param], $ruleValue);
                    if (!$validateResult) {//验证不通过 记录错误信息
                        $errorCount++;
                        $errorMsg = str_replace('$paramName', $param, $static->allValidateType[$ruleKey]);
                        $errorMsg = str_replace('$data', $ruleValue, $errorMsg);
                        $message[] = $errorMsg;
                    }
                }
            }
        }

        return ['count' => $errorCount, 'errors' => $message,];
    }

//    public function __call($name, $arguments)
//    {
//        return true;
//    }

    private function required($value)
    {
        if (!isset($value)) {
            return false;
        }
        return true;
    }

    private function eq($value, $expect)
    {
        return $value == $expect;
    }

    private function neq($value, $expect)
    {
        return $value != $expect;
    }

    private function gt($value, $expect)
    {
        return $value > $expect;
    }

    private function egt($value, $expect)
    {
        return $value >= $expect;
    }

    private function lt($value, $expect)
    {
        return $value < $expect;
    }

    private function elt($value, $expect)
    {
        return $value <= $expect;
    }

    private function in($value, $expect)
    {
        return in_array($value, json_decode($expect, true));
    }

    private function notnull($value)
    {
        return !($value === null || trim($value) === '');
    }

    private function int($value)
    {
        return ($value == intval($value) && $value !== null);
    }
}
