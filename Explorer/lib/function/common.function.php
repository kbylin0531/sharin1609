<?php
/*
* @link http://www.kalcaddle.com/
* @author warlee | e-mail:kalcaddle@qq.com
* @copyright warlee 2014.(Shanghai)Co.,Ltd
* @license http://kalcaddle.com/tools/licenses/license.txt
*/
/**
 * 生产model对象
 * @param $model_name
 * @return bool|object
 */
function init_model($model_name){
	if (!class_exists($model_name.'Model')) {
		$model_file = MODEL_DIR.$model_name.'Model.class.php';
		require_once ($model_file);
		
		if(!is_file($model_file)){
			return false;
		}
	}
	$reflectionObj = new ReflectionClass($model_name.'Model');
	$args = func_get_args();
	array_shift($args);
	return $reflectionObj -> newInstanceArgs($args);
}
/**
 * 生产controller对象
 * @param $controller_name
 * @return bool|object
 */
function init_controller($controller_name){
	if (!class_exists($controller_name)) {
		$model_file = CONTROLLER_DIR.$controller_name.'.class.php';
		if(!is_file($model_file)){
			return false;
		}
		require_once ($model_file);
	}
	$reflectionObj = new ReflectionClass($controller_name);
	$args = func_get_args();
	array_shift($args);
	return $reflectionObj -> newInstanceArgs($args);
}

/**
 * 加载类
 * @param $class
 */
function load_class($class){
	$filename = CLASS_DIR.$class.'.class.php';
	if (file_exists($filename)) {
		require($filename);
	}else{
		pr($filename.' is not exist',1);
	}
}
/**
 * 加载函数库
 * @param $function
 */
function load_function($function){
	$filename = FUNCTION_DIR.$function.'.function.php';
	if (file_exists($filename)) {
		require($filename);
	}else{
		pr($filename.' is not exist',1);
	}
}
/**
 * 文本字符串转换
 * @param $str
 * @return mixed
 */
function mystr($str){
	$from = array("\r\n", " ");
	$to = array("<br/>", "&nbsp");
	return str_replace($from, $to, $str);
} 

// 清除多余空格和回车字符
function strip($str){
	return preg_replace('!\s+!', '', $str);
} 

/**
 * 获取精确时间
 */
function mtime(){
	$t= explode(' ',microtime());
	$time = $t[0]+$t[1];
	return $time;
}
/**
 * 过滤HTML
 * @param $HTML
 * @param bool $br
 * @return mixed|string
 */
function clear_html($HTML, $br = true){
	$HTML = htmlspecialchars(trim($HTML));
	$HTML = str_replace("\t", ' ', $HTML);
	if ($br) {
		return nl2br($HTML);
	} else {
		return str_replace("\n", '', $HTML);
	} 
} 

/**
 * 将obj深度转化成array
 * 
 * @param  $obj 要转换的数据 可能是数组 也可能是个对象 还可能是一般数据类型
 * @return array || 一般数据类型
 */
function obj2array($obj){
	if (is_array($obj)) {
		foreach($obj as &$value) {
			$value = obj2array($value);
		} 
		return $obj;
	} elseif (is_object($obj)) {
		$obj = get_object_vars($obj);
		return obj2array($obj);
	} else {
		return $obj;
	} 
} 

/**
 * 计算时间差
 * 
 * @param string $pretime
 * @return string
 */
function spend_time(&$pretime){
	$now = microtime(1);
	$spend = round($now - $pretime, 5);
	$pretime = $now;
	return $spend;
} 

