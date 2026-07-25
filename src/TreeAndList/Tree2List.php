<?php

namespace HongXunPan\Tools\TreeAndList;

class Tree2List
{
    /** @var null|array $list */
    public $list = [];

    public function __construct()
    {
        $this->list = [];
    }

    private function contains($haystack, $needle)
    {
        if (!is_string($haystack) || !is_string($needle)) {
            return false;
        }

        return $needle === '' || strpos($haystack, $needle) !== false;
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
        if ($dept === 0) {
            $this->list = [];
        }
        $dept++;
        foreach ($tree as $item) {
            if (!array_key_exists($this->fieldConfig['id'], $item)) {
                throw new \Exception('tree item id field does not exist');
            }
            $value = $item;
            $value[$this->fieldConfig['parent']] = $parentId;
            $value[$this->fieldConfig['id']] = $item[$this->fieldConfig['id']];
            $value[$this->fieldConfig['dept']] = $dept;
            unset($value[$this->fieldConfig['children']]);
            $children = isset($item[$this->fieldConfig['children']])
                && is_array($item[$this->fieldConfig['children']])
                ? $item[$this->fieldConfig['children']]
                : [];
            if ($children) {
                $value[$this->fieldConfig['children']] = [];
            } else {
                $value[$this->fieldConfig['children']] = null;
            }
            $this->list[] = $value;
            if ($children) {
                $this->buildByChildren($children, $item[$this->fieldConfig['id']], $dept);
            }
        }
        return $this;
    }

    public function searchList($field, $value, $like = false)
    {
        return array_values(array_filter($this->list, function ($row) use ($like, $field, $value) {
            if (isset($row[$field])) {
                if ($like) {
                    return $this->contains($row[$field], $value);
                } else {
                    return $row[$field] == $value;
                }
            }
            return false;
        }));
    }
}
