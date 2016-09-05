<?php
/**
 * Email: linzongho@gmail.com
 * Github: https://github.com/linzongho/Sharin
 * User: asus
 * Date: 8/22/16
 * Time: 11:01 AM
 */
namespace Sharin\Core;
use Sharin\Core;
use Sharin\Exceptions\Dispatch\ActionAccessDenyException;
use Sharin\Exceptions\Dispatch\ActionParameterMissingException;
use Sharin\SharinException;
use ReflectionMethod;

/**
 * Class Dispatcher
 * @method string getModule()
 * @method string getController()
 * @method string getAction()
 * @method $this check(string $modules,string $ctrler,string $action) 检查并设置默认设置
 * @method mixed dispatch(string|array $modules,string $ctrler,string $action,array $params=[]) 调度到对应的action上去
 * @package Sharin\Core
 */
class Dispatcher extends Core {

    const CONF_NAME = 'dispatcher';
    const CONF_CONVENTION = [
        'DRIVER_DEFAULT_INDEX' => 0,//默认驱动ID，类型限定为int或者string
        //驱动类的列表
        'DRIVER_CLASS_LIST' => [
            'Sharin\\Core\\Dispatcher\\LiteDispatcher',
        ],
        'DRIVER_CONFIG_LIST'  => [
            [
                //空缺时默认补上,Done!
                'INDEX_MODULE'      => 'Home',
                'INDEX_CONTROLLER'  => 'Index',
                'INDEX_ACTION'      => 'index',
            ],
        ],
        //参数默认来自$_REQUESR
        'PARAM_SOURCE'  => self::PARAM_REQUEST,
    ];

    const PARAM_REQUEST = 0;
    const PARAM_GET     = 1;
    const PARAM_POST    = 2;
    const PARAM_PUT     = 3;


    /**
     * @var array
     */
    private static $_config = [];

    public static function __init(){
        self::$_config = self::getConfig();
    }

    /**
     * 执行控制器实例的对应方法
     * @param object $controllerInstance 控制器实例
     * @param ReflectionMethod $method 方法反射对象
     * @return mixed|null
     * @throws ActionAccessDenyException 方法非公开或者为静态方法时抛出异常
     */
    public static function execute($controllerInstance,\ReflectionMethod $method){
        if (!$method->isPublic() or $method->isStatic()) {
            throw new ActionAccessDenyException($method);
        }

        //方法的参数检测
        if ($method->getNumberOfParameters()) {//有参数
            $args = Dispatcher::fetchArguments($method);
            //执行方法
            $result = $method->invokeArgs($controllerInstance, $args);
        } else {//无参数的方法调用
            $result = $method->invoke($controllerInstance);
        }
        return $result;
    }

    /**
     * 获取传递给盖饭昂奋的参数
     * @param \ReflectionMethod $targetMethod
     * @return array
     * @throws SharinException
     */
    public static function fetchArguments(\ReflectionMethod $targetMethod){
        $args = [];
        if($methodParams = $targetMethod->getParameters()){
            //获取默认的操作参数来源
            switch(self::$_config['PARAM_SOURCE']){
                case self::PARAM_REQUEST:
                    $vars = $_REQUEST;
                    break;
                case self::PARAM_GET:
                    $vars = $_GET;
                    break;
                case self::PARAM_POST:
                    $vars = $_POST;
                    break;
                case self::PARAM_PUT:
                    parse_str(file_get_contents('php://input'), $vars);
                    break;
                default:
                    $vars = array_merge($_GET,$_POST);//POST覆盖GET
                    break;
            }
            //参数组织
            foreach ($methodParams as $param) {
                $paramName = $param->getName();
                if(isset($vars[$paramName])){
                    $args[] =   $vars[$paramName];
                }elseif($param->isDefaultValueAvailable()){
                    $args[] =   $param->getDefaultValue();
                }else{
                    throw new ActionParameterMissingException($param);
                }
            }
        }
        return $args;
    }

}