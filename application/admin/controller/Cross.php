<?php

namespace app\api\controller;

use think\Controller;
use think\Request;

class Cross extends Controller
{
    protected function initialize()
    {
        parent::initialize();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, PUT, OPTIONS, DELETE');
        header('Access-Control-Allow-Headers: Origin, x-Requested-With, Content-Type,token, Accept, x-access-sign, x-access-time');
        if (request()->isOptions()) exit;
    }
}
