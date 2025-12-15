<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\api\controller\Cross;
use app\common\model\MergeModel;
use think\Request;
use think\db;

class Merge extends Cross
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
  
    public function index(Request $request)
    {
        $limit = $request->param('limit') ? $request->param('limit') : 1;
        $page = $request->param('page') ? $request->param('page') : 1;
        $db = new MergeModel();
        $field = 'id';
        $list = $db->limit($limit)->page($page)->order('id DESC')->select();
        if ($list) {
            return json(['code' => 0, 'msg' => '获取成功', 'data' => $list]);
        } else
            return json(['code' => 1, 'msg' => '没数据']);
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
        $db = new MergeModel();
        return json($db->_update($data));
    }

    public function incSave(Request $request)
    {
        $data = $request->param();
        $db = new MergeModel();
        return json($db->_incSave($data));
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
        // $id = $request->param('id');
        // $db = new AdminModel();
        // if ($id == 1)
        //     return json(['code' => 0, 'msg' => '无权限删除']);
        // $res = $db->where('id', $id)->delete();
        // if ($res)
        //     return json(['code' => 0, 'msg' => '操作成功']);
        // else
        //     return json(['code' => 1, 'msg' => '操作失败']);
    }
}
