<?php

namespace app\api\controller\v1;


use app\common\model\UserModel;
use think\Controller;
use app\api\controller\Cross;
use app\common\model\CategoryModel;
use think\Request;
use Firebase\JWT\JWT;

class User extends Cross
{
    // 获取当前用户信息（假设当前用户ID为1，实际应根据登录状态获取）
    public function index()
    {
        $user = UserModel::get(input('id', 1));
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
    public function login(Request $request)
    {
        $data = $request->param();
        $user = new UserModel();

        $info = $user->where('username', $data['username'])->find();
        if (!$info) {
            return json(['code' => 1, 'msg' => '不存在']);
        }
        if ($info['password'] != ($data['password'])) {
            return json(['code' => 2, 'msg' => '账号或密码错误']);
        }
        $key = 'api';
        $payload = [
            'iss' => 'http://rbac',
            'aud' => 'http://rbac',
            'iat' => time(),
            'exp' => time() + 60 * 60 * 24 * 365,
            'aid' => $info['id'],
        ];

        $token = JWT::encode($payload, $key, 'HS256');
        return json(['code' => 200, 'msg' => '登录成功', 'data' => [
            'token' => $token,
            'userid' => $info['id']
        ]]);
    }

    public function register(Request $request)
    {
        $data = $request->param();
        $user = new UserModel();
        if (empty($data['username']) || empty($data['password'])) {
            return json(['code' => 3, 'msg' => '用户名或密码不能为空']);
        }
        $info = $user->where('username', $data['username'])->find();
        if ($info) {
            return json(['code' => 1, 'msg' => '用户已存在']);
        }
        $ret = $user->save($data);
        if ($ret) {
            //自动生成四条默认分类
            $defaultCats = [
                ['name' => '工作', 'icon' => 'work', 'color' => '#52c41a', 'user_id' => $user->id],
                ['name' => '家人', 'icon' => 'family', 'color' => '#faad14', 'user_id' => $user->id],
                ['name' => '生活', 'icon' => 'life', 'color' => '#1890ff', 'user_id' => $user->id],
                ['name' => '其他', 'icon' => 'other', 'color' => '#f5222d', 'user_id' => $user->id],
            ];
            foreach ($defaultCats as $catName) {
                $cats = new CategoryModel();
                $saveRet = $cats->save($catName);
            }
            if (!$saveRet) {
                return json(['code' => 4, 'msg' => '默认分类创建失败',]);
            }
            return json(['code' => 200, 'msg' => '注册成功',]);
        } else {
            return json(['code' => 2, 'msg' => '注册失败',]);
        }
        // $key = 'api';
        // $payload = [
        //     'iss' => 'http://rbac',
        //     'aud' => 'http://rbac',
        //     'iat' => time(),
        //     'exp' => time() + 60 * 60 * 24 * 365,
        //     'aid' => $user['id'],
        // ];

        // $token = JWT::encode($payload, $key, 'HS256');
    }
}
