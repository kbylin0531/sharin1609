<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/18/16
 * Time: 3:16 PM
 */

namespace Sharin;

/**
 * Class ClassLoader
 * manage the class auto-loading and initialization
 * @package Sharin
 */
final class ClassLoader {

    /**
     * @var array array of key-valure pairs (name to relative path)
     */
    private static $map = [];

    /**
     * import classes from outer
     * @param array $map
     */
    public static function import(array $map){
        $map and self::$map = array_merge(self::$map,$map);
    }

    /**
     * default loader for this system
     * @param string $clsnm class name
     * @return void
     */
    public static function load($clsnm){
        if(isset(self::$map[$clsnm])) {
            include_once self::$map[$clsnm];
        }else{
            $pos = strpos($clsnm,'\\');
            if(false === $pos){
                $file = SR_PATH_BASE . "/{$clsnm}.class.php";//class file place deside entrance file if has none namespace
                if(is_file($file)) include_once $file;
            }else{
                $path = SR_PATH_BASE.'/'.str_replace('\\', '/', $clsnm).'.class.php';
                if(is_file($path)) include_once self::$map[$clsnm] = $path;
            }
        }
        Utils::callStatic($clsnm,'__initializationize');
    }

    /**
     * register class autoloader
     * @param callable $autoloader
     * @throws SharinException
     */
    public static function register(callable $autoloader){
        if(!spl_autoload_register($autoloader,false,true)){
            throw new SharinException('Faile to register class autoloader!');
        }
    }

}