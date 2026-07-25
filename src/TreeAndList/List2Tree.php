<?php

namespace HongXunPan\Tools\TreeAndList;

class List2Tree
{
    public $tree = [];

    public function buildTree(array $list, $idKey = 'id', $parentIdKey = 'parent_id', $childrenKey = 'children')
    {
        $this->tree = $this->build($list, $idKey, $parentIdKey, $childrenKey, [0, -1]);

        return $this;
    }

    public function buildTree2(array $list, $idKey = 'id', $parentIdKey = 'parent_id', $childrenKey = 'children', $rootId = 0)
    {
        return $this->build($list, $idKey, $parentIdKey, $childrenKey, [$rootId]);
    }

    private function build(array $list, $idKey, $parentIdKey, $childrenKey, array $rootIds)
    {
        $nodes = [];
        foreach ($list as $item) {
            if (!array_key_exists($idKey, $item) || !array_key_exists($parentIdKey, $item)) {
                throw new \Exception("Each item must contain '$idKey' and '$parentIdKey' keys.");
            }
            $item[$childrenKey] = [];
            $nodes[$item[$idKey]] = $item;
        }

        $tree = [];
        foreach ($nodes as &$node) {
            $parentId = $node[$parentIdKey];
            if (in_array($parentId, $rootIds)) {
                $tree[] = &$node;
                continue;
            }
            if (isset($nodes[$parentId])) {
                $nodes[$parentId][$childrenKey][] = &$node;
            }
        }
        unset($node);

        return $tree;
    }
}
