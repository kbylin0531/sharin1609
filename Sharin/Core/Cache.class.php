<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-4
 * Time: 上午11:49
 */

namespace Sharin\Core;
use Sharin\Core;

/**
 * Class Cache
 * manage the cache of this system
 *
 * @method bool has(string $name) static 判断缓存是否存在
 * @method int get(string $name,$replace=null) static 读取缓存
 * @method boolean set(string $name,mixed $value,int $expire=null) static 写入缓存
 * @method int delete(string $name) static 删除缓存
 * @method int clean() static empty the cache
 *
 * @package Sharin\Core
 */
class Cache extends Core {

    const CONF_NAME = 'cache';
    const CONF_CONVENTION = [
        DRIVER_DEFAULT_INDEX => 0,
        DRIVER_CLASS_LIST => [
            'Sharin\\Core\\Cache\\File',
            'Sharin\\Core\\Cache\\Memcache',
        ],
        DRIVER_CONFIG_LIST => [
            [
                //from thinkphp ,match case
                'expire'        => 0,
                'cache_subdir'  => false,
                'path_level'    => 1,
                'prefix'        => '',
                'length'        => 0,
                'path'          => SR_PATH_RUNTIME.'/file_cache/',
                'data_compress' => false,
            ],
            [
                'host'      => 'localhost',
                'port'      => 11211,
                'expire'    => 0,
                'prefix'    => '',
                'timeout'   => 1000, // 超时时间（单位：毫秒）
                'persistent'=> true,
                'length'    => 0,
            ],
        ],
        //5分钟
        'DEFAULT_CACHE_EXPIRE'  => 300,
    ];



    /**
     * @var array id堆栈
     */
    public static $idStack = [];

    private static $error = null;

    public static function getError(){
        return self::$error;
    }

    /**
     * 缓存开始记录标记
     * @static
     * @param $identify
     * @return void
     */
    public static function begin($identify){
        ob_start();
        $level = ob_get_level();
        self::$idStack[$level] = $identify;
    }

    /**
     * 保存该level的数据成缓存
     * @static
     * @param int $expire 缓存时间，建议在10秒钟到1天之间
     * @param string|int $id4check 检查是否是该level的identifdy，如果不是则不保存
     * @return false|$content 返回缓存的内容或者false时表示发生了错误，可以使用getError方法获取错误信息
     */
    public static function end($expire=null,$id4check=null){
        $level = ob_get_level();
        if($level){
            if(isset(self::$idStack[$level])){
                $identify = self::$idStack[$level];
                if($id4check and $id4check !== $identify){
                    self::$error = "输入的检查项'{$id4check}'不同于LEVEL-{$level}的缓存项ID '{$identify}'，请确认!";
                    return false;
                }else{
                    $content = ob_get_clean();
                    $expire or $expire = self::getConfig('DEFAULT_CACHE_EXPIRE',3600);
                    self::set($identify,$content,$expire);
                    return $content;
                }
            }else{
                self::$error = "LEVEL为'{$level}'的记录不存在于缓存栈中，OB缓存可能通过其他方式开启并且在为手动关闭的情况下调用endWith方法！";
                return false;
            }
        }else{
            self::$error = 'OB缓存未处于开启状态！';
            return false;
        }
    }


}