function check_code($code){
	$fontsize = 18;$len = strlen($code);
    $width = 70;$height=27;
    $im = @imagecreatetruecolor($width, $height) or die("create image error!");
    $background_color = imagecolorallocate($im, 255, 255, 255);
    imagefill($im, 0, 0, $background_color);  
    for ($i = 0; $i < 2000; $i++) {//获取随机淡色            
        $line_color = imagecolorallocate($im, mt_rand(180,255),mt_rand(160, 255),mt_rand(100, 255));
        imageline($im,mt_rand(0,$width),mt_rand(0,$height), //画直线
            mt_rand(0,$width), mt_rand(0,$height),$line_color);
        imagearc($im,mt_rand(0,$width),mt_rand(0,$height), //画弧线
            mt_rand(0,$width), mt_rand(0,$height), $height, $width,$line_color);
    }
    $border_color = imagecolorallocate($im, 160, 160, 160);   
    imagerectangle($im, 0, 0, $width-1, $height-1, $border_color);//画矩形，边框颜色200,200,200

    for ($i = 0; $i < $len; $i++) {//写入随机字串
//        $current = $code[mt_rand(0, strlen($code)-1)];
        $text_color = imagecolorallocate($im,mt_rand(30, 140),mt_rand(30,140),mt_rand(30,140));
        imagechar($im,10,$i*$fontsize+6,rand(1,$height/3),$code[$i],$text_color);
    }
    if(function_exists("imagejpeg")){
		header("Content-Type: image/jpeg");
		imagejpeg($im, null,90);//图片质量
	}else if(function_exists("imagegif")){
		header("Content-Type: image/gif");
		imagegif($im);
	}else if(function_exists("imagepng")){
		header("Content-Type: image/x-png");
		imagepng($im);
	}
    imagedestroy($im);//销毁图片
}

/**
 * 返回当前浮点式的时间,单位秒;主要用在调试程序程序时间时用
 * 
 * @return float 
 */
function microtime_float(){
	list($usec, $sec) = explode(' ', microtime());
	return ((float)$usec + (float)$sec);
}
/**
 * 计算N次方根
 * @param  $num 
 * @param  $root 
 * @return float
 */
function croot($num, $root = 3){
	$root = intval($root);
	if (!$root) {
		return $num;
	} 
	return exp(log($num) / $root);
} 

function add_magic_quotes($array){
	foreach ((array) $array as $k => $v) {
		if (is_array($v)) {
			$array[$k] = add_magic_quotes($v);
		} else {
			$array[$k] = addslashes($v);
		} 
	} 
	return $array;
} 
// 字符串加转义
function add_slashes($string){
	if (!$GLOBALS['magic_quotes_gpc']) {
		if (is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = add_slashes($val);
			} 
		} else {
			$string = addslashes($string);
		} 
	} 
	return $string;
} 

/**
 * hex to binary
 */
if (!function_exists('hex2bin')) {
	function hex2bin($hexdata)	{
		return pack('H*', $hexdata);
	}
}

/**
 * 二维数组按照指定的键值进行排序，
 * 
 * @return array $array = array(
 * array('name'=>'手机','brand'=>'诺基亚','price'=>1050),
 * array('name'=>'手表','brand'=>'卡西欧','price'=>960)
 * );$out = array_sort($array,'price');
 * @param $arr
 * @param $keys
 * @param string $type
 * @return array
 */
function array_sort($arr, $keys, $type = 'asc'){
	$keysvalue = $new_array = array();
	foreach ($arr as $k => $v) {
		$keysvalue[$k] = $v[$keys];
	} 
	if ($type == 'asc') {
		asort($keysvalue);
	} else {
		arsort($keysvalue);
	} 
	reset($keysvalue);
	foreach ($keysvalue as $k => $v) {
		$new_array[$k] = $arr[$k];
	} 
	return $new_array;
} 
/**
 * 遍历数组，对每个元素调用 $callback，假如返回值不为假值，则直接返回该返回值；
 * 假如每次 $callback 都返回假值，最终返回 false
 * 
 * @param  $array 
 * @param  $callback 
 * @return mixed 
 */
