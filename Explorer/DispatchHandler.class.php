<?php
namespace Explorer;
use Sharin\Core\Dispatcher;
use Sharin\Exceptions\Dispatch\ActionAccessDenyException;
use Sharin\Exceptions\Dispatch\ControllerNotFoundException;
use Sharin\Exceptions\Dispatch\MethodNotExistException;
use Sharin\Exceptions\Dispatch\ModuleNotFoundException;
use Sharin\Interfaces\Core\DispatcherInterface;

class DispatchHandler implements DispatcherInterface {
    /**
     * @var object[]
     */
    private $controllers = [];
    /**
     * @var \ReflectionMethod[]
     */
    private $methods = [];

    public function getModule() {
        return '';
    }

    public function getController() {
        return $this->controller;
    }

    public function getAction() {
        return $this->action;
    }

    public function check($modules, $ctrler, $action){
        $this->controller = $ctrler;
        $this->action = $action;
        return $this;
    }
    /**
     * 获取空
     * @param $controller
     * @return object
     */
    private function getControllerInstance($controller){
        if(!isset($this->controllers[$controller])){
            include_once SR_PATH_BASE.'/Explorer/Controller/'.$controller.'.class.php';
            $this->controllers[$controller] = new $controller();
        }
        return $this->controllers[$controller];
    }

    /**
     * @param string $controller
     * @param string $action
     * @return array
     * @throws MethodNotExistException
     */
    private function getMethodInstance($controller,$action){
        $key = $controller.'+'.$action;
        if(!isset($this->methods[$key])){
            $controllerInstance = $this->getControllerInstance($controller);
            //方法检测
            if(!method_exists($controllerInstance,$action)) throw new MethodNotExistException($controllerInstance,$action);
            $this->methods[$key] = new \ReflectionMethod($controllerInstance, $action);
        }
        return $this->methods[$key];
    }


    private $controller = null;
    private $action = null;

    /**
     * 调度到对应的action上去,
     * @param string|array $modules
     * @param string|array $ctrlers
     * @param string|array $actions
     * @param array $params
     * @return mixed
     * @throws ActionAccessDenyException
     * @throws ControllerNotFoundException
     * @throws MethodNotExistException
     * @throws ModuleNotFoundException
     */
    public function dispatch($modules, $ctrlers, $actions, array $params = []){
        if(!is_array($ctrlers)) $ctrlers = [$ctrlers];
        if(!is_array($actions)) $actions = [$actions];
        $maxlen = count($ctrlers);
        $maxlen2 = count($actions);

        $maxlen2 > $maxlen and $maxlen = $maxlen2;

        $result = null;
        for($i = 0; $i < $maxlen; $i++){
            $controller = isset($ctrlers[$i])?$ctrlers[$i]:$ctrlers[0];
            $action = isset($actions[$i])?$actions[$i]:$actions[0];

            $controllerInstance = $this->getControllerInstance($controller);
            $methodInstance = $this->getMethodInstance($controller,$action);
            $result = Dispatcher::execute($controllerInstance,$methodInstance);
        }
        return $result;//只有最后一个结果才能被返回
    }

}