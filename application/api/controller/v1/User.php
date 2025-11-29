<?php

namespace app\api\controller\v1;


use app\common\model\UserModel;
use think\Controller;
use think\Request;

class User extends Controller
{
    // 获取当前用户信息（假设当前用户ID为1，实际应根据登录状态获取）
    public function index()
    {
        $user = UserModel::get(1);
        if ($user) {
            return json(['code' => 200, 'msg' => '成功', 'data' => $user]);
        } else {
            return json(['code' => 404, 'msg' => '用户不存在']);
        }
    }

    // 更新用户信息
    public function update(Request $request)
    {
        $data = $request->param();
        $user = UserModel::get(1);
        if ($user) {
            $user->save($data);
            return json(['code' => 200, 'msg' => '更新成功', 'data' => $user]);
        } else {
            return json(['code' => 404, 'msg' => '用户不存在']);
        }
    }
}
