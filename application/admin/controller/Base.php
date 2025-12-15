<?php

namespace app\api\controller;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use think\Controller;

class Base extends Cross
{
    //
    protected function initialize()
    {
        parent::initialize();

        $header = request()->header();
        if (!isset($header['token']) || empty($header['token'])) {
            json(['code' => 1, 'msg' => '请先登录'])->send();
            die;
        }

        $info = JWT::decode($header['token'], new Key('api', 'HS256'));
        $this->aid = $info->aid;
        // json(['code' => 1, 'token' => $info])->send();
    }
}
