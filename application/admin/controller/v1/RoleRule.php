<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\api\controller\Tools;
use app\common\model\RuleModel;
use app\common\model\RoleRuleModel;
use think\Controller;
use think\Request;

class RoleRule extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        //
        $db = new RuleModel();
        $list = $db->where('status', 1)->field('id,name,pid,url,img')->select();
        return json(['code' => 0, 'msg' => '获取规则信息成功', 'data' => $list]);
    }
    public function getTree(Request $request)
    {
        //
        $db = new RuleModel();
        $list = $db->where('status', 1)->field('id,name,pid,url,img')->select()->toArray();
        return json(['code' => 0, 'msg' => '获取规则信息成功', 'data' => Tools::List2Tree($list)]);
    }
    public function getUrlRoleId(Request $request)
    {
        $role_id = $request->param('role_id');
        $db = new RoleRuleModel();
        $rule_ids = $db->where('role_id', $role_id)->value('rule_ids');
        $rule_ids = explode(',', $rule_ids);
        foreach ($rule_ids as $key => $value) {
            $rule_ids[$key] = intval($value);
        }
        return json(['code' => 0, 'msg' => '获取规则信息成功', 'data' => $rule_ids]);
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
        $data = $request->param();
        $role_id = $request->param('role_id');
        $rule_ids =  $request->param('rule_ids');
        $db = new RoleRuleModel();
        $info = $db->where('role_id', $role_id)->find();
        if ($info) {
            //edit
            $res = $db->where('role_id', $role_id)->setField('rule_ids', $rule_ids);
        } else {
            //add
            $data['role_id'] = $role_id;
            $data['rule_ids'] = $rule_ids;
            $res = $db->save($data);
        }
        
        if ($res) {
            return json(['code' => 0, 'msg' => '操作成功']);
        } else
            return json(['code' => 1, 'msg' => '操作失败']);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read(Request $request)
    {
        // $admin_id = $request->param('admin_id');
        // $db = new RoleRuleModel();
        // $info = $db->where('admin_id', $admin_id)->find();
        // $db = new RoleModel();
        // $list = $db->where('status', 1)->field('id,name')->select()->toArray();
        // if ($info) {
        //     $tmp = explode(',', $info['role_ids']);
        //     foreach ($list as $k => $v) {
        //         # code...
        //         if (in_array($v['id'], $tmp)) {
        //             $list[$k]['check'] = true;
        //         } else
        //             $list[$k]['check'] = false;
        //     }
        // } else {
        //     foreach ($list as $key => $value) {
        //         # code...
        //         $list[$key]['check'] = false;
        //     }
        // }
        // return json(['code' => 0, 'data' => $list]);
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
