<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/5/16
 * Time: 4:32 PM
 */

namespace Sharin\Behaviours;
use Sharin\C;
use Sharin\Core\Cache;
use Sharin\Developer;
use Sharin\Interfaces\BehaviourInterface;

/**
 * Class StaticCacheBehaviour
 * 用于静态缓存控制的行为
 * @package Sharin\Behaviours
 */
class StaticCacheBehaviour implements BehaviourInterface{
    use C;

    const CONF_NAME = 'behaviour/static_cache';
    const CONF_CONVENTION = [
        'CACHE_URL_ON'  => false,
        'CACHE_PATH_ON' => false,
    ];

    private static $config = [];

    public static function __init(){
        self::$config = self::getConfig();
    }

    public function run($tag, $parameters) {}

    public function onStart(){
        if(self::$config['CACHE_URL_ON']){
            $identify = self::getURLIdentify();
            $content = Cache::get($identify,null);
            //'CACHE_PATH_ON'     => true,
            if(null !== $content){
                Developer::trace('load url cache!');
                exit($content);
            } else {
                //打开输出控制缓冲
                Cache::begin($identify);
            }
        }
    }

    public function onDispatch($params){
        if(self::$config['CACHE_PATH_ON']){
            $pidentify = self::getPathIdentify($params);
            $content = Cache::get($pidentify,null);
            if(null !== $content){
                Developer::trace('load path cache!');
                exit($content);
            }else{
                Cache::begin($pidentify);
            }
        }
    }

    public function onStop($params){
        $actionback = $params[0];
        if(isset($actionback)){
            if (0 == $actionback) $actionback = ONE_DAY;
            //缓存行为
            if(self::$config['CACHE_URL_ON']){
                $identify = self::getURLIdentify();
                Developer::trace('build url cache done!');
                exit(Cache::end($actionback,$identify));
            }
            if(self::$config['CACHE_PATH_ON']){
                $identify = self::getPathIdentify($params[1]);
                Developer::trace('build path cache done!');
                exit(Cache::end($actionback,$identify));
            }
        }else{
            Developer::trace('flush streightly!');
        }
    }

    private static function getURLIdentify(){
        static $_id = null;
        if(!$_id) $_id = md5("{$_SERVER['REQUEST_SCHEME']}_{$_SERVER['HTTP_HOST']}-{$_SERVER['REQUEST_URI']}");
        return $_id;
    }

    private static function getPathIdentify(array $params){
        return md5("{$params[0]}_{$params[1]}_{$params[2]}");
    }

}