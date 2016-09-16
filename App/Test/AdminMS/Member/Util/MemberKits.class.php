<?php
/**
 * Created by PhpStorm.
 * User: Lin
 * Date: 2015/10/11 0011
 * Time: 15:24
 */
namespace Application\Admin\Member\Util;
use System\Util\SEK;
use System\Util\SessionUtil;

/**
 * Class UserKits 用户工具集
 * @package Application\Cms\Util
 */
class MemberKits {
    /**
     * 获取用户ID
     * @return integer 等于0-未设置userid，需要重新登录，
     *                 大于0-当前登录用户ID
     * @return int
     */
    public static function getUserId(){
        $info = self::getUserInfo();
        if(isset($info)){
            return $info['userid'];
        }else{
            return 0;
        }
    }

    /**
     * 获取用户信息
     * @return null|array
     */
    public static function getUserInfo(){
        $info = SessionUtil::get('x_user_info');
        if(empty($info)){
            return null;
        }else{
            return SessionUtil::get('x_user_sign') === SEK::dataAuthSign($info)?$info : null;
        }
    }



    /**
     * 检查登陆状态
     * @return bool
     */
    public static function checkLoginStatus(){
        //设置了UID，说明是其他地方new了一个控制器
        if(defined('UID')) return true;
        $uid = self::getUserId();
        if(0 === $uid){//用户需要重新登录
            return false;
        }else{
            define('UID',$uid);
            return true;
        }
    }

}