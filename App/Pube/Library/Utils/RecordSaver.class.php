<?php
namespace Library\Utils;

use Library\Ngine;
const RECORDS_FILE = PUBE_DATA_DIR.'records.php';
Ngine::touch(RECORDS_FILE);
$GLOBALS['records'] = include_once RECORDS_FILE;
if(empty($GLOBALS['records'])){
    $GLOBALS['records'] = [];
}

/**
 * Class RecordSaver 记录保存
 * @package Library\Utils
 */
class RecordSaver {

    public static function set($key,$val){
        $GLOBALS['records'][$key] = [
            'value' => $val,
            'count' => 0,
        ];
    }

    /**
     * 增加计数
     * @param $key
     */
    public static function inc($key){
        $val = &self::get($key,'count');
        if(isset($val)){
            $val ++;
        }
    }

    /**
     * 减少计数
     * @param $key
     */
    public static function dec($key){
        $val = &self::get($key,'count');
        if(isset($val)){
            $val --;
        }
    }


    public static function &get($key='',$type='value'){
        if(!$key){
            return $GLOBALS['records'];
        }elseif(isset($GLOBALS['records'][$key])){
            return $GLOBALS['records'][$key][$type];
        }
        return null;
    }

    public static function rm($key){
        if(key_exists($key,$GLOBALS['records'])) unset($GLOBALS['records'][$key]);
    }

}

register_shutdown_function(function (){
    file_put_contents(RECORDS_FILE,'<?php return '.var_export($GLOBALS['records'],true).';');
});