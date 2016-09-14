<?php

/*
* @link http://www.kalcaddle.com/
* @author warlee | e-mail:kalcaddle@qq.com
* @copyright warlee 2014.(Shanghai)Co.,Ltd
* @license http://kalcaddle.com/tools/licenses/license.txt
*------
* 字符串加解密类；
* 一次一密；且定时解密有效
* 可用于加密&动态key生成
* demo：	
* 加密：echo Mcrypt::encode('abc','123');
* 解密：echo Mcrypt::decode('9f843I0crjv5y0dWE_-uwzL_mZRyRb1ynjGK4I_IACQ','123');
*/

class Mcrypt extends \Sharin\Library\Helper\Base64x {}