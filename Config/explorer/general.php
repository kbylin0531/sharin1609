<?php
/**
 * Email: linzongho@gmail.com
 * Github: https://github.com/linzongho/Pindex
 * User: asus
 * Date: 8/23/16
 * Time: 1:33 PM
 */

//配置数据,可在setting_user.php中更改覆盖
$config['settings'] = array(
    'download_url_time' => 0,            //下载地址生效时间，按秒计算，0代表不限制，默认不限制
    'upload_chunk_size' => 1024 * 1024 * 2,    //上传分片大小；默认1M
    'version_desc' => 'product',
);

//初始化系统配置
$config['setting_system_default'] = array(
    'system_password' => rand_string(10),
    'system_name' => "KodExplorer",
    'system_desc' => "——芒果云.资源管理器",
    'path_hidden' => ".DS_Store,.gitignore",//目录列表隐藏的项
    'auto_login' => "0",            // 是否自动登录；登录用户为guest
    'first_in' => "explorer",    // 登录后默认进入[explorer desktop,editor]
    'new_user_app' => "365日历,pptv直播,ps,qq音乐,搜狐影视,时钟,天气,水果忍者,计算器,豆瓣电台,音悦台,icloud",
    'new_user_folder' => "download,music,image,desktop",
);

// 配置项可选值
$config['setting_all'] = array(
    'language' => "en:English,zh_CN:简体中文,zh_TW:繁體中文",
    'themeall' => "default/:<b>areo blue</b>:default,simple/:<b>simple</b>:simple,metro/:<b>metro</b>:metro,metro/blue_:metro-blue:color,metro/leaf_:metro-green:color,metro/green_:metro-green+:color,metro/grey_:metro-grey:color,metro/purple_:metro-purple:color,metro/pink_:metro-pink:color,metro/orange_:metro-orange:color",
    'codethemeall' => "chrome,clouds,crimson_editor,eclipse,github,solarized_light,tomorrow,xcode,ambiance,idle_fingers,monokai,pastel_on_dark,solarized_dark,tomorrow_night_blue,tomorrow_night_eighties",
    'wallall' => "1,2,3,4,5,6,7,8,9,10,11,12,13",
    'musicthemeall' => "ting,beveled,kuwo,manila,mp3player,qqmusic,somusic,xdj",
    'moviethemeall' => "webplayer,qqplayer,vplayer,tvlive,youtube"
);

//新用户初始化配置
$config['setting_default'] = array(
    'list_type' => "icon",        // list||icon
    'list_sort_field' => "name",        // name||size||ext||mtime
    'list_sort_order' => "up",        // asc||desc
    'theme' => "simple/",    // app theme [default,simple,metro/,metro/black....]
    'codetheme' => "clouds",    // code editor theme
    'wall' => "7",            // wall picture
    'musictheme' => "mp3player",    // music player theme
    'movietheme' => "webplayer"    // movie player theme
);

//初始化默认菜单配置
$config['setting_menu_default'] = array(
    array('name' => 'desktop', 'type' => 'system', 'url' => ENTRY_FILE.'?desktop', 'target' => '_self', 'use' => '1'),
    array('name' => 'explorer', 'type' => 'system', 'url' => ENTRY_FILE.'?explorer', 'target' => '_self', 'use' => '1'),
    array('name' => 'editor', 'type' => 'system', 'url' => ENTRY_FILE.'?editor', 'target' => '_self', 'use' => '1'),
    array('name' => 'adminer', 'type' => '', 'url' => 'adminer.php', 'target' => '_blank', 'use' => '1')
);

//权限配置；精确到需要做权限控制的控制器和方法
//需要权限认证的Action;root组无视权限
$config['role_setting'] = array(
    'explorer' => array(
        'mkdir', 'mkfile', 'pathRname', 'pathDelete', 'zip', 'unzip', 'pathCopy', 'pathChmod',
        'pathCute', 'pathCuteDrag', 'pathCopyDrag', 'clipboard', 'pathPast', 'pathInfo',
        'serverDownload', 'fileUpload', 'search', 'pathDeleteRecycle',
        'fileDownload', 'zipDownload', 'fileDownloadRemove', 'fileProxy', 'officeView', 'officeSave'),
    'app' => array('user_app', 'init_app', 'add', 'edit', 'del'),//
    'user' => array('changePassword'),//可以设立公用账户
    'editor' => array('fileGet', 'fileSave'),
    'userShare' => array('set', 'del'),
    'setting' => array('set', 'system_setting', 'php_info'),
    'fav' => array('add', 'del', 'edit'),
    'member' => array('get', 'add', 'del', 'edit'),
    'group' => array('get', 'add', 'del', 'edit'),
);
define('KOD_VERSION', '3.22');

//数据地址定义。
$config['pic_thumb'] = BASIC_PATH . 'data/thumb/';        // 缩略图生成存放地址
$config['cache_dir'] = BASIC_PATH . 'data/cache/';        // 缓存文件地址
$config['app_startTime'] = mtime();                    //起始时间

//系统编码配置
$config['app_charset'] = 'utf-8';            //该程序整体统一编码
$config['check_charset'] = 'ASCII,UTF-8,GBK';//文件打开自动检测编码
//when edit a file ;check charset and auto converto utf-8;
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $config['system_os'] = 'windows';
    $config['system_charset'] = 'gbk';//user set your server system charset
} else {
    $config['system_os'] = 'linux';
    $config['system_charset'] = 'utf-8';
}
return $config;