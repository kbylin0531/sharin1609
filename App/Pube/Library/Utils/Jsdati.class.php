<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/7/16
 * Time: 4:06 PM
 */

namespace App\Pube\Library\Utils;

class Jsdati {

    private $user_name = 'bossgoo';//用户帐号
    private $user_pw = 'Bossgoo123';//用户密码
    private $user_token = '';//（有软件token的填写token，没有的请填写作者帐号）

    /**
     * Jsdati constructor.
     * @param $user_name
     * @param $user_pw
     * @param string $user_token 用户名，默认为空
     */
    public function __construct($user_name,$user_pw,$user_token=''){
        $this->user_name = $user_name;
        $this->user_pw = $user_pw;
        $this->user_token = $user_token ? $this->user_name : $user_token;
    }

    /**
     * 验证码上传函数
    0	四个字母,数字	1个验证码(1分钱)
    1	2位中文汉字	1个验证码(2分钱)
    2	选择题	1个验证码(1分钱)
    3	九宫格验证码	1个验证码(2分钱)
    5	计算题	1个验证码(1分钱)
    22	3位中文汉字	1个验证码(2分钱)
    23	4位中文汉字	1个验证码(4分钱)
    255	阳光验证码	1个验证码(1分钱)
    21	一位中文汉字验证码	1个验证码(2分钱)
    13	数字验证码	1个验证码(1分钱)
    14	问答题	1个验证码(2分钱)
    15	谷歌验证码	1个验证码(1分钱)
    8	原型验证码	1个验证码(1分钱)
    10	数字验证码	1个验证码(1分钱)
    25	5位字母加数字(5位纯字母）	1个验证码(1分钱)
    26	6位字母加数字（6位纯字母）	1个验证码(1分钱)
    16	纯字母验证码	1个验证码(1分钱)
    17	纯数字验证码	1个验证码(1分钱)
    28	7位字母加数字（7位纯字母）	1个验证码(1分钱)
    29	两位数字	1个验证码(1分钱)
    12	安卓左旋转验证码	1个验证码(1分钱)
    31	8位或8位以上字母	1个验证码(2分钱)
    68	坐标题点击2次	1个验证码(2分钱)
    69	坐标题点击4次	1个验证码(2分钱)
    70	坐标点击一次	1个验证码(2分钱)
    100	动态验证码	1个验证码(1分钱)
    18	看图题	1个验证码(1分钱)
    19	要求题目	1个验证码(1分钱)
    11	安卓验证码	1个验证码(1分钱)
    4	选择题 2	1个验证码(1分钱)
     *
     * @param string $yzm_img:[必填]验证码相对路径，如'yzmimg/1.jpg'
     * @param int $yzm_mark:[必填]验证码类型（http://www.jsdati.com/index.php/page/price）
     * @param string $yzm_minlen:[非必填]验证码最小长度
     * @param string $yzm_maxlen:[非必填]验证码最大长度
     * @return mixed
     */
    public function jsdati_upload($yzm_img, $yzm_mark = 0, $yzm_minlen = null, $yzm_maxlen = null) {
        set_time_limit(0);
        if (class_exists('CURLFile')) {
            $data_arr['upload'] = new \CURLFile(realpath($yzm_img));
        } else {
            $data_arr['upload'] = '@'.realpath($yzm_img);
        }
        $data_arr['yzm_minlen'] = $yzm_minlen;
        $data_arr['yzm_maxlen'] = $yzm_maxlen;
        $data_arr['yzmtype_mark'] = $yzm_mark;
        return $this->jsdati_post('upload', $data_arr);
    }

    /**
     * 验证码报错函数
     *
     * @param string $yzm_id [必填]验证码上传成功后返回的id
     * @return mixed
     */
    public function jsdati_error($yzm_id) {
        return $this->jsdati_post('error', array('yzm_id'=>$yzm_id));
    }

    /**
     * 查询账户点数函数
     */
    public function jsdati_point() {
        return $this->jsdati_post('point');
    }


    /**
     * curl模拟post提交函数
     * @param $type
     * @param array $val
     * @return mixed
     */
    private function jsdati_post($type, $val = null) {
        $data = array(
            'user_name' => $this->user_name,
            'user_pw' => $this->user_pw,
            'zztool_token' => $this->user_token,
        );
        if (is_array($val)) {
            $data = array_merge($data , $val);
        }
        $http = curl_init("http://bbb4.hyslt.com/api.php?mod=php&act={$type}");
        curl_setopt($http, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($http, CURLOPT_POST, 1);
        curl_setopt($http, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($http);
        curl_close($http);
        return $result;
    }

}