<?php
/**
 * run with command 
 * php start.php start
 */

use Workerman\Worker;
include '../../../Sharin/Library/Workman.class.php';

ini_set('display_errors', 'on');

if(($error = \Sharin\Library\Workman::checkEnv()) !== true){
    exit($error);
};

// 标记是全局启动
define('GLOBAL_START', 1);

// 加载所有Applications/*/start.php，以便启动所有服务
foreach(glob(__DIR__.'/Applications/*/start*.php') as $start_file) {
    require_once $start_file;
}
// 运行所有服务
Worker::runAll();
