<?php
/**
 * Powered by linzhv@qq.com.
 * Github: git@github.com:linzongho/sharin.git
 * User: root
 * Date: 16-9-3
 * Time: 上午10:48
 */

const SR_DEBUG_MODE_ON = true;
const SR_PAGE_TRACE_ON = true;

include '../Sharin/web.engine.php';

Sharin::init([
    'APP_NAME'  => 'App',//一个入口文件对应一个应用
]);
Sharin::start();