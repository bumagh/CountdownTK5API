<?php

namespace app\api\controller\v1;


use app\common\model\CountdownModel;
use think\Controller;
use think\Request;

class Countdown extends Controller
{
    // 获取用户的倒数日列表
    public function index(Request $request)
    {
        $user_id = 1; // 当前用户ID
        $category_id = $request->param('category_id', 0);
        $include_archived = $request->param('include_archived', 0);

        $query = CountdownModel::where('user_id', $user_id);

        if ($category_id) {
            $query->where('category_id', $category_id);
        }

        if (!$include_archived) {
            $query->where('is_archived', false);
        }

        // 排序：置顶的在前，非置顶的在后；置顶的按更新时间降序排列
        $list = $query->order('is_pinned DESC, updated_at DESC, date ASC')
            ->select();

        // 使用获取器添加显示日期和天数差
        foreach ($list as $item) {
            $item->append(['days_diff', 'display_date', 'repeat_frequency_text']);
        }

        return json(['code' => 200, 'msg' => '成功', 'data' => $list]);
    }

    // 获取归档的倒数日
    public function archived()
    {
        $user_id = 1;
        $list = CountdownModel::where('user_id', $user_id)
            ->where('is_archived', true)
            ->order('updated_at DESC')
            ->select();

        foreach ($list as $item) {
            $item->append(['days_diff', 'display_date', 'repeat_frequency_text']);
        }

        return json(['code' => 200, 'msg' => '成功', 'data' => $list]);
    }

    // 获取单个倒数日
    public function read($id)
    {
        $countdown = CountdownModel::get($id);
        if ($countdown) {
            $countdown->append(['days_diff', 'display_date', 'repeat_frequency_text']);
            return json(['code' => 200, 'msg' => '成功', 'data' => $countdown]);
        } else {
            return json(['code' => 404, 'msg' => '倒数日不存在']);
        }
    }

    // 添加倒数日
    public function save(Request $request)
    {
        $data = $request->param();
        $data['user_id'] = 1; // 当前用户ID
        $validate = new \app\common\validate\Countdown();
        if (!$validate->check($data)) {
            return json(['code' => 400, 'msg' => $validate->getError()]);
        }
        $countdown = CountdownModel::create($data);
        $countdown->append(['days_diff', 'display_date', 'repeat_frequency_text']);
        return json(['code' => 200, 'msg' => '添加成功', 'data' => $countdown]);
    }

    // 更新倒数日
    public function update(Request $request, $id)
    {
        $data = $request->param();
        $countdown = CountdownModel::get($id);
        if ($countdown) {
            $countdown->save($data);
            $countdown->append(['days_diff', 'display_date', 'repeat_frequency_text']);
            return json(['code' => 200, 'msg' => '更新成功', 'data' => $countdown]);
        } else {
            return json(['code' => 404, 'msg' => '倒数日不存在']);
        }
    }

    // 删除倒数日
    public function delete($id)
    {
        $countdown = CountdownModel::get($id);
        if ($countdown) {
            $countdown->delete();
            return json(['code' => 200, 'msg' => '删除成功']);
        } else {
            return json(['code' => 404, 'msg' => '倒数日不存在']);
        }
    }

    // 归档倒数日
    public function archive($id)
    {
        $countdown = CountdownModel::get($id);
        if ($countdown) {
            $countdown->is_archived = true;
            $countdown->save();
            return json(['code' => 200, 'msg' => '归档成功', 'data' => $countdown]);
        } else {
            return json(['code' => 404, 'msg' => '倒数日不存在']);
        }
    }

    // 取消归档倒数日
    public function unarchive($id)
    {
        $countdown = CountdownModel::get($id);
        if ($countdown) {
            $countdown->is_archived = false;
            $countdown->save();
            return json(['code' => 200, 'msg' => '取消归档成功', 'data' => $countdown]);
        } else {
            return json(['code' => 404, 'msg' => '倒数日不存在']);
        }
    }

    // 切换置顶状态
    public function togglePin($id)
    {
        $countdown = CountdownModel::get($id);
        if ($countdown) {
            $countdown->is_pinned = !$countdown->is_pinned;
            $countdown->save();
            return json(['code' => 200, 'msg' => '操作成功', 'data' => $countdown]);
        } else {
            return json(['code' => 404, 'msg' => '倒数日不存在']);
        }
    }

    // 获取指定日期的倒数日
    public function byDate($date)
    {
        $user_id = 1;
        $list = CountdownModel::where('user_id', $user_id)
            ->where('date', $date)
            ->where('is_archived', false)
            ->select();

        foreach ($list as $item) {
            $item->append(['days_diff', 'display_date', 'repeat_frequency_text']);
        }

        return json(['code' => 200, 'msg' => '成功', 'data' => $list]);
    }
}
