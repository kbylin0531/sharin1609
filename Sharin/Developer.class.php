<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/18/16
 * Time: 3:26 PM
 */

namespace Sharin;
use Sharin\Core\Trace;

/**
 * Class Developer
 * Developer tool to improve performance and debug
 * @package Sharin
 */
final class Developer {

    private static $showTrace = SR_DEBUG_MODE_ON;

    /**
     * @var array
     */
    private static $highlightes = [];
    /**
     * @var array
     */
    private static $_status = [];
    /**
     * @var array
     */
    private static $_traces = [];

    /**
     * Open the page trace
     * @return void
     */
    public static function openTrace(){
        self::$showTrace = true;
    }

    /**
     * Close the page trace
     * @return void
     */
    public static function closeTrace(){
        self::$showTrace = false;
    }

    /**
     * record the runtime's time and memory usage
     * @param null|string $tag tag of runtime point
     * @return void
     */
    public static function status($tag){
        SR_DEBUG_MODE_ON and self::$_status[$tag] = [
            microtime(true),
            memory_get_usage(),
        ];
    }

    /**
     * import status
     * @param string $tag
     * @param array $status
     */
    public static function import($tag,array $status){
        self::$_status[$tag] = $status;
    }

    /**
     * 记录下跟踪信息
     * @param string|mixed $message
     * @param ...
     * @return void
     */
    public static function trace($message=null){
        static $index = 0;
        if(!SR_DEBUG_MODE_ON) return;
        if(null === $message){
            self::$showTrace and Trace::show(self::$highlightes,self::$_traces,self::$_status);
        }else{
            $location = debug_backtrace();
            if(isset($location[0])){
                $location = "{$location[0]['file']}:{$location[0]['line']}";
            }else{
                $location = $index ++;
            }
            if(func_num_args() > 1) $message = var_export(func_get_args(),true);
            if(!is_string($message)) $message = var_export($message,true);
            if(isset(self::$_traces[$location])){
                $index ++;//it may called multi-times in some place
                $location = "$location ($index)";
            }
            self::$_traces[$location] = $message;
        }
    }
}