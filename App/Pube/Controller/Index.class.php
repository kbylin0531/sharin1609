<?php
namespace App\Pube\Controller;
use Library\MemberRegister\EC21MemberRegister;
use Library\Ngine;
use Library\Utils\RecordSaver;
use Sharin\Core\Controller;
use Sharin\Core\Response;
use Sharin\Library\Session;

include_once dirname(__DIR__).'/Library/Ngine.class.php';
Ngine::init();

class Index extends Controller{

    public function index($code='',$email=''){
        if($code){
            $register = new EC21MemberRegister();
            $code = $register->postCode($code,Session::get('childId'));
            $info = $register->setCapture($code)->setEmail($email)->register();
            if($register->update()){
                RecordSaver::set($info['username'],$info);
                Response::ajaxBack([
                    'type'  => 1,
                    'value'=> $info,
                ]);
            }else{
                Response::ajaxBack([
                    'type'  => 0,
                    'value'=> '修改失败',
                ]);
            }
        }
        $this->assign('data',json_encode(array_values(RecordSaver::get())));
        $this->display();
    }

    public function showList(){
        echo '<pre>';
        var_export(RecordSaver::get());
    }

    public function getImage(){
        $register = new EC21MemberRegister();
        $register->getRegisterPage();
        $magic = $register->getMagic();
        $childId = $register->getChidId($magic);
        Session::set('childId',$childId);

        $image = $register->saveImage('',$childId);
        Response::ajaxBack([
            'src' => SR_PUBLIC_URL.'/'.$image,
        ]);
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
            $magic = $register->getMagic();
            $childId = $register->getChidId($magic);
            Session::set('childId',$childId);

            $image = $register->saveImage('',$childId);
            $image = SR_PUBLIC_URL.'/'.$image;
            echo <<< endline
<form action="{$_SERVER['REQUEST_URI']}" method="get">
    <label>www.ec21.com</label><br>
    <img src='{$image}' />
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