function array_try($array, $callback){
	if (!$array || !$callback) {
		return false;
	} 
	$args = func_get_args();
	array_shift($args);
	array_shift($args);
	if (!$args) {
		$args = array();
	} 
	foreach($array as $v) {
		$params = $args;
		array_unshift($params, $v);
		$x = call_user_func_array($callback, $params);
		if ($x) {
			return $x;
		} 
	} 
	return false;
} 
// 求多个数组的并集
function array_union(){
	$argsCount = func_num_args();
	if ($argsCount < 2) {
		return false;
	} else if (2 === $argsCount) {
		list($arr1, $arr2) = func_get_args();

		while ((list($k, $v) = each($arr2))) {
			if (!in_array($v, $arr1)) $arr1[] = $v;
		} 
		return $arr1;
	} else { // 三个以上的数组合并
		$arg_list = func_get_args();
		$all = call_user_func_array('array_union', $arg_list);
		return array_union($arg_list[0], $all);
	} 
}
// 取出数组中第n项
function array_get($arr,$index){
   foreach($arr as $k=>$v){
       $index--;
       if($index<0) return array($k,$v);
   }
}

function show_tips($message){
	echo<<<END
<html>
	<style>
	#msgbox{border: 1px solid #ddd;border: 1px solid #eee;padding: 30px;border-radius: 5px;background: #f6f6f6;
	font-family: 'Helvetica Neue', "Microsoft Yahei", "微软雅黑", "STXihei", "WenQuanYi Micro Hei", sans-serif;
	color:888;font-size:13px;margin:0 auto;margin-top:10%;width: 400px;font-size: 16;color:#666;}
	#msgbox #title{padding-left:20px;font-weight:800;font-size:25px;}
	#msgbox #message{padding:20px;}
	</style>
	<body>
	<div id="msgbox">
	<div id="title">tips</div>
	<div id="message">$message</div>
	</body>
</html>
END;
	exit;
} 
/**
 * 打包返回AJAX请求的数据
 * @params {int} 返回状态码， 通常0表示正常
 * @params {array} 返回的数据集合
 * @param $data
 * @param bool $code
 * @param string $info
 */
function show_json($data,$code = true,$info=''){
	$use_time = mtime() - $GLOBALS['config']['app_startTime'];
	$result = array('code' => $code,'use_time'=>$use_time,'data' => $data);
	if ($info != '') {
		$result['info'] = $info;
	}
	header("X-Powered-By: kodExplorer.");
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($result);
	exit;
} 

/**
 * 简单模版转换，用于根据配置获取列表：
 * 参数：cute1:第一次切割的字符串，cute2第二次切割的字符串,
 * arraylist为待处理的字符串，$this 为标记当前项，$this_str为当项标记的替换。
 * $tpl为处理后填充到静态模版({0}代表切割后左值,{1}代表切割后右值,{this}代表当前项填充值)
 * 例子：
 * $arr="default=淡蓝(默认)=5|mac=mac海洋=6|mac1=mac1海洋=7";
 * $tpl="<li class='list {this}' theme='{0}'>{1}_{2}</li>\n";
 * echo getTplList('|','=',$arr,$tpl,'mac'),'<br/>';
 */
/**
 * @param $cute1
 * @param $cute2
 * @param $arraylist
 * @param $tpl
 * @param $this
 * @param string $this_str
 * @return string
 */
function getTplList($cute1, $cute2, $arraylist, $tpl,$this,$this_str=''){
	$list = explode($cute1, $arraylist);
	if ($this_str == '') $this_str ="this";
	$html = '';
	foreach ($list as $value) {
		$info = explode($cute2, $value);
		$arr_replace = array();	
		foreach ($info as $key => $v) {
			$arr_replace[$key]='{'.$key .'}';
		}
		if ($info[0] == $this) {
			$temp = str_replace($arr_replace, $info, $tpl);
			$temp = str_replace('{this}', $this_str, $temp);
		} else {
			$temp = str_replace($arr_replace, $info, $tpl);
			$temp = str_replace('{this}', '', $temp);
		}
		$html .= $temp;
	} 
	return $html;
} 

