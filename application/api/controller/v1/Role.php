<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\common\model\RoleModel;
use think\Controller;
use think\Request;

class Role extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $limit = $request->param('limit') ? $request->param('limit') : 10;
        $page = $request->param('page') ? $request->param('page') : 1;
        $db = new RoleModel();
        $list = $db->where('status', 1)->limit($limit)->page($page)->select();
        if (!$list->isEmpty()) {
            $count = $db->where('status', 1)->limit($limit)->page($page)->count('id');
            return json(['code' => 0, 'msg' => '获取成功', 'data' => $list, 'count' => $count]);
        } else
            return json(['code' => 0, 'msg' => '没数据']);
    }

    public function lists(Request $request)
    {
        $db = new RoleModel();
        $fields = 'id,name';
        $list = $db->field($fields)->select();
        return json(['code' => 0, 'msg' => '获取成功', 'data' => $list]);
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
        $data = $request->param();
        $db = new RoleModel();
        return json($db->_update($data));
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
    public function delete(Request $request)
    {
        $id = $request->param('id');
        $db = new RoleModel();
        return json($db->_del($id));
    }
}
