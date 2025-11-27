<?php

use \Workerman\Worker;
use \GatewayWorker\Register;

require_once __DIR__ . '/../../vendor/autoload.php';
$register = new Register('text://127.0.0.1:6993');

// 如果不是在根目录启动，则运行runAll方法
if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
