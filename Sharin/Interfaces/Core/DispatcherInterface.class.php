<?php
/**
 * Email: linzongho@gmail.com
 * Github: https://github.com/linzongho/Sharin
 * User: asus
 * Date: 8/25/16
 * Time: 10:03 AM
 */

namespace Sharin\Interfaces\Core;
use Sharin\Exceptions\Dispatch\ControllerNotFoundException;
use Sharin\Exceptions\Dispatch\MethodNotExistException;
use Sharin\Exceptions\Dispatch\ModuleNotFoundException;
use Sharin\Exceptions\Dispatch\ActionAccessDenyException;


interface DispatcherInterface {

    /**
     * 获取调度的模块
     * @return string
     */
    public function getModule();

    /**
     * 获取调度的控制器
     * @return string
     */
    public function getController();

    /**
     * 获取调度的操作
     * @return string
     */
    public function getAction();

    /**
     * 检查并设置默认设置
     * @param $modules
     * @param $ctrler
     * @param $action
     * @return $this
     */
    public function check($modules,$ctrler,$action);

    /**
     * 调度到对应的action上去
     * @param string|array $modules
     * @param string|array $ctrler
     * @param string|array $action
     * @param array $params
     * @return mixed
     * @throws ActionAccessDenyException
     * @throws ControllerNotFoundException
     * @throws MethodNotExistException
     * @throws ModuleNotFoundException
     */
    public function dispatch($modules,$ctrler,$action,array $params=[]);

}