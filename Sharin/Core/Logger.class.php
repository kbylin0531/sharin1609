<?php
/**
 * Email: linzongho@gmail.com
 * Github: https://github.com/linzongho/Sharin
 * User: asus
 * Date: 8/22/16
 * Time: 11:40 AM
 */

namespace Sharin\Core;
use Sharin\Core;
use Sharin\SharinException;
/**
 * Class Log 日志管理类
 * @method bool write(string $content,string $path) static 写入日志信息
 * @method bool read(string $path) static 读取日志文件内容
 * @package Kbylin\System\Core
 */
class Logger extends Core {

    /**
     * @var array 日志信息
     */
    private static $records       =  [];

    const CONF_NAME = 'log';
    const CONF_CONVENTION = [
        'DRIVER_DEFAULT_INDEX' => 0,//默认的驱动标识符，类型为int或者string
        'DRIVER_CLASS_LIST' => [
            'Sharin\\Core\\Logger\\File',
        ],//驱动类列表
        'RATE'      => Logger::LOGRATE_DAY,
        //Think\Log
        'TIME_FORMAT'   =>  ' c ',
        'FILE_SIZE'     =>  2097152,
        'PATH'  => SR_PATH_RUNTIME.'/Log',
        // 允许记录的日志级别
        'LEVEL'         =>  true,//'EMERG,ALERT,CRIT,ERR,WARN,NOTIC,INFO,DEBUG,SQL',
    ];

    /**
     * 日志频率
     * LOGRATE_DAY  每天一个文件的日志频率
     * LOGRATE_HOUR 每小时一个文件的日志频率，适用于较频繁的访问
     */
    const LOGRATE_HOUR = 0;
    const LOGRATE_DAY = 1;

    /**
     * 系统预设的级别，用户也可以自定义
     */
    const LEVEL_DEBUG   = 'Debug';//错误和调试
    const LEVEL_NOTICE  = 'Notice';
    const LEVEL_INFO    = 'Info';
    const LEVEL_WARN    = 'Warn';
    const LEVEL_ERROR   = 'Error';
    const LEVEL_RECORD  = 'Record';//记录日常操作的数据信息，以便数据丢失后寻回

    /**
     * 获取日志文件的UID（Unique Identifier）
     * @param string $level 日志界别
     * @param string $datetime 日志时间标识符，如“2016-03-17/09”日期和小时之间用'/'划分
     * @return string 返回UID
     * @throws SharinException
     */
    protected static function getLogName($level=self::LEVEL_DEBUG,$datetime=null){
        if(isset($datetime)){
            $path = SR_PATH_RUNTIME."/Log/{$level}/{$datetime}.log";
        }else{
            $date = date('Y-m-d');
            $rate = self::getConfig('RATE');
            $rate or $rate = self::LOGRATE_DAY;
            switch($rate){
                case self::LOGRATE_DAY:
                    $path = SR_PATH_RUNTIME."/Log/{$level}/{$date}.log";
                    break;
                case self::LOGRATE_HOUR:
                    $hour = date('H');
                    $path = SR_PATH_RUNTIME."/Log/{$level}/{$date}/{$hour}.log";
                    break;
                default:
                    return SharinException::throwing("日志频率未定义：'{$rate}'");
            }
        }
        return $path;
    }

//--------------------------------------- 内置的静态方法:record + save  -------------------------------------------------------------------//
    /**
     * 记录日志 并且会过滤未经设置的级别
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param boolean $force  是否强制记录
     * @return $this
     */
    public static function record($message, $level=self::LEVEL_INFO, $force=false) {
        static $allowlevel = null;
        null === $allowlevel and $allowlevel = self::getConfig('LEVEL');
        if($force or $allowlevel or false !== strpos($allowlevel,$level)){
            self::$records[] =   "{$level}: {$message}\r\n";
        }
    }

    /**
     * 保存record记录的信息，该函数无需手动调用
     * @static
     * @access public
     * @param string $destination  写入目标
     * @return void
     */
    public static function save($destination='') {
        if(self::$records){
            $message    =   implode('',self::$records);

            $config = self::getConfig();
            $now = date($config['TIME_FORMAT']);
            $destination or $destination = self::getLogName(self::LEVEL_RECORD);
            // 自动创建日志目录
            $log_dir = dirname($destination);
            if (!is_dir($log_dir)) {
                mkdir($log_dir, 0755, true);
            }
            //TODO:检测日志文件大小，超过配置大小则备份日志文件重新生成
            error_log("[{$now}] ".$_SERVER['REMOTE_ADDR'].' '.$_SERVER['REQUEST_URI']."\r\n{$message}\r\n", 3,$destination);
            // 保存后清空日志缓存
            self::$records = [];
        }
    }
}
//一旦该类加载进来，那么这段语句必定执行，无需手动调用
register_shutdown_function(function(){
    Logger::save();
});