<?php

namespace app\common\model;

use think\Model;

class CategoryModel extends Model
{
    // 设置表名
    protected $table = 'categories';

    // 设置主键
    protected $pk = 'id';

    // 自动时间戳
    protected $autoWriteTimestamp = "datetime";
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    // 类型转换
    protected $type = [
        'id' => 'integer',
        'user_id' => 'integer',
    ];

    // 关联用户
    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }

    // 关联倒数日
    public function countdowns()
    {
        return $this->hasMany('Countdown', 'category_id');
    }
}
