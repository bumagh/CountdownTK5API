<?php

namespace app\common\model;

use think\Model;

class BaseModel extends Model
{
    protected $pk;
    protected $table;
    protected function initialize()
    {
        parent::initialize();
        $this->pk = $this->getPk();
        $this->table = $this->getTable();
    }
    public function _update($data)
    {
        if (isset($data['id']) && !empty($data['id'])) {
            $res = $this->save($data, ['id' => $data['id']]);
        } else {
            $res = $this->save($data);
        }
        if ($res) {
            return ['code' => 0, 'msg' => '操作成功'];
        } else
            return ['code' => 1, 'msg' => '操作失败'];
    }
    public function _incSave($data)
    {
        if (isset($data['id']) && !empty($data['id'])) {
            // 过滤增量字段
            $incrementData = [];

            // 遍历传入的增量数据，过滤出需要增量更新的字段
            foreach ($data as $key => $value) {
                // 假设增量字段名是以 'inc_' 开头，且值为增量值
                if (strpos($key, 'inc_') === 0 && is_numeric($value)) {
                    $field = substr($key, 4); // 去掉 'inc_' 前缀，得到实际字段名
                    $incrementData[$field] = $value;
                }
            }

            // 如果有增量字段，进行增量更新
            if (!empty($incrementData)) {
                $res = $this->where('id', $data['id'])->inc($incrementData)->update();
            } else {
                // 如果没有增量字段，直接保存
                $res = $this->save($data, ['id' => $data['id']]);
            }
        } else {

            // 如果没有 id 则直接保存
            $res = $this->save($data);
        }

        if ($res) {
            return ['code' => 0, 'msg' => '操作成功'];
        } else {
            return ['code' => 1, 'msg' => '操作失败'];
        }
    }

    public function _del($data)
    {
        $res = $this->where('id', $data)->delete();
        if ($res)
            return ['code' => 0, 'msg' => '操作成功'];
        else
            return ['code' => 1, 'msg' => '操作失败'];
    }
    public function _lists($limit, $page, $field)
    {
        $list = $this->limit($limit)->page($page)->field($field)->select();
        if ($list) {
            $count = $this->count('id');
            return ['code' => 0, 'msg' => '获取成功', 'data' => $list, 'count' => $count];
        } else
            return ['code' => 0, 'msg' => '没数据'];
    }
    public function _listCondi($limit, $page, $field, $condi)
    {
        $list = $this->where($condi)->limit($limit)->page($page)->field($field)->select();
        if ($list) {
            $count = $this->count('id');
            return ['code' => 0, 'msg' => '获取成功', 'data' => $list, 'count' => $count];
        } else
            return ['code' => 0, 'msg' => '没数据'];
    }
    public function _listCondiOrder($limit, $page, $field, $condi,$order)
    {
        $list = $this->where($condi)->order($order)->limit($limit)->page($page)->field($field)->select();
        if ($list) {
            $count = $this->count('id');
            return ['code' => 0, 'msg' => '获取成功', 'data' => $list, 'count' => $count];
        } else
            return ['code' => 0, 'msg' => '没数据'];
    }
}
