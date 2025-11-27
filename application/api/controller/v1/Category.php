<?php

namespace app\api\controller;

use app\common\model\CategoryModel;
use think\Controller;
use think\Request;

class Category extends Controller
{
    // 获取用户的所有分类
    public function index()
    {
        $categories = CategoryModel::where('user_id', 1)->select();
        return json(['code' => 200, 'msg' => '成功', 'data' => $categories]);
    }

    // 获取单个分类
    public function read($id)
    {
        $category = CategoryModel::get($id);
        if ($category) {
            return json(['code' => 200, 'msg' => '成功', 'data' => $category]);
        } else {
            return json(['code' => 404, 'msg' => '分类不存在']);
        }
    }

    // 添加分类
    public function save(Request $request)
    {
        $data = $request->param();
        $data['user_id'] = 1; // 当前用户ID
        $validate = new \app\common\validate\Category();
        if (!$validate->check($data)) {
            return json(['code' => 400, 'msg' => $validate->getError()]);
        }
        $category = CategoryModel::create($data);
        return json(['code' => 200, 'msg' => '添加成功', 'data' => $category]);
    }

    // 更新分类
    public function update(Request $request, $id)
    {
        $data = $request->param();
        $category = CategoryModel::get($id);
        if ($category) {
            $category->save($data);
            return json(['code' => 200, 'msg' => '更新成功', 'data' => $category]);
        } else {
            return json(['code' => 404, 'msg' => '分类不存在']);
        }
    }

    // 删除分类
    public function delete($id)
    {
        $category = CategoryModel::get($id);
        if ($category) {
            $category->delete();
            return json(['code' => 200, 'msg' => '删除成功']);
        } else {
            return json(['code' => 404, 'msg' => '分类不存在']);
        }
    }
}
