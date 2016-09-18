<?php

/**
 * Email: linzongho@gmail.com
 * Github: https://github.com/linzongho/Pindex
 * User: asus
 * Date: 8/24/16
 * Time: 5:55 PM
 */
namespace CodeLibrary;
use Pindex\PindexException;
use Pindex\Utils;

class PindexURLRouter {


    /**
     * 将参数序列装换成参数数组，应用Router模块的配置
     * @param string $params 参数字符串
     * @param string $ppb
     * @param string $pkvb
     * @return array
     */
    private function toParametersArray($params,$ppb='/',$pkvb='/'){//解析字符串成数组
        $pc = [];
        if($ppb !== $pkvb){//使用不同的分割符
            $parampairs = explode($ppb,$params);
            foreach($parampairs as $val){
                $pos = strpos($val,$pkvb);
                if(false === $pos){
                    //非键值对，赋值数字键
                }else{
                    $key = substr($val,0,$pos);
                    $val = substr($val,$pos+strlen($pkvb));
                    $pc[$key] = $val;
                }
            }
        }else{//使用相同的分隔符
            $elements = explode($ppb,$params);
            $count = count($elements);
            for($i=0; $i<$count; $i += 2){
                if(isset($elements[$i+1])){
                    $pc[$elements[$i]] = $elements[$i+1];
                }else{
                    //单个将被投入匿名参数,先废弃
                }
            }
        }
        return $pc;
    }

    /**
     * 模块序列转换成数组形式
     * 且数组形式的都是大写字母开头的单词形式
     * @param string|array $modules 模块序列
     * @param string $mmbridge 模块之间的分隔符
     * @return array
     * @throws \Exception
     */
    private function toModulesArray($modules, $mmbridge='/'){
        if(is_string($modules)){
            if(false === stripos($modules,$mmbridge)){
                $modules = [$modules];
            }else{
                $modules = explode($mmbridge,$modules);
            }
        }
        is_array($modules) or PindexException::throwing('Parameter should be an array!');
        return array_map(function ($val) {
            return Utils::styleStr($val,1);
        }, $modules);
    }


    /**
     * 判断是否是重定向链接
     * 判断依据：
     *  ①以http或者https开头
     *  ②以'/'开头的字符串
     * @param string $link 链接地址
     * @return bool
     */
    private function isRedirectLink($link){
        $link = trim($link);
        return (0 === strpos($link, 'http')) or (0 === strpos($link,'/')) or (0 === strpos($link, 'https'));
    }


}