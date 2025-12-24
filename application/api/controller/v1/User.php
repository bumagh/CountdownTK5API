<?php

namespace app\api\controller\v1;


use think\Db;
use think\Request;
use Firebase\JWT\JWT;
use think\Controller;
use app\api\controller\Cross;
use app\common\model\UserModel;
use app\common\model\CategoryModel;

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
        $user = UserModel::get($data['id']);
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
            $defaultCats = [
                ['name' => 'work', 'icon' => '💼', 'color' => '#52c41a', 'user_id' => $info->id],
                ['name' => 'family', 'icon' => '👨‍👩‍👧', 'color' => '#faad14', 'user_id' => $info->id],
                ['name' => 'life', 'icon' => '🏠', 'color' => '#1890ff', 'user_id' => $info->id],
                ['name' => 'longlife', 'icon' => '❤️', 'color' => '#f5222d', 'user_id' => $info->id],
            ];
            foreach ($defaultCats as $catName) {
                $cats = new CategoryModel();
                $saveRet = $cats->save($catName);
            }
            if (!$saveRet) {
                return json(['code' => 4, 'msg' => '默认分类创建失败',]);
            }
            //自动生成一条120岁倒数日并且置顶
            $countdownData = [
                'user_id' => $info->id,
                'title' => '120岁倒数日',
                'date' => date('Y-m-d', strtotime($info['birth_date'] . ' +120 years')),
                'is_pinned' => true,
                //从category表中获取刚创建的“长寿”分类ID
                'category_id' => $cats->id,
            ];
            $countdown = new \app\common\model\CountdownModel();
            $countdownRet = $countdown->save($countdownData);
            if (!$countdownRet) {
                return json(['code' => 5, 'msg' => '默认倒数日创建失败']);
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
    public function initInfo(Request $request)
    {
        $data = $request->param();
        $user = UserModel::get($data['id']);
        $user->save($data);
        $info = $user->where('id', $data['id'])->find();
        //自动生成四条默认分类
        $defaultCats = [
            ['name' => 'work', 'icon' => '💼', 'color' => '#52c41a', 'user_id' => $info->id],
            ['name' => 'family', 'icon' => '👨‍👩‍👧', 'color' => '#faad14', 'user_id' => $info->id],
            ['name' => 'life', 'icon' => '🏠', 'color' => '#1890ff', 'user_id' => $info->id],
            ['name' => 'longlife', 'icon' => '❤️', 'color' => '#f5222d', 'user_id' => $info->id],
        ];
        foreach ($defaultCats as $catName) {
            $cats = new CategoryModel();
            $saveRet = $cats->save($catName);
        }
        if (!$saveRet) {
            return json(['code' => 4, 'msg' => '默认分类创建失败',]);
        }
        //自动生成一条120岁倒数日并且置顶
        $countdownData = [
            'user_id' => $info->id,
            'title' => '120岁倒数日',
            'date' => date('Y-m-d', strtotime($info['birth_date'] . ' +120 years')),
            'is_pinned' => true,
            //从category表中获取刚创建的“长寿”分类ID
            'category_id' => $cats->id,
        ];
        $countdown = new \app\common\model\CountdownModel();
        $countdownRet = $countdown->save($countdownData);
        if (!$countdownRet) {
            return json(['code' => 5, 'msg' => '默认倒数日创建失败']);
        }
        return json(['code' => 200, 'msg' => '成功初始化']);
    }
}
