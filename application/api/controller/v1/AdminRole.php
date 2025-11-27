<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\common\model\AdminRoleModel;
use think\Request;
use app\common\model\RoleModel;

class AdminRole extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $db = new RoleModel();
        $list = $db->where('status', 1)->field('id,name')->select();
        if (!$list->isEmpty()) {
            $count = $db->where('status', 1)->count('id');
            return json(['code' => 0, 'msg' => '获取成功', 'data' => $list, 'count' => $count]);
        } else
            return json(['code' => 0, 'msg' => '没数据']);
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
        $admin_id = $request->param('admin_id');
        $role_ids =  $request->param('role_ids');
        $db = new AdminRoleModel();
        $info = $db->where('admin_id', $admin_id)->find();
        if ($info) {
            //edit
            $res = $db->where('admin_id', $admin_id)->setField('role_id', $role_ids);
        } else {
            //add
            $data['admin_id'] = $admin_id;
            $data['role_ids'] = $role_ids;
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
        $admin_id = $request->param('admin_id');
        $db = new AdminRoleModel();
        $info = $db->where('admin_id', $admin_id)->find();
        $db = new RoleModel();
        $list = $db->where('status', 1)->field('id,name')->select()->toArray();
        if ($info) {
            $tmp = explode(',', $info['role_ids']);
            foreach ($list as $k => $v) {
                # code...
                if (in_array($v['id'], $tmp)) {
                    $list[$k]['check'] = true;
                } else
                    $list[$k]['check'] = false;
            }
        } else {
            foreach ($list as $key => $value) {
                # code...
                $list[$key]['check'] = false;
            }
        }
        return json(['code' => 0, 'data' => $list]);
    }
    public function adminroleids(Request $request)
    {
        $admin_id = $request->param('admin_id');
        $db = new AdminRoleModel();
        $info = $db->where('admin_id', $admin_id)->value('role_id');
        if ($info) {
            $role_ids = explode(',', $info);
            foreach ($role_ids as $key => $value) {
                $role_ids[$key] = intval($value);
            }
            return json(['code' => 0, 'msg' => '获取admin下的roleids成功', 'data' => $role_ids]);
        } else
            return json(['code' => 1, 'msg' => '获取admin下的roleids为空', 'data' => '']);
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
