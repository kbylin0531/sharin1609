<?php
/**
 * Email: linzongho@gmail.com
 * Github: https://github.com/linzongho/Sharin
 * User: asus
 * Date: 8/25/16
 * Time: 10:01 AM
 */

namespace Sharin\Core\Dispatcher;
use Sharin\Core\Dispatcher;
use Sharin\Developer;
use Sharin\Exceptions\Dispatch\ControllerNotFoundException;
use Sharin\Exceptions\Dispatch\MethodNotExistException;
use Sharin\Exceptions\Dispatch\ModuleNotFoundException;
use Sharin\Exceptions\Dispatch\ActionAccessDenyException;
use Sharin\Interfaces\Core\DispatcherInterface;

class LiteDispatcher implements DispatcherInterface{

    private $config = [
        //空缺时默认补上,Done!
        'INDEX_MODULE'      => 'Home',
        'INDEX_CONTROLLER'  => 'Index',
        'INDEX_ACTION'      => 'index',
    ];

    public function __construct(array $config=null){
        $config and $this->config = array_merge($this->config,$config);
    }

    /**
     * @var string 待调度的模块
     */
    private $module = '';
    /**
     * @var string  待调度的控制器
     */
    private $controller = '';
    /**
     * @var string 待调度的操作
     */
    private $action = '';

    /**
     * 获取调度的模块
     * @return string
     */
    public function getModule(){
        return $this->module;
    }

    /**
     * 获取调度的控制器
     * @return string
     */
    public function getController(){
        return $this->controller;
    }

    /**
     * 获取调度的操作
     * @return string
     */
    public function getAction(){
        return $this->action;
    }

    /**
     * 检查并设置默认设置
     * @param $modules
     * @param $ctrler
     * @param $action
     * @return $this
     */
    public function check($modules,$ctrler,$action){
        $this->module      = $modules?$modules:$this->config['INDEX_MODULE'];
        $this->controller  = $ctrler?$ctrler:$this->config['INDEX_CONTROLLER'];
        $this->action      = $action?$action:$this->config['INDEX_ACTION'];

        $this->module and is_array($this->module) and $this->module = implode('/',$this->module);
        return $this;
    }

    /**
     * @param array|string $modules
     * @param array|string $ctrler
     * @param array|string $action
     * @param array $params
     * @return mixed|null
     * @throws ActionAccessDenyException
     * @throws ControllerNotFoundException
     * @throws MethodNotExistException
     * @throws ModuleNotFoundException
     */
    public function dispatch($modules,$ctrler,$action,array $params=[]){
        $modulepath = SR_PATH_APP."/{$modules}";//linux 不识别
        strpos($modules,'/') and $modules = str_replace('/','\\',$modules);
        //模块检测
        if(!is_dir($modulepath)){
            throw new ModuleNotFoundException($modules);
        }

        //控制器名称及存实性检测
        $className = SR_APP_NAME."\\{$modules}\\Controller\\{$ctrler}";
        if(!class_exists($className,true)){
            throw new ControllerNotFoundException($className);
        }
        $classInstance =  new $className();
        //方法检测
        if(!method_exists($classInstance,$action)){
            throw new MethodNotExistException($modules,$className,$action);
        }
        $method = new \ReflectionMethod($classInstance, $action);

        return Dispatcher::execute($classInstance,$method);
    }

}