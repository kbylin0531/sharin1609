<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/7/16
 * Time: 3:49 PM
 */

namespace App\Publisher\Controller;
use App\Publisher\Library\MemberRegister\EC21MemberRegister;
defined('PATH_COOKIE') or define('PATH_COOKIE',SR_PATH_APP,'/Publisher/Data/Cookie/');
class Index {

    public function index(){

    }

    public function testRigister(){
        $register = new EC21MemberRegister();
        if(!strpos($register->getRegisterPage(),'Set-Cookie: JSESSIONID=')){
            exit('獲取註冊頁面失敗');
        }

        echo $register->getFramePage();

    }

}