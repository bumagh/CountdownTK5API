<?php

namespace app\common\model;

use think\Model;

class CountdownModel extends Model
{
    // 设置表名
    protected $table = 'countdowns';

    // 设置主键
    protected $pk = 'id';

    // 自动时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    // 类型转换
    protected $type = [
        'id' => 'integer',
        'category_id' => 'integer',
        'user_id' => 'integer',
        'is_pinned' => 'boolean',
        'repeat_cycle' => 'integer',
        'is_archived' => 'boolean',
    ];

    // 日期字段
    protected $dateFormat = 'Y-m-d';

    // 关联用户
    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }

    // 关联分类
    public function category()
    {
        return $this->belongsTo('Category', 'category_id');
    }

    // 获取重复频率文本
    public function getRepeatFrequencyTextAttr($value, $data)
    {
        $repeatFrequency = $data['repeat_frequency'] ?? '不重复';
        $repeatCycle = $data['repeat_cycle'] ?? 0;
        if ($repeatCycle == 0 || $repeatFrequency == '不重复') {
            return '不重复';
        }
        return "每{$repeatCycle}{$repeatFrequency}";
    }

    // 计算距离目标日期的天数
    public function getDaysDiffAttr($value, $data)
    {
        $targetDate = $data['date'];
        $today = date('Y-m-d');
        return ceil((strtotime($targetDate) - strtotime($today)) / (60 * 60 * 24));
    }

    // 获取显示日期（考虑重复）
    public function getDisplayDateAttr($value, $data)
    {
        // 如果不是重复日程，返回原日期
        if ($data['repeat_cycle'] == 0 || $data['repeat_frequency'] == '不重复') {
            return $data['date'];
        }

        $today = date('Y-m-d');
        $nextDate = $data['date'];

        // 如果起始日期在未来，直接返回
        if ($nextDate > $today) {
            return $nextDate;
        }

        // 循环计算下一个未来日期
        while ($nextDate <= $today) {
            switch ($data['repeat_frequency']) {
                case '天重复':
                    $nextDate = date('Y-m-d', strtotime($nextDate . " +{$data['repeat_cycle']} days"));
                    break;
                case '周重复':
                    $nextDate = date('Y-m-d', strtotime($nextDate . " +{$data['repeat_cycle']} weeks"));
                    break;
                case '月重复':
                    $nextDate = date('Y-m-d', strtotime($nextDate . " +{$data['repeat_cycle']} months"));
                    break;
                case '年重复':
                    $nextDate = date('Y-m-d', strtotime($nextDate . " +{$data['repeat_cycle']} years"));
                    break;
            }
        }

        return $nextDate;
    }
}
