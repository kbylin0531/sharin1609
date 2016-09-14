<?php

namespace Library;

/**
 * Interface PlatformInterface 平台实现接口
 *
 * 组合操作示例：
 * 一 发布产品示例：
 * <code>
 *  $platform = PlatformManager::instance('ec21);
 *  if($platform->login('username','userpwd')){
 *      if($platform->submit([
 *          'name'  => 'XXXXXX',
 *          'description'   => 'YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY'，
 *          'image' => '/home/......./a.jpg',
 *          ......
 *      ])){
 *          exit('发布成功：');
 *      }else{
 *          exit('发布失败：'.$platform->getError());
 *      };
 * }else{
 *      exit('无法登录：'.$platform->getError());
 * }
 * <code>
 * 二：........
 *
 *
 * @package Library
 */
interface PlatformInterface {

    /**
     * 发布产品
     * @param array $info 产品属性列表
     * @return bool
     */
    public function publish(array $info);

    /**
     * 用户登录
     * 如果上次登录的cookie未过期，可以直接采用上次的cookie并返回true以省去登录花去的时间
     * @param string $username
     * @param string $passwd 密码留空时默认使用用户名作为密码
     * @param string $capture 登录验证码，未空时不提交该属性
     * @return bool
     */
    public function login($username,$passwd='',$capture='');

    /**
     * 用户注册
     * @param array $info 用户属性列表
     * @return bool
     */
    public function register(array $info);

    /**
     * 完善公司信息
     * @param array $info 公司信息列表
     * @return bool
     */
    public function complete(array $info);

    /**
     * 获取错误信息
     * @return string 返回空字符串''表示没有错误
     */
    public function getError();

}