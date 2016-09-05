<?php
/**
 * Github: https://github.com/linzongho/Sharin
 * Email:linzongho@gmail.com
 * User: asus
 * Date: 8/24/16
 * Time: 8:05 PM
 */
namespace Sharin\Interfaces\Core;

interface DispatchInstanceGeneraterInterface{

    /**
     * 设置调度需要的模块、控制器、操作信息
     * @param string|array $module
     * @param string|array $controller
     * @param string|array $action
     * @return void
     */
    public function setParameters($module,$controller,$action);

    /**
     * @return object
     */
    public function nextController();

    /**
     * @return \ReflectionMethod
     */
    public function nextAction();

    /**
     * @return bool
     */
    public function hasNext();

}