<?php
/**
 * Email: linzongho@gmail.com
 * Github: https://github.com/linzongho/Sharin
 * User: asus
 * Date: 8/22/16
 * Time: 11:16 AM
 */

namespace Sharin\Core;

use Sharin\Core;

interface ViewInterface {

    /**
     * 让模板引擎知道调用的相关上下文环境
     * @param array $context 上下文环境，包括模块、控制器、方法和模板信息可供设置使用
     * @return $this
     */
    public function setContext(array $context);

    /**
     * 保存控制器分配的变量
     * @param string $tpl_var
     * @param null $value
     * @param bool $nocache
     * @return $this
     */
    public function assign($tpl_var,$value=null,$nocache=false);

    /**
     * 获取所有替换字符串
     * @return array
     */
    public function getParsingString();

    /**
     * 设置模板替换字符串
     * @param string $str
     * @param string $replacement
     * @return void
     */
    public function registerParsingString($str,$replacement);
    /**
     * 显示模板
     * @param string $context 全部模板引擎通用的
     * @param null $cache_id
     * @param null $compile_id
     * @param null $parent
     * @return void
     */
    public function display($context = null, $cache_id = null, $compile_id = null, $parent = null);

}
/**
 * Class View
 * @method void assign(string|array $tpl_var,mixed $value=null,bool $nocache=false) static 保存控制器分配的变量
 * @method void registerParsingString(string $str,string|int $replacement) static 设置模板替换字符串
 * @method void display(array $context,int  $cache_id = null,int $compile_id = null,int $parent = null) static 显示模板
 * @package Sharin\Library
 */
class View extends Core {
    const CONF_NAME = 'view';
    const CONF_CONVENTION = [
        'DRIVER_DEFAULT_INDEX' => 1,
        'DRIVER_CLASS_LIST' => [
            'Sharin\\Core\\View\\Smarty',
            'Sharin\\Core\\View\\Think',
        ],
        'DRIVER_CONFIG_LIST' => [
            [
                'SMARTY_DIR'        => SR_PATH_BASE.'/Vendor/smarty3/libs/',
                'TEMPLATE_CACHE_DIR'    => SR_PATH_RUNTIME.'/View/',
                'SMARTY_CONF'       => [
                    //模板变量分割符号
                    'left_delimiter'    => '{',
                    'right_delimiter'   => '}',
                    //缓存开启和缓存时间
                    'caching'        => true,
                    'cache_lifetime'  => 15,
                ],
            ],
            [
                'CACHE_ON'         => true,//缓存是否开启
                'CACHE_EXPIRE'     => 10,//缓存时间，0便是永久缓存,仅以设置为30
                'CACHE_UPDATE_CHECK'=> true,//是否检查模板文件是否发生了修改，如果发生修改将更新缓存文件（实现：检测模板文件的时间是否大于缓存文件的修改时间）

                'CACHE_PATH'       => SR_PATH_RUNTIME.'/View/',
                'TEMPLATE_SUFFIX'  =>  '.html',     // 默认模板文件后缀
                'CACHFILE_SUFFIX'  =>  '.php',      // 默认模板缓存后缀
                'TAGLIB_BEGIN'     =>  '<',  // 标签库标签开始标记
                'TAGLIB_END'       =>  '>',  // 标签库标签结束标记
                'L_DELIM'          =>  '{',            // 模板引擎普通标签开始标记
                'R_DELIM'          =>  '}',            // 模板引擎普通标签结束标记
                'DENY_PHP'         =>  false, // 默认模板引擎是否禁用PHP原生代码
                'DENY_FUNC_LIST'   =>  'echo,exit',    // 模板引擎禁用函数
                'VAR_IDENTIFY'     =>  'array',     // 模板变量识别。留空自动判断,参数为'obj'则表示对象

                'TMPL_PARSE_STRING'=> [],//用户自定义的字符替换
            ],
        ],

        //模板文件提示错误信息模板
        'TEMPLATE_EMPTY_PATH'   => 'notpl',
    ];

    /**
     * 解析资源文件地址
     * 模板文件资源位置格式：
     *      ModuleA/ModuleB@ControllerName/ActionName:themeName
     * @param array|null $context 模板调用上下文环境，包括模块、控制器、方法和模板主题
     * @return array 类型由参数三决定
     */
    public static function parseTemplatePath($context){
        $path = SR_PATH_BASE.'/'.SR_APP_NAME."/{$context['m']}/View/{$context['c']}/";
        isset($context['t']) and $path = "{$path}{$context['t']}/";
        $path = "{$path}{$context['a']}";
        return $path;
    }
}