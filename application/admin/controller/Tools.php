<?php

namespace app\api\controller;

use think\Controller;
use think\Request;

class Tools
{
    public static function List2Tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
    {
        if (!is_array($list)) return [];
        $aRefer = [];
        foreach ($list as $key => $data) {
            # code...
            $aRefer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            $parentId = $data[$pid];
            if ($root === $parentId) {
                $tree[] = &$list[$key];
            } else {
                if (isset($aRefer[$parentId])) {
                    $parent = &$aRefer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
        return $tree;
    }
}
