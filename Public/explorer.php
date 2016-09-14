<?php
const PINDEX_DEBUG_MODE_ON = true;
const PINDEX_PAGE_TRACE_ON = true;

include '../Sharin/web.engine.php';
Sharin::init([
    'APP_NAME'  => 'Explorer',//一个入口文件对应一个应用
]);

const ENTRY_FILE = 'explorer.php';

//Pindex::init([
//    'APP_NAME' => 'Explorer',
//    'ROUTER_INDEX'      => 1,//路由
//    'DISPATCHER_INDEX'    => 1,//调度器
//]);

@set_time_limit(600);//10min pathInfoMuti,search,upload,download...
@ini_set('session.cache_expire', 600);

$web_root = str_replace($_SERVER['SCRIPT_NAME'], '', __DIR__ . '/'.ENTRY_FILE) . '/';
substr($web_root, -10) == ENTRY_FILE.'/' and $web_root =$_SERVER['DOCUMENT_ROOT'] . '/';//解决部分主机不兼容问题

define('WEB_ROOT', $web_root);
define('HOST', (\Sharin\Core\SEK::isHttps() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/');
define('BASIC_PATH', dirname(__DIR__).'/Explorer/');
define('APPHOST', HOST . str_replace(WEB_ROOT, '', BASIC_PATH));//程序根目录
define('LIB_DIR', BASIC_PATH . 'lib/');        //库目录
define('FUNCTION_DIR', LIB_DIR . 'function/');        //函数库目录
define('CLASS_DIR', LIB_DIR . 'class/');            //内目录
define('CORER_DIR', LIB_DIR . 'core/');            //核心目录
define('DATA_PATH', BASIC_PATH . 'data/');       //用户数据目录
define('LOG_PATH', DATA_PATH . 'log/');         //日志目录
define('USER_SYSTEM', DATA_PATH . 'system/');      //用户数据存储目录
define('DATA_THUMB', DATA_PATH . 'thumb/');       //缩略图生成存放
define('LANGUAGE_PATH', DATA_PATH . 'i18n/');        //多语言目录

define('TEMPLATE', BASIC_PATH . 'View/');    //模版文件路径
define('CONTROLLER_DIR', BASIC_PATH . 'Controller/'); //控制器目录
define('MODEL_DIR', BASIC_PATH . 'Model/');        //模型目录

define('STATIC_JS', 'app');  //_dev(开发状态)||app(打包压缩)
define('STATIC_LESS', 'css');//less(开发状态)||css(打包压缩)
define('STATIC_PATH', "./static/");//静态文件目录
//define('STATIC_PATH','http://static.kalcaddle.com/static/');//静态文件统分离,可单独将static部署到CDN
//可以自定义【用户目录】和【公共目录】;移到web目录之外，可以使程序更安全, 就不用限制用户的扩展名权限了;
define('USER_PATH', DATA_PATH . 'User/');        //用户目录
//自定义用户目录；需要先将data/User移到别的地方 再修改配置，例如：
//define('USER_PATH',   DATA_PATH .'/Library/WebServer/Documents/User');
define('PUBLIC_PATH', DATA_PATH . 'public/');     //公共目录
//公共共享目录,读写权限跟随用户目录的读写权限 再修改配置，例如：
//define('PUBLIC_PATH','/Library/WebServer/Documents/Public/');
//office服务器配置；默认调用的微软的接口，程序需要部署到外网。本地部署weboffice 引号内填写office解析服务器地址 形如:  http://---/view.aspx?src=
define('OFFICE_SERVER', "https://view.officeapps.live.com/op/view.aspx?src=");

include(FUNCTION_DIR . 'web.function.php');
include(FUNCTION_DIR . 'file.function.php');
include(FUNCTION_DIR . 'common.function.php');
include(FUNCTION_DIR . 'util.function.php');

include(CLASS_DIR . 'fileCache.class.php');
include(CORER_DIR . 'Controller.class.php');
include(CORER_DIR . 'Model.class.php');

function stripslashes_deep($value){
    $value = is_array($value) ? array_map('stripslashes_deep', $value) : (isset($value) ? stripslashes($value) : null);
    return $value;
}

isset($GLOBALS['in']['PHPSESSID']) and session_id($GLOBALS['in']['PHPSESSID']);

@session_start();
session_write_close();//避免session锁定问题;之后要修改$_SESSION 需要先调用session_start()

//语言包加载：优先级：cookie获取>自动识别
//首次没有cookie则自动识别——存入cookie,过期时间无限
//首次没有cookie则自动识别——存入cookie,过期时间无限
if (isset($_COOKIE['explorer_user_language'])) {
    $lang = $_COOKIE['explorer_user_language'];
}else{//没有cookie
    $lang = \Sharin\Library\Helper\ClientAgent::getClientLang('en');
    setcookie('explorer_user_language',$lang, time()+3600*24*365);
}
if ($lang == '') $lang = 'en';
$lang = str_replace(array('/','\\','..','.'),'',$lang);
define('LANGUAGE_TYPE', $lang);
$GLOBALS['L'] = include(LANGUAGE_PATH.$lang.'/main.php');

//加载用户自定义配置
$setting_user = BASIC_PATH.'config/setting_user.php';
if (file_exists($setting_user)) {
    include($setting_user);
}

$GLOBALS['config'] = \Sharin\Core\Configger::parse(SR_PATH_CONFIG.'/explorer/general.php');

//init setting
$setting_file = USER_SYSTEM.'system_setting.php';
if (!file_exists($setting_file)){//不存在则建立
    $setting = $GLOBALS['config']['setting_system_default'];
    $setting['menu'] = $GLOBALS['config']['setting_menu_default'];
    \fileCache::save($setting_file,$setting);
}else{
    $setting = \fileCache::load($setting_file);
}
if (!is_array($setting)) {
    $setting = $GLOBALS['config']['setting_system_default'];
}
if (!is_array($setting['menu'])) {
    $setting['menu'] = $GLOBALS['config']['setting_menu_default'];
}
$GLOBALS['L']['kod_name'] = $setting['system_name'];
$GLOBALS['L']['kod_name_desc'] = $setting['system_desc'];
if (isset($setting['powerby'])) {
    $GLOBALS['L']['kod_power_by'] = $setting['powerby'];
}
$GLOBALS['config']['setting_system'] = $setting;//全局


\Sharin\Core\Dispatcher::using(1);
\Sharin\Core\Router::using(1);

Sharin::start();