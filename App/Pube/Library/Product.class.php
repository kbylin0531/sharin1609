<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/7/16
 * Time: 4:07 PM
 */

namespace Library;
use Library\Utils\HttpRequest;
use Exception;
abstract class Product {
    /**
     * @var string 产品提交页面
     */
    protected $submitAddress   = '';
    /**
     * @var string 产品提交方法
     */
    protected $submitMethod    = 'post';
    /**
     * @var array 产品属性列表
     */
    protected $attrs = [];

    public function __construct(array $attrs=[]){
        $attrs and $this->attrs = array_merge($this->attrs,$attrs);
    }
    public function __get($name){
        return isset($this->attrs[$name]) ? $this->attrs[$name] : '';
    }
    public function __set($name,$val){
        $this->attrs[$name] = $val;
    }
//-------------------------------------- Public method --------------------------------------------------------------------------------
    /**
     * 产品上传
     * @param Member $member 产品总是和会员相关的，通过该参数可以获取登录的信息
     * @return bool 是否提交成功
     * @throws Exception
     */
    public function submit(Member $member){
        if(!$member->login()){
            throw new Exception('用户登录出错！');
        }
        $form = $this->attrs;
        $method = $this->submitMethod;
        $result = HttpRequest::$method($this->submitAddress,$form,$member->getCookie(),true,true);
        return $this->isSucmitSuccess($result);
    }

    /**
     * @param string $response 请求的响应内容
     * @return bool
     */
    abstract public function isSucmitSuccess($response);

    /**
     * 获取上一次提交的产品
     * @param Member $member
     * @return mixed
     */
    abstract public function getLastSubmit(Member $member);

    /**
     * 添加关键词
     * @param string $keywork
     * @return bool
     */
    abstract public function addKeywork($keywork);

    /**
     * @var array 产品图片
     */
    protected $images = [];

    /**
     * 设置产品图片
     * @param string|array $images 图片路径
     * @return $this
     */
    public function setImage($images){
        $this->images = $images;
    }

}