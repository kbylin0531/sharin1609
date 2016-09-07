<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/7/16
 * Time: 4:15 PM
 */

namespace App\Pube\Library\MemberRegister;
use App\Pube\Library\MemberRigister;
use Sharin\Core\Storage;

class EC21MemberRegister extends MemberRigister {
    /**
     * @var string 打開註冊頁面 時候的cookie
     */
    private $register_page_cookie = '';
    /**
     * @var string
     */
    private $register_framepage_cookie = '';

    private $register_image_cookie = '';

    public function __construct(){
        $this->register_page_cookie = PATH_COOKIE.'/Pube/Data/register/ec21.cookie';
        $this->register_framepage_cookie = PATH_COOKIE.'/Pube/Data/register/ec21.frame.cookie';
        $this->register_image_cookie = PATH_COOKIE.'/Pube/Data/register/ec21.image.cookie';
    }

    public function getRegisterPage(){
        $url = 'http://www.ec21.com/global/member/MyRegist.jsp';
        $content = self::get($url,'',$this->register_page_cookie,true);
        return $content;
    }

    public function getFramePage(){
        $url = 'http://www.ec21.com/global/captcha/captcha.jsp?type=MyRegist';
        $content = self::get($url,'',$this->register_framepage_cookie,true);
        return $content;
    }
    public function getMagic(){
        $url = 'http://api.solvemedia.com/papi/challenge.script?k=9PRRNhB78ykeJvMH-fDB-ypgIsmsdvyB';
        $content = self::get($url,'','',false);
        if(preg_match('/magic[\s]*:[\s]*\'([\w\d\.-_]+)\'/',$content,$matches) and isset($matches[1])){
            return $matches[1];
        }
        return false;
    }

    public function getChidId($magic=''){
        $time = time(); $time2 = $time + 3;
        $magic or $magic = $this->getMagic();
        $url = 'http://api.solvemedia.com/papi/_challenge.js?k=9PRRNhB78ykeJvMH-fDB-ypgIsmsdvyB;f=_ACPuzzleUtil.callbacks%5B0%5D;l=en;t=img;s=300x150;c=js,h5c,h5ct,svg,h5v,v/h264,v/ogg,v/webm,h5a,a/mp3,a/ogg,ua/chrome,ua/chrome52,os/linux,swf22,swf22.0,swf,fwv/M7oZvQ.fbsw41,jslib/jquery,htmlplus;am='.$magic.';ca=script;ts='.$time.';ct='.$time2.';th=custom;r=0.0021079165513289144';
        $content = self::get($url,'',$this->register_image_cookie);
        if(preg_match('/\"chid\"[\s]*:[\s]*\"(.*)\"/',$content,$matches) and isset($matches[1])) {
            return $matches[1];
        }
        return false;
    }

    /**
     * @param string $img_path oppsite to script path
     * @param string $chid
     * @param string $magic
     * @return bool|string
     */
    public function saveImage($img_path='',$chid='',$magic=''){
        $chid or $chid = $this->getChidId($magic);
//        $img_path or $img_path = SR_PATH_RUNTIME.'/captures/'.md5($chid).'ec21.gif';
        $img_path or $img_path = '/dynamic/capture/'.md5($chid).'ec21.gif';
        $url = 'http://api.solvemedia.com/papi/media?c='.$chid.';w=300;h=100;fg=000000';
        $content = self::get($url,'',$this->register_image_cookie);
        $path = SR_PATH_BASE.'/Public/'.ltrim($img_path,'/');
        Storage::touch($path);
        echo is_file($path)?'Y':'N';
        $size = file_put_contents($path,$content);
        if($size < 300){
            /* 准确来说是227 media-error.gif的大小 */
            return false;
        }else {
            return $img_path;
        }
    }

    public function postCode($code,$chid=''){
        $chid or $chid = $this->getChidId();
        $url = 'http://www.ec21.com/global/captcha/captchaSubmit.jsp';
        $content = self::post($url,http_build_query(array( //application/x-www-form-urlencoded
            'adcopy_response'   => $code,
            'adcopy_challenge'  => $chid,
        )),'',$this->register_page_cookie);
        if(preg_match("/dataForm\.captchaState\.value[\s]*=[\s]*\'(.*)\'/",$content,$matches) and isset($matches[1])){
            return $matches[1];
        }
        return false;
    }
    public function register($code){
        $username = strtolower('zbg'.time());
        $phone = '15658070289';
        $email = '380636453@qq.com';
        $tel = [
            'tel1_no'=>'86',
            'tel2_no'=>substr($phone,0,3),
            'tel3_no'=>substr($phone,3),
        ];
        $comp_nm = ucfirst($username).' Corporation';
        $data = [
            'languageSelect'=>'chinese',
            'languageSelect1'=>'chinese',
            'another'=>'',
            'another2'=>'',
            'chk_ids'=>'Y',
            'reg_class'=>'F',
            'mType'=>'T',
            'gubun'=>'S',
            'actionName'=>'insert',
            'inKn'=>'',
            'FBIn'=>'',
            'fEmail'=>'',
            'captchaState'=>$code,
            'country'=>'CN',
            'gubuns'=>'S',
            'contact_sex'=>'M',
            'comp_nm'=> $comp_nm,
            'email'=>$email,
            'checkedEmail'=>$email,
            'isValidEmail'=>'true',
            'member_id'=>$username,
            'passwd'=>$username,
            're_passwd'=>$username,
            'mPlan'=>'N',
            'siteName'=>'',
            'noSite'=>'Y',
            'contact_nm'=>$username,
        ];
        $data = array_merge($data,$tel);
        $content = self::post('http://www.ec21.com/global/member/myRegistSubmit.jsp',http_build_query($data),'',$this->register_page_cookie);

        echo htmlspecialchars($content)."#";
        if(strpos($content,'/myRegistSubmit.jsp')){
            $data = [
                'another'   => '',
                'another2'   => '',
                'member_id'   => $username,
                'comp_nm'   => $comp_nm,
                'contact_nm'   => $username,
                'email1'   => $email,
                'gubun'   => 'S',
                'country_cd'   => 'CN',
                'mtype'   => 'T',
                'languageSelect'   => 'chinese',
                'facebookInvolve'   => '',
            ];
            $data = array_merge($data,$tel);
            $content = self::post('http://www.ec21.com/global/member/myRegistOk.jsp',http_build_query($data),'',$this->register_page_cookie,false,[
                CURLOPT_REFERER => 'http//www.ec21.com/global/member/myRegistSubmit.jsp',
            ]);
        }else{
            $content = false;
        }
        unlink($this->register_page_cookie);
        unlink($this->register_framepage_cookie);
        unlink($this->register_image_cookie);
        return $content;
    }


}