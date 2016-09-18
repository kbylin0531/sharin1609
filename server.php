<?php
namespace Workerman {
    /**
     * Class Autoloader
     * Workerman的自动加载器
     * @package Workerman
     */
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
            if (strpos($class_path, 'Workerman/') < 2 or strpos($class_path, 'GatewayWorker/') < 2) {
                $class_file = SR_PATH_PLUGIN.DIRECTORY_SEPARATOR.$class_path.'.php';
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
}

namespace {

    use Workerman\Autoloader;
    use Workerman\Worker;
    use Workerman\WebServer;

    if(PHP_SAPI !== 'cli') die('Please run as client mode!');
    include 'Sharin/console.engine.php';

    /**
     * Class Server
     */
    final class Server {
        /**
         * @var Worker[]
         */
        private static $workers = [];
        /**
         * @var WebServer[]
         */
        private static $webservers = [];

        /**
         * 命令行参数管理
         * @param int $index
         * @param mixed $replacement
         * @return mixed
         */
        public static function arg($index=1,$replacement=''){
            global $argv;
            return isset($argv[$index])? $argv[$index] : $replacement;
        }

        /**
         * 开启应用
         */
        public static function start(){
            $entry = SR_PATH_APP.DIRECTORY_SEPARATOR.'start.php';
            if(is_file($entry)){
                include $entry;
            }else{
                die("Server script '$entry' is not a file!\n");
            }
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

        /**
         * 运行环境检查
         * @return bool|string
         */
        public static function checkEnv(){
            global $argv;
            //补充server.php占据的位置
            if(isset($argv[2])){
                $argv[1] = $argv[2];
                echo "ARG-1:{$argv[1]} \n";
            }
            if(isset($argv[3])){
                $argv[2] = $argv[3];
                echo "ARG-2:{$argv[2]} \n";
            }
            if(isset($argv[4])) {
                $argv[3] = $argv[4];
                echo "ARG-3:{$argv[3]} \n";
            }
            $error = '';
            // 检查扩展
            if(!extension_loaded('pcntl'))  $error = "Please install pcntl extension. See http://doc3.workerman.net/install/install.html\n";
            if(!extension_loaded('posix')) $error = "Please install posix extension. See http://doc3.workerman.net/install/install.html\n";
            return $error;
        }

    }



    //开启脚本
    $appname = Server::arg(1);
    if(!$appname){
        die("Please run as 'php server.php [APPLICATION_NAME] [ACTION_NAME]'!\n");
    }
    define('SR_APP_NAME', ucfirst($appname));
    define('SR_PATH_APP', SR_PATH_SERVER.DIRECTORY_SEPARATOR.SR_APP_NAME);
    spl_autoload_register([Autoloader::class,'loadByNamespace']);
    if($error = Server::checkEnv()) die($error);
    Server::start();

}

