<?php
/**
 * Email: linzongho@gmail.com
 * Github: https://github.com/linzongho/Sharin
 * User: asus
 * Date: 8/25/16
 * Time: 8:58 AM
 */

namespace Sharin\Interfaces;

/**
 * Interface BehaviourInterface
 * 行为接口
 * @package Sharin\Interfaces
 */
interface BehaviourInterface {

    /**
     * @param string $tag 行为接触点
     * @param mixed $parameters 外部传入参数
     * @return mixed
     */
    public function run($tag,$parameters);

}