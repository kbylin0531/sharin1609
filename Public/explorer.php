<?php

const PINDEX_DEBUG_MODE_ON = true;
const PINDEX_PAGE_TRACE_ON = true;

include '../Sharin/web.engine.php';
Sharin::init([
    'APP_NAME'  => 'Explorer',//一个入口文件对应一个应用
]);

const ENTRY_FILE = 'explorer.php';

include SR_PATH_APP.'/Common/config/config.php';

$app = new Application();
init_lang();
init_setting();
$app->run();