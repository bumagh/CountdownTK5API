<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\common\model\AdminRoleModel;
use app\common\model\RoleRuleModel;
use app\common\model\RuleModel;
use think\Controller;
use think\Request;

class Menu extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        $admin_id = $this->aid;
        $adminRole = new AdminRoleModel();
        $role_ids = $adminRole->where('admin_id', $admin_id)->value('role_ids');
        $role_ids_arr = explode(',', $role_ids);
        $roleRule = new RoleRuleModel();
        $roleRuleIds = $roleRule->whereIn('role_id', $role_ids_arr)->column('rule_ids');
        $tmp = '';
        foreach ($roleRuleIds as $key => $value) {
            $tmp .= ',' . $value;
        }
        $tmps = substr($tmp, 1);
        $rule = new RuleModel();
        $list = $rule->whereIn('id', $tmps)->select()->toArray();
        $menus = $this->List2Tree($list);
        return json(['code' => 0, 'msg' => '获取菜单成功', 'data' => $menus]);
    }
    protected function List2Tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
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

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
