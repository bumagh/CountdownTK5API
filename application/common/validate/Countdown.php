<?php

namespace app\common\validate;

use think\Validate;

class Countdown extends Validate
{
    protected $rule = [
        'title' => 'require|max:200',
        'date' => 'require|dateFormat:Y-m-d',
        'category_id' => 'require|integer',
        'user_id' => 'require|integer',
        'is_pinned' => 'boolean',
        'repeat_cycle' => 'integer',
        'repeat_frequency' => 'in:不重复,天重复,周重复,月重复,年重复',
        'is_archived' => 'boolean',
    ];

    protected $message = [
        'title.require' => '标题不能为空',
        'title.max' => '标题不能超过200个字符',
        'date.require' => '日期不能为空',
        'date.dateFormat' => '日期格式不正确，应为YYYY-MM-DD',
        'category_id.require' => '分类ID不能为空',
        'category_id.integer' => '分类ID必须是整数',
        'user_id.require' => '用户ID不能为空',
        'user_id.integer' => '用户ID必须是整数',
        'is_pinned.boolean' => '置顶状态必须是布尔值',
        'repeat_cycle.integer' => '重复周期必须是整数',
        'repeat_frequency.in' => '重复频率必须是：不重复、天重复、周重复、月重复、年重复',
        'is_archived.boolean' => '归档状态必须是布尔值',
    ];
}
