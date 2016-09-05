<?php

const SHARIN_VERSION    = 1;
const NONE_CONFIG       = null;
const USE_DEFAULT       = null;
const DRIVER_DEFAULT_INDEX  = 'DRIVER_DEFAULT_INDEX';
const DRIVER_CLASS_LIST     = 'DRIVER_CLASS_LIST';
const DRIVER_CONFIG_LIST    = 'DRIVER_CONFIG_LIST';

//数据类型
const TYPE_BOOL     = 'boolean';
const TYPE_INT      = 'integer';
const TYPE_FLOAT    = 'double';//double ,  float
const TYPE_STR      = 'string';
const TYPE_ARRAY    = 'array';
const TYPE_OBJ      = 'object';
const TYPE_RESOURCE = 'resource';
const TYPE_NULL     = 'NULL';
const TYPE_UNKNOWN  = 'unknown type';
//返回直类型
const AJAX_JSON     = 0;
const AJAX_XML      = 1;
const AJAX_STRING   = 2;
//时间常量
const ONE_DAY   = 86400;
const ONE_WEEK  = 604800;
const ONE_MONTH = 2592000;
//数据库操作类型
const ACTION_SELECT = 0;//查询操作,将使用到$_fields和$_where字段
const ACTION_CREATE = 1;//添加操作,将使用到$_fields字段
const ACTION_UPDATE = 2;//更新操作,将使用到$_fields和$_where字段
const ACTION_DELETE = 3;//删除操作,将使用到$_where字段

if(PHP_SAPI === 'cli') {include 'console.engine.php';exit;}//console.engine will do something different from web.engin

define('SR_IS_WIN',false !== stripos(PHP_OS, 'WIN'));//const IS_WINDOWS = PHP_OS === 'WINNT';
defined('SR_DEBUG_MODE_ON') or define('SR_DEBUG_MODE_ON', true);
defined('SR_PAGE_TRACE_ON') or define('SR_PAGE_TRACE_ON', true);//在处理微信签名检查时会发生以外的错误

define('SR_NOW_MICRO', $_SERVER['REQUEST_TIME_FLOAT']);//(int)($_SERVER['REQUEST_TIME_FLOAT']*1000)//isset($_SERVER['REQUEST_TIME_FLOAT'])? $_SERVER['REQUEST_TIME_FLOAT']:microtime(true)
define('SR_NOW',$_SERVER['REQUEST_TIME']);
define('SR_IS_AJAX',    ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtoupper($_SERVER['HTTP_X_REQUESTED_WITH']) == 'XMLHTTPREQUEST') ));
define('SR_IS_POST',    strtoupper($_SERVER['REQUEST_METHOD']) === 'POST');//“GET”, “HEAD”，“POST”，“PUT”
define('SR_IS_HTTPS',   isset ($_SERVER ['HTTPS']) and $_SERVER ['HTTPS'] === 'on');
define('SR_HTTP_PREFIX',SR_IS_HTTPS ? 'https://' : 'http://' );

$script_dir = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/');
define('SR_PUBLIC_URL',SR_HTTP_PREFIX.$_SERVER['SERVER_NAME'].((80 == $_SERVER['SERVER_PORT'])? $script_dir : ":{$_SERVER['SERVER_PORT']}{$script_dir}"));

define('SR_PATH_BASE', dirname(dirname(__DIR__)));
const SR_PATH_FRAMEWORK = SR_PATH_BASE.'/Sharin';
const SR_PATH_CONFIG    = SR_PATH_BASE.'/Config';
const SR_PATH_RUNTIME   = SR_PATH_BASE.'/Runtime';
const SR_PATH_PUBLIC    = SR_PATH_BASE.'/Public';

const ON_INIT       = 'onInit';
const ON_INITED     = 'onInited';
const ON_START      = 'onStart';
const ON_ROUTE      = 'onRoute';
const ON_CHECK      = 'onCheck';
const ON_DISPATCH   = 'onDispatch';
const ON_STOP       = 'onStop';
const ON_SHUTDOWN   = 'onShutdown';
