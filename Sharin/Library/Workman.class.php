<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 9/17/16
 * Time: 11:00 AM
 */
namespace Sharin\Library;
use Workerman\WebServer;
use Workerman\Worker;

/**
 * Class Workman
 * @package Sharin\Library
 */
class Workman {

    /**
     * @var Worker[]
     */
    private static $workers = [];
    /**
     * @var WebServer[]
     */
    private static $webservers = [];

    /**
     * Load files by namespace.
     * @param string $name
     * @return boolean
     */
    public static function _autoload($name) {
        $class_path = str_replace('\\', DIRECTORY_SEPARATOR, $name);
        if (strpos($name, 'Workerman\\') === 0) {
            $class_file = SR_PATH_FRAMEWORK . "/Vendor/{$class_path}.php";
            if (is_file($class_file)) {
                require $class_file;
                if (class_exists($name, false)) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * Create a Websocket server
     * @param string $socketName
     * @param array $contextOpts
     * @return Worker
     */
    public static function getWorker($socketName = '', array $contextOpts = []){
        if(empty(self::$workers[$socketName])){
            self::$workers[$socketName] = new Worker($socketName,$contextOpts);
        }
        return self::$workers[$socketName];
    }

    /**
     * @param string $socketName
     * @param array $contextOpts
     * @return WebServer
     */
    public static function getWebServer($socketName = '', array $contextOpts = []){
        if(empty(self::$webservers[$socketName])){
            self::$webservers[$socketName] = new WebServer($socketName,$contextOpts);
        }
        return self::$webservers[$socketName];
    }

    public static function checkEnv(){
        // 检查扩展
        if(!extension_loaded('pcntl'))
        {
            return ("Please install pcntl extension. See http://doc3.workerman.net/install/install.html\n");
        }

        if(!extension_loaded('posix'))
        {
            return ("Please install posix extension. See http://doc3.workerman.net/install/install.html\n");
        }
        return true;
    }

}

spl_autoload_register([Workman::class,'_autoload']);
