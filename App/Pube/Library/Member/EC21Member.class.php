<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/7/16
 * Time: 4:03 PM
 */

namespace Library\Member;
use Library\Member;
use Library\Utils\HttpRequest;
use Library\Utils\Jsdati;
use Sharin\Developer;
use Exception;
/**
 * Class EC21Member EC21平台的会员实体类
 *
 * 备注：EC21免费会员最多提交15个产品：You have 15 products and 1 group. ( Your product posting limit is reached, you are not allowed to post products anymore. )
 */
class EC21Member extends Member {
    protected $login_addresss = 'https://login.ec21.com/global/login/LoginSubmit.jsp';
    protected $register_address = 'http://www.ec21.com/global/member/myRegistSubmit.jsp';
    private $loginrecord = '';

    protected $hiddenFields = [
        'FBIn'  => '',
        'fEmail'  => '',
        'inq_gubun'  => '',
        'nextUrl'  => 'http://www.ec21.com/',
        'periodLimit'   => 'Y',
    ];

    //显式表单
    protected $usernameField  = 'user_id';
    protected $passwordField  = 'user_pw';
    protected $verifycodeField = '';//验证码

    /**
     * @var string 验证码
     */
    protected $verifycode = '';

    public function __construct($username, $password, $verifycode=''){
        parent::__construct($username, $password, $verifycode);
        $this->loginrecord = PUBE_COOKIE_DIR.'ec21.record.php';
    }

    /**
     * 检查用户是否登录
     * @param bool $tryifnot 未登录的情况下是否尝试登录
     * @return bool
     */
    public function login($tryifnot=true){
        //检查cookie之前先检查是否有登录记录
        $id = $this->getIdentify();
        $records = is_file($this->loginrecord)?include $this->loginrecord:[];
        Developer::trace($records);
        if($records and isset($records[$id]) and ($records[$id] - 60/* 距离过期时间60秒便认为是过期 */) > NOW and is_file($this->getCookie())){
            Developer::trace('cookie存在且未过期');
            return true;/*cookie被删除如何...*/
        }
        return parent::login($tryifnot);
    }

//-------------------------------------------------------------------------------------------------------------------

    /**
     * ec21登录成功时返回的头部中带有setcookie指令，登录失败没有
     * @param string $response
     * @return bool
     */
    protected function isLoginSuccess($response){
        return strpos($response,'Set-Cookie')?true:false;
    }

    /**
     * @param string $response
     * @return bool
     * @throws Exception
     */
    protected function saveExpareTimestamp($response){
        if(preg_match('/Expires=([\w,\s\d-:.]*\sGMT)/',$response,$matches)){
            if(isset($matches[1])){
                $timestamp = strtotime($matches[1]);
                $id = $this->getIdentify();
                $records = is_readable($this->loginrecord)?include $this->loginrecord:[];
                $records[$id] = $timestamp;//覆盖式
                if(!file_put_contents($this->loginrecord,'<?php /* 该文件中记录着cookie到期时间 */ return '.var_export($records,true).';')){
                    /* 返回false或者写入0字节 都是失败的(TODO:隐藏的问题是如果用户数过多可能会导致加载和写入过慢，后期可以考虑KV数据库) */
                    throw new Exception('EC21登录记录文件不可写!');
                }
            }
        }
        return false;
    }

