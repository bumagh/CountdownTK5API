<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\common\model\GoodsModel;
use app\common\model\SoldierModel;
use app\common\model\TaskModel;
use think\Request;

class LoginGame extends Base
{
    public function loginPlayerSel(Request $request)
    {
        $dbGoods = new GoodsModel();
        $bagItemDatas = $dbGoods->getListByBagIdAndStatus($request->param('bag_id'), 1);
        $dbSoldier = new SoldierModel();
        $soldierItemDatas = $dbSoldier->getListByCharacterId($request->param('character_id'));
        $dbTask = new TaskModel();
        $taskItems = $dbTask->getListByCharacterId($request->param('character_id'));
        return json(['code' => 0, 'msg' => '获取角色信息成功', 'data' => [
            'bagItemDatas' => $bagItemDatas,
            'soldierItemDatas' => $soldierItemDatas,
            'taskItems'=>$taskItems
        ]]);
    }
}