//获取当前url地址
function get_url() {
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] 
					== '443' ? 'https://' : 'http://';
	$php_self   = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	$path_info  = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	$relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 
				$php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
	return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}



// 获取内容第一条
function match($content, $preg){
	$preg = "/" . $preg . "/isU";
	preg_match($preg, $content, $result);
	return $result[1];
} 
// 获取内容,获取一个页面若干信息.结果在 1,2,3……中
function match_all($content, $preg){
	$preg = "/" . $preg . "/isU";
	preg_match_all($preg, $content, $result);
	return $result;
} 


/**
 * 获取变量的名字
 * eg hello="123" 获取ss字符串
 * @param $aVar
 * @return int
 */
function get_var_name(&$aVar){
	foreach($GLOBALS as $key => $var) {
		if ($aVar == $GLOBALS[$key] && $key != "argc") {
			return $key;
		} 
	}
	return  0;
} 
// -----------------变量调试-------------------
/**
 * 格式化输出变量，或者对象
 *
 * @param mixed $var
 * @param boolean $exit
 */
function pr($var, $exit = false){
    \Sharin\println($var,$exit);
}

/**
 * 调试输出变量，对象的值。
 * 参数任意个(任意类型的变量)
 */
function debug_out(){
	$avg_num = func_num_args();
	$avg_list = func_get_args();
	ob_start();
	for($i = 0; $i < $avg_num; $i++) {
		pr($avg_list[$i]);
	}
	$out = ob_get_clean();
	echo $out;
	exit;
} 

function rand_from_to($from, $to){
	return \Sharin\Core\SEK::random($from,$to);
} 

function rand_string($len = 4, $type='check_code'){
	return \Sharin\Core\SEK::randomString($len,$type);
} 

///**
// * 生成自动密码
// */
//function make_password(){
//	$temp = '0123456789abcdefghijklmnopqrstuvwxyz'.
//			'ABCDEFGHIJKMNPQRSTUVWXYZ~!@#$^*)_+}{}[]|":;,.'.time();
//	for($i=0;$i<10;$i++){
//		$temp = str_shuffle($temp.substr($temp,-5));
//	}
//	return md5($temp);
//}


///**
// * php DES解密函数
// *
// * @param string $key 密钥
// * @param string $encrypted 加密字符串
// * @return string
// */
//function des_decode($key, $encrypted){
//	$encrypted = base64_decode($encrypted);
//	$td = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_CBC, ''); //使用MCRYPT_DES算法,cbc模式
//	$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
//	$ks = mcrypt_enc_get_key_size($td);
//
//	mcrypt_generic_init($td, $key, $key); //初始处理
//	$decrypted = mdecrypt_generic($td, $encrypted); //解密
//
//	mcrypt_generic_deinit($td); //结束
//	mcrypt_module_close($td);
//	return pkcs5_unpad($decrypted);
//}
///**
// * php DES加密函数
// *
// * @param string $key 密钥
// * @param string $text 字符串
// * @return string
// */
//function des_encode($key, $text){
//	$y = pkcs5_pad($text);
//	$td = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_CBC, ''); //使用MCRYPT_DES算法,cbc模式
//	$ks = mcrypt_enc_get_key_size($td);
//
//	mcrypt_generic_init($td, $key, $key); //初始处理
//	$encrypted = mcrypt_generic($td, $y); //解密
//	mcrypt_generic_deinit($td); //结束
//	mcrypt_module_close($td);
//	return base64_encode($encrypted);
//}
function pkcs5_unpad($text){
	$pad = ord($text{strlen($text)-1});
	if ($pad > strlen($text)) return $text;
	if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return $text;
	return substr($text, 0, -1 * $pad);
} 
function pkcs5_pad($text, $block = 8){
	$pad = $block - (strlen($text) % $block);
	return $text . str_repeat(chr($pad), $pad);
} 
