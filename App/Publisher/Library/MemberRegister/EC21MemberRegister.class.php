<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/7/16
 * Time: 4:15 PM
 */

namespace App\Publisher\Library\MemberRegister;
use App\Publisher\Library\MemberRigister;

class EC21MemberRegister extends MemberRigister {
    /**
     * @var string 打開註冊頁面 時候的cookie
     */
    private $register_page_cookie = '';
    /**
     * @var string
     */
    private $register_framepage_cookie = '';

    public function __construct(){
        $this->register_page_cookie = PATH_COOKIE.'/Publisher/Data/register/ec21.cookie';
        $this->register_framepage_cookie = PATH_COOKIE.'/Publisher/Data/register/ec21.frame.cookie';

    }

    public function getRegisterPage(){
        $url = 'http://www.ec21.com/global/member/MyRegist.jsp';
        $content = self::get($url,'',$this->register_page_cookie,true);
        return $content;
    }

    public function getFramePage(){
        $url = 'http://www.ec21.com/global/captcha/captcha.jsp?type=MyRegist';
        $content = self::get($url,'',$this->register_framepage_cookie,true);
        return $content;
    }


}