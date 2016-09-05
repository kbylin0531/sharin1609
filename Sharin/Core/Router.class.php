<?php
/**
 * Email: linzongho@gmail.com
 * Github: https://github.com/linzongho/Sharin
 * User: asus
 * Date: 8/22/16
 * Time: 10:55 AM
 */
namespace Sharin\Core;
use Sharin\Core;
use Sharin\Interfaces\Core\URLParseCreaterInterface;

/**
 * Class Router
 *
 * @method bool parse() 解析URL或兼域名
 * @method string getModules() 获取解析的模块，多个模块使用'/'分隔
 * @method string getController() 获取控制器
 * @method string getAction() 获取操作名称
 * @method array getParameters() 获取输入参数
 * @method string create(string|array $modules,string $contler,string $action,array $params=[]) 创建URL
 *
 * @package Sharin\Core
 */
class Router extends Core {

    const CONF_NAME = 'route';
    const CONF_CONVENTION = [
        'DRIVER_DEFAULT_INDEX' => 0,//默认驱动ID，类型限定为int或者string
        'DRIVER_CLASS_LIST' => [
            'Sharin\\Core\\Router\\LiteRouter',
        ],//驱动类的列表
        'DRIVER_CONFIG_LIST' => [
            [
                //@see LiteRouter's config
            ]
        ],//驱动类列表参数
    ];
    /**
     * @var URLParseCreaterInterface
     */
    protected $_driver = null;

    /**
     * $url规则如：
     *  .../Ma/Mb/Cc/Ad
     * 依次从后往前解析出操作，控制器，模块(如果存在模块将被认定为完整的模块路径)
     * @param string $url 快速创建的URL字符串
     * @param array $params GET参数数组
     * @return string
     */
    public static function url($url=null,array $params=[]){
        //解析参数中的$url
        $params or $params = [];
        $hashpos = strpos($url,'#');
        if($hashpos){
            $hash = substr($url,$hashpos+1);
            $url = substr($url,0,$hashpos);
        }else{
            $hash = '';
        }
        $parts = @explode('/',trim($url,'/'));

        //调用URLHelper创建URL
        $action  = array_pop($parts);
        $ctler   = $action?array_pop($parts):null;
        $modules = $ctler?$parts:null;
        $url = self::instance()->create($modules,$ctler,$action,$params);
        if($hash) $url .= '#'.$hash;
        return $url;
    }

}