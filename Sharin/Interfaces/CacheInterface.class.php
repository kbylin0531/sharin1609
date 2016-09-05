<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 16-9-4
 * Time: 上午11:50
 */

namespace Sharin\Interfaces;

/**
 * Interface CacheorInterface
 * Cache Driver Interface
 * @package Sharin\Interfaces
 */
interface CacheInterface {

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $replacement
     * @return mixed
     */
    public function get($name,$replacement=null);

    /**
     * @access public
     * @param string $name 缓存名称
     * @return bool
     */
    public function has($name);

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param int $expire  有效时间，0为永久（以秒计时）
     * @return boolean
     */
    public function set($name, $value, $expire = null);

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function delete($name);

    /**
     * 清除缓存
     * @access public
     * @return boolean
     */
    public function clean();


}