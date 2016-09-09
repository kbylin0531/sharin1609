<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/7/16
 * Time: 4:15 PM
 */

namespace Library\MemberRegister;
use Library\MemberRigister;

class EC21MemberRegister extends MemberRigister {
    /**
     * @var string 打開註冊頁面 時候的cookie
     */
    private $register_page_cookie = '';
    /**
     * @var string 图片获取cookie
     */
    private $register_image_cookie = '';


    public function __construct(){
        $this->register_page_cookie = PUBE_COOKIE_DIR.'/Pube/Data/register/ec21.cookie';
        $this->register_image_cookie = PUBE_COOKIE_DIR.'/Pube/Data/register/ec21.image.cookie';
    }

    public function getRegisterPage(){
        $url = 'http://www.ec21.com/global/member/MyRegist.jsp';
        $content = self::get($url,'',$this->register_page_cookie,true);
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
        $path = PUBE_SCRIPT_DIR.'/'.ltrim($img_path,'/');
        self::touch($path);
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



    public function register(){
        $username = strtolower('zbg'.time());
        $phone = '1701180323';
        $email = $this->email;
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
            'captchaState'=>$this->capture,
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
        echo htmlspecialchars($content);
        if(strpos($content,'/myRegistSubmit.jsp')){
            //如果没有返回还是可以登录，邮箱不通过也可以...
        }
        return [
            'email'     =>  $email,
            'username'  =>  $username,
            'passwd'    =>  $username,
        ];
    }

    public function update(){
        $data = [
            'actionName' => 'update',
            'sellCategoryCode' => '4340',
            'buyCategoryCode' => '4314',
            'c_email1' => $this->email,
            'c_email2' => '',
            'mtype' => 'T',
            'flag' => '1',
            'gubuns' => 'S',
            'fileFlag2' => '0',
            'pimg2Local' => '',
            'Upimgname' => '',
            'pimg2LocalSize' => '',
            'logoimg_chk' => '2',
            'cimg1LocalSize' => '',
            'imageModifyFlag' => '0',
            'editBrochure' => '',
            'delBrochure' => '',
            'fn1' => '',
            'fOriNm1' => '',
            'fId1' => '',
            'fSize1' => '',
            'broTitle1' => '',
            'broDesc1' => '',
            'fn2' => '',
            'fOriNm2' => '',
            'fId2' => '',
            'fSize2' => '',
            'broTitle2' => '',
            'broDesc2' => '',
            'fn3' => '',
            'fOriNm3' => '',
            'fId3' => '',
            'fSize3' => '',
            'broTitle3' => '',
            'broDesc3' => '',
            'fn4' => '',
            'fOriNm4' => '',
            'fId4' => '',
            'fSize4' => '',
            'broTitle4' => '',
            'broDesc4' => '',
            'fn5' => '',
            'fOriNm5' => '',
            'fId5' => '',
            'fSize5' => '',
            'broTitle5' => '',
            'broDesc5' => '',
            'gubun' => 'S',
            'comp_nm' => 'Zbg'.time().' Corporation',
            'addr1' => 'Products or Selling Leads 3',
            'addr3' => 'Zhejiang',
            'stateSelect' => '',
            'addr2' => 'Hangzhou',
            'citySelect' => '0571',
            'zip_no' => '0571',
            'country_cd' => 'CN',
            'tel1_no' => '86',
            'tel2_no' => '0571',
            'tel3_no' => '84515458',
            'fax1_no' => '',
            'fax2_no' => '0571',
            'fax3_no' => '',
            'keyword' => 'Please',
            'keyword_s' => 'Please',
            'busi_no' => '',
            'trade_no' => '',
            'type' => '01',
            'found_dt' => '2010',
            'employee' => '04',
            'revenue_qt' => '03',
            'homelink' => '',
            'comp_info' => 'This is a description about nothing !Do not fire us,thank you sir,thank you sir,thank you sir,thank you sir!',
            'video_seq' => '',
            'ytVer' => '',
            'video_type' => '1',
        ];
        $content = self::post('http://www.ec21.com/global/basic/MyCompanyProfileSubmit.jsp',http_build_query($data),'',$this->register_page_cookie);
        $content = htmlspecialchars($content);
        echo "完善公司信息返回:{$content}<br>";
        return stripos($content,'OK') !== false;
    }

}