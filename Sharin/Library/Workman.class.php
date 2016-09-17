<?php
namespace {

    defined('SR_PATH_VENDOR') or define('SR_PATH_VENDOR',dirname(__DIR__)."/Vendor/");

}
namespace Sharin\Library {

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
}

namespace Workerman {

    class Autoloader {

        /**
         * Autoload root path.
         *
         * @var string
         */
        protected static $_autoloadRootPath = '';

        /**
         * Set autoload root path.
         *
         * @param string $root_path
         * @return void
         */
        public static function setRootPath($root_path)
        {
            self::$_autoloadRootPath = $root_path;
        }

        /**
         * Load files by namespace.
         *
         * @param string $name
         * @return boolean
         */
        public static function loadByNamespace($name) {
            $class_path = str_replace('\\', DIRECTORY_SEPARATOR, $name);
            if (strpos($name, 'Workerman\\') < 2 or strpos($name, 'GatewayWorker\\') < 2) {
                $class_file = SR_PATH_VENDOR.$class_path.'.php';
            } elseif(self::$_autoloadRootPath) {
                $class_file = self::$_autoloadRootPath . DIRECTORY_SEPARATOR . $class_path . '.php';
            }else{
                return false;
            }

            if (is_file($class_file)) {
                include $class_file;
                if (class_exists($name, false)) {
                    return true;
                }
            }
            return false;
        }
    }
    spl_autoload_register([Autoloader::class,'loadByNamespace']);
}



