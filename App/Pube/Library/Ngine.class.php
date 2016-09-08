<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/7/16
 * Time: 4:20 PM
 */
namespace Library;

defined('PUBE_BASE_DIR') or define('PUBE_BASE_DIR',dirname(__DIR__).'/');
defined('PUBE_DATA_DIR') or define('PUBE_DATA_DIR',PUBE_BASE_DIR.'Data/');
defined('PUBE_COOKIE_DIR') or define('PUBE_COOKIE_DIR',PUBE_DATA_DIR.'Cookie/');
defined('PUBE_SCRIPT_DIR') or define('PUBE_SCRIPT_DIR',dirname($_SERVER['SCRIPT_FILENAME']).'/');

define('NOW',$_SERVER['REQUEST_TIME']);

abstract class Ngine {

    public static function init(){
        spl_autoload_register(function ($clsnm){
            static $_map = [];
            if(false !== strpos(ltrim($clsnm,'\\'),'Library\\')){
                //类名称以Library开头的都认为可能属于该类库
                $path = PUBE_BASE_DIR.str_replace('\\', '/', $clsnm).'.class.php';
                if(is_readable($path)) include_once $_map[$clsnm] = $path;
            }
        },true,true);
    }

    /**
     * 检查文件如果文件不存在则创建一个空的文件，并且解决上层目录的问题
     * @param $cookie
     * @throws \Exception
     */
    public static function touch($cookie){
        $dir = dirname($cookie);
        if(!is_dir($dir)){
            if(!mkdir($dir,0777,true)){
                throw new \Exception('创建cookie存放目录失败');
            }
        }
        if(!is_writable($dir)){
            if(!chmod($dir,0777)){
                throw new \Exception('为cookie存放目录添加写权限失败');
            }
        }
        touch($cookie);
    }

    public static function get($url,$inputcookie,$outputcookie,$withhead=false){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, $withhead); //将头文件的信息作为数据流输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        if($inputcookie){
            if(strpos($inputcookie,'/') === 0){
                self::touch($inputcookie);
            }
            curl_setopt($ch,CURLOPT_COOKIE,$inputcookie);
        }
        if($outputcookie){
            if(strpos($outputcookie,'/') === 0){
                self::touch($outputcookie);
            }
            curl_setopt($ch, CURLOPT_COOKIEFILE, $outputcookie);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $outputcookie);
        }
        $content = curl_exec($ch);
        curl_close($ch);
        return false === $content ? '': (string)$content;
    }

    public static function post($url,$fields,$inputcookie,$outputcookie,$withhead=false,array $other=[]){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, $withhead); //将头文件的信息作为数据流输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        if($inputcookie){
            if(strpos($inputcookie,'/') === 0){
                self::touch($inputcookie);
            }
            curl_setopt($ch,CURLOPT_COOKIE,$inputcookie);
        }
        if($outputcookie){
            if(strpos($inputcookie,'/') === 0){
                self::touch($inputcookie);
            }
            curl_setopt($ch, CURLOPT_COOKIEFILE, $outputcookie);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $outputcookie);
        }
        foreach ($other as $k=>$v){
            curl_setopt($ch,$k,$v);
        }
        $content = curl_exec($ch);
        curl_close($ch);
        return false === $content ? '': (string)$content;
    }


}