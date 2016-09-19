<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/14/16
 * Time: 11:18 AM
 */

namespace Library;

/**
 * Class PlatformManager
 * 平台管理
 * @package Library
 */
class PlatformManager {

    /**
     * @var array 平台标识符对平台实现类的映射关系列表
     */
    protected static $map = [
        'ec21'  => 'Library\\Platform\\EC21Platform',
        'diytrace'  => '?????',
        //..........................
    ];
    /**
     * @var array 平台单例列表
     */
    protected static $instances = [];

    /**
     * 实例化一个平台实现类
     * @param string $shorname 平台类的简写名词，对应$map属性中的健
     * @return PlatformInterface 返回实现了平台接口的类
     */
    public static function instance($shorname){
        if(!isset(self::$instances[$shorname])){
            $clsnm = self::$map[$shorname];
            self::$instances[$shorname] = new $clsnm();
        }
        return self::$instances[$shorname];
    }

}