<?php

namespace HongXunPan\Tools\TreeAndList;

class Tree2List
{
    /** @var null|array $list */
    public $list = null;

    public function __construct()
    {
        if (!function_exists('str_contains')) {
            /**
             * 判断字符串是否包含指定字符串
             * @param $haystack
             * @param $needle
             * @return bool
             */
            function str_contains($haystack, $needle)
            {
                return '' === $needle || false !== strpos($haystack, $needle);
            }
        }
    }

    private $fieldConfig = [
        'children' => 'children',
        'id' => 'id',
        'parent' => 'parent',
        'dept' => 'dept',
    ];
    public function setFieldName(array $fieldConfig)
    {
        foreach ($fieldConfig as $field => $fieldName) {
            $this->fieldConfig[$field] = $fieldName;
        }
        return $this;
    }

    /**
     * @param array $tree
     * @param int $parentId
     * @param int $dept
     * @return Tree2List //to check
     */
    public function buildByChildren(array $tree, $parentId = 0, $dept = 0)
    {
        $dept++;
        foreach ($tree as $item) {
            $value = $item;
            $value[$this->fieldConfig['parent']] = $parentId;
            $value[$this->fieldConfig['id']] = $item[$this->fieldConfig['id']];
            $value[$this->fieldConfig['dept']] = $dept;
            unset($value[$this->fieldConfig['children']]);
            if ($item[$this->fieldConfig['children']]) {
                $value[$this->fieldConfig['children']] = [];
            } else {
                $value[$this->fieldConfig['children']] = null;
            }
            $this->list[] = $value;
            if ($item[$this->fieldConfig['children']]) {
                $this->buildByChildren($item[$this->fieldConfig['children']], $item[$this->fieldConfig['id']], $dept);
            }
        }
        return $this;
    }

    public function searchList($field, $value, $like = false)
    {
        return array_values(array_filter($this->list, function ($row) use ($like, $field, $value) {
            if (isset($row[$field])) {
                if ($like) {
                    return str_contains($row[$field], $value);
                } else {
                    return $row[$field] == $value;
                }
            }
            return false;
        }));
    }
}
