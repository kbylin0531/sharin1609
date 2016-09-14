<?php
/*
* @link http://www.kalcaddle.com/
* @author warlee | e-mail:kalcaddle@qq.com
* @copyright warlee 2014.(Shanghai)Co.,Ltd
* @license http://kalcaddle.com/tools/licenses/license.txt
*/

/**
 * 控制器抽象类
 */
abstract class Controller {
	public $in;
	public $db;
	public $config;	// 全局配置
	public $tpl;	// 模板目录
	public $values;	// 模板变量
	public $L;

	/**
	 * 构造函数
	 */
	function __construct(){
		global $in,$config,$db,$L;

		$this -> db  = $db;
		$this -> L 	 = $L;
		$this -> config = &$config;
		$this -> in = &$in;	
		$this -> values['config'] = &$config;
		$this -> values['in'] = &$in;
        $this -> assign('entry_name',ENTRY_FILE);
	}

    /**
	 * 显示模板
     * @param array|string $key
     * @param mixed|null $value
     * @return void
     */
	protected function assign($key,$value){
		$this->values[$key] = $value;
	}
	/**
	 * 显示模板
     * @param string $tpl_file
     * @return void
     */
	protected function display($tpl_file){
		global $L;
		extract($this->values);
		require($this->tpl.$tpl_file);
//        $context = [
//            'm' => '',
//            'c' => static::class,
//            'a' => SEK::backtrace(SEK::ELEMENT_FUNCTION,SEK::PLACE_FORWARD),
//        ];
//
//        View::assign($this->values);
//        View::display($context);
	}
}
