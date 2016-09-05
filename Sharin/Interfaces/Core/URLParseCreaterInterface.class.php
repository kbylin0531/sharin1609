<?php
/**
 * Email: linzongho@gmail.com
 * Github: https://github.com/linzongho/Sharin
 * User: asus
 * Date: 8/25/16
 * Time: 8:53 AM
 */

namespace Sharin\Interfaces\Core;

interface URLParseCreaterInterface {
    /**
     * 解析URL或兼域名
     * @return bool
     */
    public function parse();

    /**
     * 获取解析的模块，多个模块使用'/'分隔
     * @return string
     */
    public function getModules();

    /**
     * 获取控制器
     * @return string
     */
    public function getController();

    /**
     * 获取操作名称
     * @return string
     */
    public function getAction();

    /**
     * 获取输入参数
     * @return array
     */
    public function getParameters();

    /**
     * 创建URL
     * @param string|array $modules 模块序列
     * @param string $contler 控制器名称
     * @param string $action 操作名称
     * @param array|null $params 参数
     * @return string 可以访问的URI
     */
    public function create($modules,$contler,$action,array $params=null);

}