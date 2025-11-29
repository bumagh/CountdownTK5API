<?php

namespace app\common\model;

use think\Model;

class UserModel extends Model
{
    // 设置表名
    protected $table = 'users';

    // 设置主键
    protected $pk = 'id';

    // 自动时间戳
    protected $autoWriteTimestamp = "datetime";
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    // 类型转换
    protected $type = [
        'id' => 'integer',
    ];

    // 关联分类
    public function categories()
    {
        return $this->hasMany('Category', 'user_id');
    }

    // 关联倒数日
    public function countdowns()
    {
        return $this->hasMany('Countdown', 'user_id');
    }
}
