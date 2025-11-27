<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\common\model\AdminModel;
use think\Controller;
use think\Request;

class Admin extends Base
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
        $aid = $this->aid;
        $db = new AdminModel();
        $field = 'id,username,password,status,create_time,create_time,zone_id';
        return json($db->_lists($limit, $page, $field));
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
        $db = new AdminModel();
        if (isset($data['id']) && !empty($data['id'])) {
            $info = $db->where('id', $data['id'])->find();
            if (isset($data['password']) && !empty($data['password'])) {
                if ($info['password'] === md5($data['password'])) {
                    $data['password'] = $info['password'];
                } else
                    $data['password'] = md5($info['password']);
            }
            $res = $db->save($data, ['id' => $data['id']]);
        } else {
            $data['password'] = md5($data['password']);
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
        $db = new AdminModel();
        if ($id == 1)
            return json(['code' => 0, 'msg' => '无权限删除']);
        $res = $db->where('id', $id)->delete();
        if ($res)
            return json(['code' => 0, 'msg' => '操作成功']);
        else
            return json(['code' => 1, 'msg' => '操作失败']);
    }
}
