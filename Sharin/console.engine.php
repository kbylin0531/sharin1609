<?php
/**
 * Powered by linzhv@qq.com.
 * Github: git@github.com:linzongho/sharin.git
 * User: root
 * Date: 16-9-3
 * Time: 上午11:09
 */
namespace {

    use Sharin\ClassLoader;
    use Sharin\Exceptions\ParameterInvalidException;

    require __DIR__.'/Common/constant.console.inc';

    require __DIR__.'/Common/environment.inc';

    //error  display
    if(SR_DEBUG_MODE_ON){
        error_reporting(-1);
        ini_set('display_errors',1);
    }else{
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);//php5.3version use code: error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
        ini_set('display_errors', 0);
    }

    /**
     * Class Sharingan
     */
    final class Sharingan {

        private static $config = [
            'OS_ENCODING'       => 'UTF-8',//file system encoding,GB2312 for windows,and utf8 for most linux
            'TIMEZONE_ZONE'     => 'Asia/Shanghai',

            'ERROR_HANDLER'         => null,
            'EXCEPTION_HANDLER'     => null,
        ];

        /**
         * initize the behaviour of this system
         * @param array $config system configuration
         * @return void
         * @throws ParameterInvalidException
         */
        public static function init(array $config=NONE_CONFIG){
            static $needs = true;
            if($needs){//防止重复初始化
                $config and self::$config = array_merge(self::$config,$config);

                define('SR_OS_ENCODING',self::$config['OS_ENCODING']);
                define('SR_EXCEPTION_CLEAN',self::$config['EXCEPTION_CLEAN']);
                date_default_timezone_set(self::$config['TIMEZONE_ZONE']) or die('Date default timezone set failed!');

                //behavior
                spl_autoload_register([ClassLoader::class,'load'],false,true) or die('Faile to register class autoloader!');
                register_shutdown_function(function (){});

                $needs = false;
            }
        }


        /**
         * 逆初始化，取消错误异常等注册并恢复原状
         * @static
         */
        public static function unregister(){
            self::$config['ERROR_HANDLER'] and set_error_handler(self::$config['ERROR_HANDLER']);
            self::$config['EXCEPTION_HANDLER'] and set_exception_handler(self::$config['EXCEPTION_HANDLER']);
        }

    }


}