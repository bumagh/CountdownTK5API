<?php

use \Workerman\Worker;
use \GatewayWorker\Gateway;
// 自动加载类
require_once __DIR__ . '/../../vendor/autoload.php';

$gateway = new Gateway("websocket://0.0.0.0:6992");
$gateway->name = 'FishApp';
$gateway->count = 1;
$gateway->lanIp = '127.0.0.1';
$gateway->startPort = 7001;
$gateway->registerAddress = '127.0.0.1:6993';
$gateway->onConnect = function ($connection) {
    echo ("connected");
};
// 如果不是在根目录启动，则运行runAll方法
if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
