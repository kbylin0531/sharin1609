<?php
/**
 * Email: linzongho@gmail.com
 * Github: https://github.com/linzongho/Sharin
 * User: asus
 * Date: 8/22/16
 * Time: 3:29 PM
 */
namespace Sharin\Core;
use Sharin\Exceptions\IO\FileNotFoundException;

/**
 * Class LangHelper 语言助手
 * @package System\Core
 */
class Lang{

    const LANG_ZH_CN = 'zh-cn';
    const LANG_ZH_TW = 'zh-tw';
    const LANG_EN_US = 'en-us';
    /**
     * 语言包类型，默认为简体中文
     * @var string
     */
    private static $_lang_type = self::LANG_ZH_CN;
    /**
     * 系统以外语言包的路径
     * @var string
     */
    private static $_outer_path  = null;
    /**
     * 语言包缓存
     * @var array
     */
    private static $_lang_cache = array();
    /**
     * 是否已经完成加载
     * @var bool
     */
    private static $_has_loaded = false;
    /**
     * 设置语言包类型
     * @param $type
     */
    public static function setLangType($type){
        self::$_lang_type = $type;
    }

    /**
     * 设置外部语言包的路径
     * @param $path
     * @return void
     * @throws FileNotFoundException
     */
    public static function setOuterLangPath($path){
        if(Storage::has($path)){
            self::$_outer_path = $path;
            return;
        }
        throw new FileNotFoundException($path);
    }

    /**
     * 获取语言包数组
     * @param string $type null时获取默认
     * @return array
     * @throws FileNotFoundException
     */
    public static function getLang($type=null){
        if(!self::$_has_loaded){
            return self::loadLang($type);
        }
        return self::$_lang_cache;
    }

    /**
     * 加载、获取语言包
     * @param string $type 语言包类型
     * @return array
     * @throws FileNotFoundException
     */
    public static function loadLang($type=null){
        //加载框架内置语言包
        isset($type) or $type = self::$_lang_type;
        $innerpath = SR_PATH_FRAMEWORK."Lang/{$type}.lang.php";
        if(Storage::has($innerpath)){
            $innerLang = include_once $innerpath;
        }else{
            throw new FileNotFoundException($innerpath);
        }

        //加载用户自定义语言包
        $outerLang = array();
        if(isset(self::$_outer_path)){
            $outerpath = self::$_outer_path."{$type}.lang.php";
            if(Storage::has($outerpath)){
                $outerLang = include_once $outerpath;
            }else{
                throw new FileNotFoundException($outerpath);
            }
        }
        self::$_has_loaded = true;
        return self::$_lang_cache = array_merge(self::$_lang_cache,$innerLang,$outerLang);
    }
}