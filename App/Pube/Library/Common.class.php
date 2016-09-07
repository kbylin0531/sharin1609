<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/7/16
 * Time: 4:20 PM
 */

namespace App\Pube\Library;

class Common
{
    public static function touchCookie($cookie){
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
                self::touchCookie($inputcookie);
            }
            curl_setopt($ch,CURLOPT_COOKIE,$inputcookie);
        }
        if($outputcookie){
            if(strpos($outputcookie,'/') === 0){
                self::touchCookie($outputcookie);
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
                self::touchCookie($inputcookie);
            }
            curl_setopt($ch,CURLOPT_COOKIE,$inputcookie);
        }
        if($outputcookie){
            if(strpos($inputcookie,'/') === 0){
                self::touchCookie($inputcookie);
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