    public function register($code=0){
        $username = 'Z'.time();
        $phone = '15658070289';
        $email = 'linzhv@qq.com';
        empty($_SESSION) and       session_start();
        $content = HttpRequest::post('http://www.ec21.com/global/captcha/captchaSubmit.jsp',json_encode(
            array(
                'adcopy_response'   => $code,
                'adcopy_challenge'  => $_SESSION['$chid'],
            )
        ),'JSESSIONID=73A076A4B7B2B07856F1909FF5765953.worker4e; __asc=62966f901570376b0afd3d5332e; __auc=62966f901570376b0afd3d5332e; __utmt=1; __utma=246840592.256602142.1473231893.1473231893.1473231893.1; __utmb=246840592.4.10.1473231893; __utmc=246840592; __utmz=246840592.1473231893.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none)');
        echo '<pre>';
        var_dump([
            array(
                'adcopy_response'   => $code,
                'adcopy_challenge'  => $_SESSION['$chid'],
            ),
            htmlspecialchars($content),
        ]);
        if(preg_match('/dataForm\.captchaState\.value[\s]*=[\s]*\'([\w\d\. - _] +)\'/',$content,$matches) and isset($matches[1])){
            $code = $matches[1];
        }else{
            exit('HHHHHHHHHHHHHHHHHHHHHHHHHHH');
        }

        $return = HttpRequest::post($this->register_address,$data,null,true);
        echo '<pre>';
        var_dump($this->register_address,$data);
        echo htmlspecialchars($return);
        if(strpos($return,'Wrong answer! Please try again.')){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 获取验证码的值
     * @param int $requestid 本次请求的ID
     * @param int $_times 内部获取验证码图片失败时重试的此书，由内部设置
     * @return bool
     */
    public function getVerifyCode($requestid, $_times=0){
        if($_times === 5){
            return false;
        }else{
            $_times ++;
        }
        empty($_SESSION) and       session_start();
        $time = $_SERVER['REQUEST_TIME'] -30;
        $time2 = $time + 3;
        $apiserver = 'http://api.solvemedia.com';
        $img_path = $requestid.'ec21.gif';
        $cookie_path =  PUBE_COOKIE_DIR.'/'.$time.'ec21.cookie';
        $cookie2_path =  PUBE_COOKIE_DIR.'/ec21page.cookie';
        $magic_url = $apiserver.'/papi/challenge.script?k=9PRRNhB78ykeJvMH-fDB-ypgIsmsdvyB';
        $encode_url = 'http://www.ec21.com/global/captcha/captchaSubmit.jsp';//图片验证处理地址

        //獲取ec21的cookie，第一次接收很簡單的cookie
//        $content = HttpRequest::get2('http://www.ec21.com/global/member/MyRegist.jsp','',$cookie2_path);//46E98DC4C1B20BF96E2D791EFE9EE36D.worker1
//        $content = htmlspecialchars($content);
//        echo "获取reg_page返回：{$content}<br>";
//        $content = HttpRequest::get('http://www.ec21.com/global/captcha/captcha.jsp?type=MyRegist',$cookie2_path);
//        $content = htmlspecialchars($content);
//        echo "获取captcha_frame_page返回：{$content}<br>";

        $content = HttpRequest::get($magic_url,'',false);
//        $content = htmlspecialchars($content);
//        echo "获取magic返回：{$content}<br>";
        if(preg_match('/magic[\s]*:[\s]*\'([\w\d\.-_]+)\'/',$content,$matches) and isset($matches[1])){
            $magic = $matches[1];
        }else{
            return $this->getVerifyCode($requestid,$_times);
        }
        $cookie_url = $apiserver.'/papi/_challenge.js?k=9PRRNhB78ykeJvMH-fDB-ypgIsmsdvyB;f=_ACPuzzleUtil.callbacks%5B0%5D;l=en;t=img;s=300x150;c=js,h5c,h5ct,svg,h5v,v/h264,v/ogg,v/webm,h5a,a/mp3,a/ogg,ua/chrome,ua/chrome52,os/linux,swf22,swf22.0,swf,fwv/M7oZvQ.fbsw41,jslib/jquery,htmlplus;am='.$magic.';ca=script;ts='.$time.';ct='.$time2.';th=custom;r=0.0021079165513289144';
        $content = HttpRequest::get($cookie_url,$cookie_path,true);
//        $content = htmlspecialchars($content);
//        echo "获取api_cookie返回：{$content}<br>";

        if(preg_match('/\"chid\"[\s]*:[\s]*\"([\.\@\d\w-_.]+)\"/',$content,$matches) and isset($matches[1])){
            $_SESSION['$chid'] = $matches[1];
            $verycode_url = $apiserver.'/papi/media?c='.$_SESSION['$chid'].';w=300;h=100;fg=000000';
            $content = HttpRequest::get($verycode_url,$cookie_path,false);
            $size = file_put_contents($img_path,$content);
            echo "<img src='".dirname($_SERVER['SCRIPT_NAME'])."/{$img_path}' /><br>";
//            if($size < 300){
//                /* 准确来说是227 media-error.gif的大小 */
//                return $this->getVerifyCode($requestid,$_times);
//            }else{
//                $code = $this->verifyImage($img_path,31);
//                $content = HttpRequest::post($encode_url,array(
//                    'adcopy_response'   => $code,
//                    'adcopy_challenge'  => $chid,
//                ),null,true);
//                echo "<img src='".dirname($_SERVER['SCRIPT_NAME'])."/{$img_path}' />{".($code?$code:'无法识别')."}<br>";
//                if(preg_match('/dataForm\.captchaState\.value[\s]*=[\s]*\'([\w\d\. - _] +)\'/',$content,$matches) and isset($matches[1])){
//                    return $matches[1];
//                }
//            }
        }
        return false;
    }

    /**
     * @param $img
     * @param int $mark 默认4个字母
     * @return bool|string
     */
    private function verifyImage($img,$mark=0){
        static $engine = null;
        if(!$engine){
            $engine = new Jsdati('bossgoo','Bossgoo123');
        }
        $result = $engine->jsdati_upload($img,$mark);
        if(is_string($result) and ($result = json_decode($result,true))){
            if(!empty($result['result'])){
                $val = $result['data']['val'];
                return $val;
            }
        }
        return false;
    }

}