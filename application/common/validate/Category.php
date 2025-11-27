<?php

namespace app\common\validate;

use think\Validate;

class Category extends Validate
{
    protected $rule = [
        'name' => 'require|max:100',
        'icon' => 'max:10',
        'color' => 'max:20',
        'user_id' => 'require|integer',
    ];

    protected $message = [
        'name.require' => '分类名称不能为空',
        'name.max' => '分类名称不能超过100个字符',
        'icon.max' => '图标不能超过10个字符',
        'color.max' => '颜色不能超过20个字符',
        'user_id.require' => '用户ID不能为空',
        'user_id.integer' => '用户ID必须是整数',
    ];
}
