<?php

use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\Lib\DbConnection;

function db_connect(): DbConnection
{
    $config = array(
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'moleweb',
        'password' => 'llc-123456',
        'dbname' => 'moleweb',
        'charset' => 'utf8'
    );
    //查询最近1周有无需要推送的离线信息
    return new DbConnection(
        $config['host'],
        $config['port'],
        $config['user'],
        $config['password'],
        $config['dbname'],
        $config['charset']
    );
}

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    public static $db;
    public static function onConnect($client_id)
    {
        // Events::$db = db_connect();  //数据库链接
    }

    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param mixed $message 具体消息
     */
    public static function onMessage($client_id, $messageSrc)
    {
        echo $messageSrc;
        $message = json_decode($messageSrc, true);
        $message_type = $message['method'] ?? null;
        switch ($message_type) {
            case WsApiEnum::WsPing:
                GateWay::sendToCurrentClient(json_encode(new WsCommonResp(WsApiEnum::WsPing, 'pong', 'pong')));
                // $isLogin = false;
                // $username = $message['data']['username'];
                // $password = $message['data']['password'];
                // $resMsg = Events::$db->select('uid,realname')->from('mol_member')
                //     ->where("username = '{$username}'")
                //     ->query();
                // if (!empty($resMsg)) {
                //     $isLogin = true;
                //     $uid = $resMsg[0]["uid"];
                //     $realname = $resMsg[0]["realname"];
                //     $resData = Events::$db->select('money,curgametype')->from('mol_userdata')
                //         ->where("userid = '{$uid}'")
                //         ->query();
                //     if (!empty($resData)) {
                //         $msg = json_encode(new WsLoginResp($isLogin, WsApiEnum::WsLoginSuccess, new WsUser($uid, $resData[0]["money"], $resData[0]["curgametype"], $realname)));
                //         GateWay::sendToCurrentClient($msg);
                //     }
                // }

                break;
            default:
                # code...
                break;
        }
    }

    /**
     * 当用户断开连接时触发
     * @param int $client_id 连接id
     */
    public static function onClose($client_id)
    {
        // 向所有人发送 
        // Events::$db->closeConnection();
    }
}



class WsApiEnum
{
    const WsRegister = "gcuserService/register";
    const WsLogin = "gcuserService/login";
    const WsLoginSuccess = "userLogin";
    const WsGetDeskList = "getDeskList";
    const WsEnterRoom = "enterRoom";
    const WsLeaveRoom = "xxFishService/leaveRoom";
    const WsPing = "Ping";
}

class WsEnterRoomResp
{
    public $method = WsApiEnum::WsEnterRoom;
    public $success;
    public $messageStatus;
    public $seatId;
    public function __construct($success, $messageStatus, $seatId)
    {
        $this->success = $success;
        $this->messageStatus = $messageStatus;
        $this->seatId = $seatId;
    }
}
class WsEnterRoomQuery
{
    public $userId;
    public $gameType;
    public $deskId;
    public $seatId;
}
class WsCommonResp
{
    public $method;
    public $msg;
    public $data;
    public function __construct($method, $msg, $data)
    {
        $this->method = $method;
        $this->msg = $msg;
        $this->data = $data;
    }
}
class WsLoginResp
{
    public $method;
    public $isLogin;
    public $user;
    public function __construct($isLogin, $method, $user)
    {
        $this->method = $method;
        $this->isLogin = $isLogin;
        $this->user = $user;
    }
}
class WsGetDeskListResp
{
    public $method;
    public $deskAll;
    public function __construct($method, $deskAll)
    {
        $this->method = $method;
        $this->deskAll = $deskAll;
    }
}
class WsDesk
{
    public $gameType;
    public $id;
    public $name;
    public $seatNum;
    public $minGold;
    public $seats = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0];
    public function __construct($gameType, $id, $name, $seatNum, $seats, $minGold)
    {
        $this->gameType = $gameType;
        $this->id = $id;
        $this->name = $name;
        $this->seatNum = $seatNum;

        $this->seats = $seats;
        $this->minGold = $minGold;
    }
}
class WsUser
{
    public  $id;
    public  $gameGold;
    public  $gameId;
    public $username;
    public function __construct($id, $gameGold, $gameId, $username)
    {
        $this->id = $id;
        $this->gameGold = $gameGold;
        $this->gameId = $gameId;
        $this->username = $username;
    }
}
