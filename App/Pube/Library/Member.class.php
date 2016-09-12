<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/7/16
 * Time: 3:51 PM
 */

namespace Library;

abstract class Member extends Ngine{

    /**
     * @var string 登录表单提交页面
     */
    protected $login_addresss = '';
    /**
     * @var string 提交方法
     */
    protected $login_method = 'post';
    /**
     * @var string 註冊地址
     */
    protected $register_address = '';
    /**
     * @var string 註冊提交方法
     */
    protected $register_emthod = 'post';

    /**
     * @var array 登錄隐藏表单
     */
    protected $login_hiddens = [];
    /**
     * @var array 註冊隐藏表单
     */
    protected $register_hiddens = [];

    //显式表单
    protected $usernameField  = 'username';
    protected $passwordField  = 'password';
    protected $verifycodeField = '';//验证码
    //用户相关
    /**
     * @var string 登录用户名
     */
    protected $username = '';
    /**
     * @var string 登录密码
     */
    protected $password = '';
    /**
     * @var string 验证码
     */
    protected $verifycode = '';

    protected $login_cookie = '';

    /**
     * Member constructor.
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $verifycode 验证码，不需要验证码的时候默认为空
     */
    public function __construct($username,$password='',$verifycode=''){
        $this->username     = $username;
        $password or $password = $username;
        $this->password     = $password;
        $this->verifycode   = $verifycode;
        self::touch($this->login_cookie = PUBE_COOKIE_DIR.'Login/Ec21.'.$this->username);
    }

    /**
     * 检查用户是否登录
     * 不检测cookie是否过期
     * @return bool
     */
    public function login(){
        $fields = $this->buildLoginFields();
        $method = $this->login_method;
        $address = $this->login_addresss;
        $response = self::$method($address,$fields,'',$this->login_cookie,true);
        $this->saveExpareTimestamp($response);
        return $this->isLoginSuccess($response);
    }

    /**
     * 获取登录cookie文件路径
     * @return false|string
     * @throws \Exception cookie目录不存在或者不可写时抛出异常
     */
    public function getCookie(){
        return $this->login_cookie;
    }

    /**
     * @param bool $build
     * @return string|array
     */
    protected function buildLoginFields($build=true){
        $form = array(
            $this->usernameField    => $this->username,
            $this->passwordField    => $this->password,
        );
        if($this->verifycodeField){/* 需要验证码的时候填写 */
            $this->verifycode and $form[$this->verifycodeField] = $this->verifycode;
        }
        $form = array_merge($this->login_hiddens,$form);
        return $build?http_build_query($form):$form;
    }

    /**
     * 平台+用户名作为cookie的标识符
     * @return string
     */
    protected function getIdentify(){
        return md5(static::class.'__'.$this->username);
    }

    /**
     * 判断登录是否成功
     * @param string $response 登录返回信息
     * @return bool
     */
    abstract protected function isLoginSuccess($response);

    /**
     * 获取过期的时间戳
     * @param string $response 登录返回信息
     * @return bool
     */
    abstract protected function saveExpareTimestamp($response);

    /**
     * @var string 默认分类ID
     */
    protected $default_cateid = '';

    /**
     * @return string
     */
    public function getDefaultCateid()
    {
        return $this->default_cateid;
    }

    /**
     * @param string $default_cateid
     */
    public function setDefaultCateid($default_cateid)
    {
        $this->default_cateid = $default_cateid;
    }


}