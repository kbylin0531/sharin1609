<?php
namespace App\Pube\Controller;
use Library\MemberRegister\EC21MemberRegister;
use Library\Ngine;
use Library\Utils\RecordSaver;
use Sharin\Core\Controller;
use Sharin\Library\Session;

include_once dirname(__DIR__).'/Library/Ngine.class.php';
Ngine::init();

class Index extends Controller{

    public function index(){

        $this->display();

    }

    public function showList(){
        echo '<pre>';
        var_export(RecordSaver::get());
    }

    public function testRigister($code='',$email=''){
        $register = new EC21MemberRegister();
        if($code){
            $code = $register->postCode($code,Session::get('childId'));

            $info = $register->setCapture($code)->setEmail($email)->register();

            echo " <a href='".SR_SCRIPT_URL."/Pube/Index/testRigister'>继续注册</a><pre>";
            var_dump($info);
            RecordSaver::set($info['username'],$info);

            echo $register->update()? 'Y':'N';
        }else{
            $register->getRegisterPage();
            $register->getFramePage();
            $magic = $register->getMagic();
            $childId = $register->getChidId($magic);
            Session::set('childId',$childId);

            $image = $register->saveImage('',$childId);
            $image = SR_PUBLIC_URL.'/'.$image;
            echo "<img src='{$image}' />";
            echo <<< endline
        <form action="{$_SERVER['REQUEST_URI']}" method="get">
            <label>Code:</label>
            <input name="code" type="text">
            <label>Email:</label>
            <input name="email" type="text">
            <input type="submit" value="submit">
        </form>
endline;
        }
    }

}