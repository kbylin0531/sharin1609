<?php
/**
 * Github: https://github.com/linzongho/Pindex
 * Email:linzongho@gmail.com
 * User: asus
 * Date: 8/24/16
 * Time: 10:04 PM
 */

namespace Explorer;
use Sharin\Interfaces\Core\URLParseCreaterInterface;

class ExplorerRouter implements URLParseCreaterInterface {

    public $default_controller = 'desktop';	//默认的类名
    public $default_action = 'index';

    private $result = [
        0 => '',//c
        1 => '',//a
    ];

    public function __construct(){
        $this->default_controller = $GLOBALS['config']['setting_system']['first_in'];
        $this->default_action = 'index';
    }

    /**
     * 解析URL或兼域名
     * @return bool
     */
    public function parse(){
        $return = array_merge($_GET,$_POST);
        $remote = array_get($return,0);
        $return['URLremote'] = explode('/',trim($remote[0],'/'));
        $this->result[0] = empty($return['URLremote'][0])?$this->default_controller:$return['URLremote'][0];
        $this->result[1] = empty($return['URLremote'][1])?$this->default_action:$return['URLremote'][1];
        define('ST',$this->result[0]);
        define('ACT',$this->result[1]);
        $GLOBALS['in'] = $return;
        return true;
    }

    /**
     * 创建URL
     * @param string|array $modules 模块序列
     * @param string $contler 控制器名称
     * @param string $action 操作名称
     * @param array|null $params 参数
     * @return string 可以访问的URI
     */
    public function create($modules, $contler, $action, array $params = null){}

    /**
     * 获取解析的模块，多个模块使用'/'分隔
     * @return string
     */
    public function getModules(){
        return '';
    }

    /**
     * 获取控制器
     * @return string
     */
    public function getController(){
        return [
            'user','user',$this->result[0]
        ];
    }

    /**
     * 获取操作名称
     * @return string
     */
    public function getAction(){
        return [
            'loginCheck','authCheck',$this->result[1]
        ];
    }

    /**
     * 获取输入参数
     * @return array
     */
    public function getParameters(){
        return [];
    }


}