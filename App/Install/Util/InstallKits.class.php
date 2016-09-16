<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/10/9
 * Time: 16:02
 */
namespace Application\Installer\Util;
use System\Core\Configer;

/**
 * Class InstallKits 安装过程中使用的工具包
 * @package Application\Installer\Util
 */
final class InstallKits {
    /**
     * 缓存的数据库配置
     * @var array
     */
    private static $_dbconfig = null;

    /**
     * 及时显示提示信息
     * @param string $msg 提示信息
     * @param string $class 提示信息类型
     */
    public static function flushMessageToClient($msg, $class = ''){
        echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"{$class}\")</script>";
        flush();
        ob_flush();
    }

    /**
     * 获取数据库配置
     * @param string $option 数据库连接配置项
     * @return array|null
     * @throws \System\Exception\FileNotFoundException
     */
    public static function getDatabaseConfig($option=null){
        if(!isset(self::$_dbconfig)){
            $path = str_replace('\\','/',dirname(dirname(__FILE__)).'/Configure/database.config.php');
            self::$_dbconfig = Configer::read($path);
        }
        if(isset($option)){
            return isset(self::$_dbconfig[$option])? self::$_dbconfig[$option]:null;
        }
        return self::$_dbconfig;
    }

}