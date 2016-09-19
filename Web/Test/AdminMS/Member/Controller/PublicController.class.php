<?php
/**
 * Created by PhpStorm.
 * User: Lin
 * Date: 2015/10/16 0016
 * Time: 19:46
 */
namespace Application\Admin\Member\Controller;
use Application\Admin\Member\Model\MemberModel;
use Application\Admin\Member\Util\MemberKits;
use System\Core\Controller;
use System\Extension\Verify;
use System\Util\SEK;

/**
 * Class PublicController
 * @package Application\Admin\Member\Controller
 */
class PublicController extends Controller {

    private static $message_box = array(
        'empty_username'   => '请输入用户名',
        'empty_password'   => '请输入密码',
        'empty_verify'     => '请输入验证码',
        'error_account'    => '用户名密码错误',
        'error_verify'     => '错误的验证码'

    );

    private static $login_success_jump = 'admin/main/index/index';

    private $login_message = '';

    public function __construct(){
        parent::__construct();
    }

    /**
     * 登陆与现实
     * @param string $username
     * @param string $password
     * @param string $verify
     */
    public function login($username='',$password='',$verify=''){
        if(IS_POST){
            $this->login_message = empty($username)?'':self::$message_box['empty_username'];
            $this->login_message = empty($password)?'':self::$message_box['empty_password'];
            $this->login_message = empty($verify)?'':self::$message_box['empty_verify'];

            if(!$this->checkVerify($verify)){
                $this->login_message = self::$message_box['error_verify'];
            }elseif(!$this->checkPassword($username,$password)){
                $this->login_message = self::$message_box['error_account'];
            }else{
                $this->redirect(self::$login_success_jump);
            }
        }
        if(MemberKits::getUserId()){
            $this->redirect(self::$login_success_jump);
        }
        $this->assign('login_message',$this->login_message);
        $this->display('login');
    }

    /**
     * 登出
     */
    public function logout(){
        echo 'Here is "'.__METHOD__.'" ,you gonna to log out the system,that is right!';
    }

    /**
     * 忘记密码
     * @param $email
     */
    public function forget($email){
        SEK::dumpout($email);
    }

    /**
     * 登陆界面注册用户
     * @param $username
     * @param $password
     * @param $email
     * @param $mobile
     *
     * @param int $sex
     * @param string $qq
     * @param string $nickname
     */
    public function register($username,$password,$email='',$mobile='',$sex=1,$qq='',$nickname=''){
        $fields = array(
            'username'  => $username,
            'nickname'  => $nickname,
            'password'  => $password,
            'sex'       => $sex,
            'email'     => $email,
            'mobile'    => $mobile,
            'qq'        => $qq,
        );
        $memberModel = new MemberModel();
        $rst = $memberModel->register($fields);
        if(false === $rst){
            $this->ajaxFaiure('注册失败'.$memberModel->getErrorInfo());
        }else{
            $this->ajaxSuccess('注册成功');
        }
    }

    /**
     * 生成验证码
     * @param $id
     */
    public function verify($id=APP_NAME){
        Verify::entry($id);
    }

    /**
     * 校验验证码
     * @param $verify
     * @param $id
     * @return bool
     */
    private function checkVerify($verify,$id=APP_NAME){
        return Verify::check($verify,$id);
    }

    private function checkPassword($username,$password){
        return true;
    }

}