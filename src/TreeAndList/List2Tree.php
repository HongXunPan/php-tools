<?php

namespace HongXunPan\Tools\TreeAndList;

use stdClass;

class List2Tree
{
    public $tree = [];

//    public $node = StdClass::class;

    public function buildTree(array $list, $idKey = 'id', $parentIdKey = 'parent_id', $childrenKey = 'children')
    {
        //第一步 构建map Map<id, item>
        $map = [];
        foreach ($list as $item) {
            $node = new StdClass();
            $node->id = $item[$idKey];
            $node->parentId = $item[$parentIdKey] ?: 0;
            $node->value = $item;
            $node->value['children'] = [];
//            $map[$item[$idKey]] = $item;
//            $map[$item[$idKey]][$childrenKey] = [];
            $map[$item[$idKey]] = $node;
        }
//        dd($list);

        //遍历map 构建树
        foreach ($map as $item) {
            $parentId = $item->parentId;
//            dd($parentId, $item);
            if ($parentId && $parentId > 0) {
                $parent = $map[$parentId];
                if ($parent) {
                    $map[$parentId]->value['children'][] = $map[$item->id]->value;
                }
            }
        }

        //过滤只要parentId=0的数据
        $this->tree = [];
        foreach ($map as $item) {
            if ($item->parentId == 0 || $item->parentId == -1) {
                $this->tree[] = $item->value;
            }
        }
//        dd($this->tree);

        return $this;
    }

    public function buildTree2(array $list, $idKey = 'id', $parentIdKey = 'parent_id', $childrenKey = 'children', $rootId = 0)
    {// 验证输入参数
        if (empty($list)) {
            return [];
        }

        // 预处理数据，创建一个哈希表用于快速查找
        $nodes = [];
        foreach ($list as $item) {
            if (!isset($item[$idKey]) || !isset($item[$parentIdKey])) {
                throw new \Exception("Each item must contain '$idKey' and '$parentIdKey' keys.");
            }
            $nodes[$item[$idKey]] = $item;
        }

        // 初始化根节点集合
        $tree = [];
        foreach ($nodes as &$node) {
            $node[$childrenKey] = isset($nodes[$node[$idKey]]) ? [] : null;

            if ($node[$parentIdKey] == $rootId) {
                $tree[] = &$node;
            } else {
                if (isset($nodes[$node[$parentIdKey]])) {
                    $nodes[$node[$parentIdKey]][$childrenKey][] = &$node;
                }
            }
        }
        return $tree;
    }

}
