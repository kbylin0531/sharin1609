<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/25/16
 * Time: 6:16 PM
 */

namespace App\Admin\Controller;
use App\Admin\Model\MemberModel;
use Sharin\Core\Controller;
use Sharin\Core\Logger;
use Sharin\Extension\Loginout;

class Publics extends Controller{

    public function register(){
        $this->display();
    }
    public function login($username='',$passwd='',$remember=false){
        $error = '';
        if(SR_IS_POST){
            if(!$username or !$passwd){
                $error = '用户名或者密码不能为空';
            }else{
                $result = Loginout::login($username,$passwd,MemberModel::getInstance());
                $remember and Loginout::remember(ONE_WEEK);
                if($result){
                    echo 'redirect';
                    $this->redirect('/Admin/Index/index');
                }else{
                    Logger::record([$result,$username,$passwd,'login failed']);
                }
                $error = $result;
            }
        }
        Loginout::check() and $this->redirect('/Admin/Index/index');//已经登录的状态
        $this->assign('error',$error);
        $this->display();
    }
    public function lockScreen(){
        $this->display();
    }

    public function show404(){
        $this->display('404');
    }

    public function show500(){
        $this->display('500');
    }

    /**
     * 注销登录
     */
    public function logout(){
        Loginout::logout(new MemberModel()) and $this->redirect('/Admin/Publics/login');
    }

}