<?php
namespace Library;

/**
 * Class MemberRigister 會員註冊
 * @package Library
 */
abstract class MemberRigister extends Ngine{
    /**
     * @var string 注册邮箱
     */
    protected $email = '';
    /**
     * @var string 注册验证码
     */
    protected $capture = '';

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email) {
        if(false === strpos($email,'@')){
            $email .= '@qq.com';
        }
        $this->email = $email;
        return $this;
    }

    /**
     * @param string $capture
     * @return $this
     */
    public function setCapture($capture) {
        $this->capture = $capture;
        return $this;
    }

    /**
     * 注册公司信息
     * @return array
     */
    abstract public function register();

    /**
     * 更新公司信息
     * @return mixed
     */
    abstract public function update();



}