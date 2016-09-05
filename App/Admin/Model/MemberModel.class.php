<?php

/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/25/16
 * Time: 5:49 PM
 */
namespace App\Admin\Model;
use Sharin\Core\Logger;
use Sharin\Core\Model;
use Sharin\Extension\Interfaces\LoginoutInterface;
use Sharin\Library\Helper\ClientAgent;

class MemberModel extends Model implements LoginoutInterface{

    protected $tablename = 'lx_member';

    /**
     * 获取登录错误信息
     * @return string|null
     */
    public function getLoginError(){
    }

    protected $fields = [
        'username'  => null,
        'sex'       => null,
        'nickname'  => null,
        'email'     => null,
        'reg_time'  => null,
        'last_login_ip'     => null,
        'last_login_time'   => null,
        'status'    => null,
        'passwd'    => null,//初始密码
        'profile'   => null,
    ];

    const LOGIN_USERNAME = 0;
    const LOGIN_EMAIL = 1;

    /**
     * @param string $username
     * @param null $password
     * @param int $type
     * @return false|array
     */
    public function login($username,$password,$type=self::LOGIN_USERNAME){
        $where = ['status'=>1];//only status =1
        switch ($type){
            case self::LOGIN_EMAIL:
                $where['email'] = $username;
                break;
            case self::LOGIN_USERNAME:
            default:
                $where['username'] = $username;
        }
        $userinfo = $this->fields('profile,email,id,nickname,last_login_ip,last_login_time,sex,username,passwd')->where($where)->find();

        if(false === $userinfo){
            $error = $this->error();
            Logger::write($error,$userinfo);
            if(!SR_DEBUG_MODE_ON){
                $this->error = '服务端发生了错误！';
            }else{
                $this->error = "登录失败：{$error}!";
            }
        }elseif(!$userinfo){//空数组
            $this->error = '用户不存在';
        }else{
            if($password === $userinfo['passwd']){
                //update
                $this->fields([
                    'last_login_ip'     => ClientAgent::getClientIP(),
                    'last_login_time'   => SR_NOW,
                ])->where($where)->update();

                unset($userinfo['passwd']);
                return $userinfo;
            }else{
                $this->error = '密码不正确！';
            }
        }
        return false;
    }

    /**
     * 注销登陆
     * @return bool
     */
    public function logout(){
        return true;
    }

    /**
     * 获取用户列表
     * @param int $status
     * @return array|bool
     */
    public function lists($status =  1){
        return $this->where('status = '.intval($status))->select();
    }

    /**
     * 根据用户名获取用户信息
     * @param $username
     * @return bool|mixed
     */
    public function findByName($username){
        $result = $this->where(['username'=>$username])->find();
        return $result;
    }
    /**
     * 添加用户
     * @param array $info
     * @return bool
     */
    public function add(array $info){
        $info = $this->data($info);
        if(empty($info['nickname'])) $info['nickname'] = '匿名用户_'.str_replace('.','',''.microtime(true));
        return $this->fields($info)->create();
    }

    /**
     * 删除用户
     * @param int $uid
     * @return bool
     */
    public function remove($uid){
        return $this->fields(['status'=>0])->where('id = '.intval($uid))->update();
    }

    /**
     * 修改用户信息
     * @param array $info
     * @return bool
     */
    public function revise(array $info){
        if(!isset($info['id'])){
            $this->error = '缺少用户ID信息，无法完成更新';
            return false;
        }
        $id = $info['id'];
        unset($info['id']);
        $info = $this->data($info);
        return $this->fields($info)->where('id = '.intval($id))->update();
    }


}