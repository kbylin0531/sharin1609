<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/7/16
 * Time: 3:49 PM
 */

namespace App\Pube\Controller;
use App\Pube\Library\MemberRegister\EC21MemberRegister;
use Sharin\Library\Session;

defined('PATH_COOKIE') or define('PATH_COOKIE',SR_PATH_APP,'/Pube/Data/Cookie/');
class Index {

    public function index(){

    }

    public function testRigister($code=''){
        $register = new EC21MemberRegister();
        $urlbase = dirname($_SERVER['SCRIPT_NAME']);
        if($code){
            $code = $register->postCode($code,Session::get('childId'));

            $content = $register->register($code);
            if(false === $content){
                echo "failed to register!  <a href='{$urlbase}/index.php/Pube/Index/testRigister'>$code</a>";
            }else{
                echo $content;
            }
        }else{
            $register->getRegisterPage();
            $register->getFramePage();
            $magic = $register->getMagic();
            $childId = $register->getChidId($magic);
            Session::set('childId',$childId);

            $image = $register->saveImage('',$childId);
            $image = $urlbase.'/'.$image;
            echo "<img src='{$image}' />";
            echo <<< endline
        <form action="{$_SERVER['REQUEST_URI']}" method="get">
            <input name="code" type="text">
            <input type="submit" value="submit">
        </form>
endline;
        }
    }

}