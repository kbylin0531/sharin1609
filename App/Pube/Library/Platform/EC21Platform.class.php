<?php
namespace Library\Platform;
use Library\Platform;
use Library\Utils\HttpRequest;
use Sharin\Developer;
use Exception;
use Sharin\Library\Session;

/**
 * Class EC21Platform
 *
 * @property string $gcatalog_id
 * @property string $categorymId
 * @property string $categoryNm
 *
 * @package Library\Platform
 */
class EC21Platform extends Platform {
    /**
     * EC21Platform constructor.
     */
    public function __construct() {
        $this->register_page_cookie = PUBE_COOKIE_DIR.'/register/ec21.cookie';
        $this->register_image_cookie = PUBE_COOKIE_DIR.'/ec21.image.cookie';
        self::touch($this->loginrecord = PUBE_DATA_DIR.'record/ec21.inc');
    }

    /**
     * @return EC21Platform
     */
    public static function getInstance(){
        return parent::getInstance();
    }

//--------------------------------- 产品提交 ----------------------------------------------------------------------------------//
    protected $submitAddress   = 'http://www.ec21.com/global/basic/MyProductEditSubmit.jsp';
    protected $attrs = [
        //必要参数
        'catalog_nm'=>'My Product',
        'allDesc'=>'',//描述,要求一定字数
        'origin'=>'CN',//商品产地
        'display'=>'Y',//'N'
        'categorymId'=>'212815',//分类ID
        'categoryNm'=>'Pharmaceutical Intermediates',//分类名称
        'gcatalog_id'=>'GC10133729',//产品分组ID
        //可选
        //可以通过方法设置
        'keyword'=>'',
        'keyword1'=>'',
        'keyword2'=>'',
        'keyword3'=>'',
        'keyword4'=>'',
        'keyword_s'=>'',
        //自动设置（无关参数）
        'frequencyCategory'=>'',//常用分类，默认为''
        'Upimgname'=>'',//上传图片访问地址
        'attribute_input_580'=>'',
        'attribute_input_66'=>'',
        'brand'=>'',
        'catalog_id'=>'',
        'categoryCd'=>'',
        'certi_num_CCC'=>'',
        'certi_num_CE'=>'',
        'certi_num_FCC'=>'',
        'certi_num_FDA'=>'',
        'certi_num_TUV'=>'',
        'certi_num_UL'=>'',
        'currency'=>'',
        'imageModifyFlag'=>'0',
        'leadtime'=>'',
        'measure_unit'=>'',
        'minorder'=>'',
        'model'=>'',
        'noGroupFlag'=>'',
        'package'=>'',
        'package_hei'=>'',
        'package_len'=>'',
        'package_unit'=>'',
        'package_wei'=>'',
        'package_wid'=>'',
        'package_wunit'=>'',
        'pageNum'=>'1',
        'pimg1LocalSize'=>'',
        'pimg_0'=>'',
        'port'=>'',
        'price'=>'',
        'price_condition'=>'04',//价格条件
        'role_nms'=>'',
        'supplyability'=>'',
        'usereattri_nm1'=>'',
        'usereattri_nm2'=>'',
        'usereattri_nm3'=>'',
        'usereattri_nm4'=>'',
        'usereattri_nm5'=>'',
        'usereattri_val1'=>'',
        'usereattri_val2'=>'',
        'usereattri_val3'=>'',
        'usereattri_val4'=>'',
        'usereattri_val5'=>'',
        'video_seq'=>'',
        'video_type'=>'1',
    ];

    /**
     * 设置产品名称
     * @param $name
     * @return $this
     * @throws Exception
     */
    public function setName($name){
        if(strlen($name) >= 5){
            $this->attrs['catalog_nm'] = $name;
        }else{
            throw new Exception('名称不合法！');
        }
        return $this;
    }

    /**
     * 设置产品描述
     * 字数限制
     * @param $description
     * @return $this
     */
    public function setDescription($description){
        $this->attrs['allDesc'] = $description;
        return $this;
    }

    /**
     * 添加关键字列表，以逗号i分隔
     * @param $keyworks
     */
    public function addKeywords($keyworks){
        $keyworks = explode(',',$keyworks);
        foreach ($keyworks as $k){
            $this->addKeywork($k);
        }
    }

    /**
     * 添加单个关键词
     * @param string $keywork
     * @return $this
     * @throws Exception
     */
    public function addKeywork($keywork){
        if(empty($this->attrs['keyword'])){
            $this->attrs['keyword'] = $keywork;
        }elseif(empty($this->attrs['keyword1'])){
            $this->attrs['keyword1'] = $keywork;
        }elseif(empty($this->attrs['keyword2'])){
            $this->attrs['keyword2'] = $keywork;
        }elseif(empty($this->attrs['keyword3'])){
            $this->attrs['keyword3'] = $keywork;
        }elseif(empty($this->attrs['keyword4'])){
            $this->attrs['keyword4'] = $keywork;
        }else{
            throw new Exception('最多填写4个关键词');
        }
        if(empty($this->attrs['keyword_s'])){
            $this->attrs['keyword_s'] = $keywork;
        }else{
            $this->attrs['keyword_s'] .= ','.$keywork;
        }
        return $this;
    }

    /**
     * @param string $path 文件路径
     * @return string|false 返回文件上传地址,失败时返回false
     * @throws Exception
     */
    private function uploadImage($path){
        $imagename = 'PI_'.microtime(true);
        //第一次提交上传图片
        HttpRequest::post(
            'http://upimage.ec21.com/global/upload/product/productOneImageUploadForSwf.jsp',[
                'f_n'  => $imagename,
                'temp_file'  => new \CURLFile($path,'application/octet-stream'),
                'Upload'  => 'Submit Query',
            ],
            $this->cookie_login,true
        );//返回\n * 15
        //第二次提交上获取图片地址
        $fields = [
            'fileFlag'  => 1,
            'type'  => 'PI',
            'pnum'  => 0,
            'pimg1Local'  => $imagename.'.jpg',
            'pimg1LocalSize'    => 9472,
            'hideFileID'    => '',
        ];
        $result2 = HttpRequest::post(
            'http://www.ec21.com/global/fileup/UploadImageResultMulti.jsp',
            $fields,
            $this->cookie_login,
            true
        );

        if(preg_match('/http:\/\/upimage\.ec21\.com\/upload\/temporary\/([\w\d_\.]+)/',$result2,$matches)){
            if(isset($matches[1])){
                return $matches[0];
            }
        }
        return false;
    }
    /**
     * @return bool
     */
    public function submit(){
        $this->attrs['actionName'] = 'insert';//表示添加操作
        if($this->images){
            $image = $this->uploadImage($this->images);
            if($image){
                $this->attrs['Upimgname'] = $image;
            }
        }
        return parent::submit();
    }

    protected $error = '';

    /**
     * 产品成功提交返回的信息：
     * HTTP/1.1 100 Continue HTTP/1.1 302 Found Date: Thu, 01 Sep 2016 08:54:18 GMT Server: Apache Set-Cookie: JSESSIONID=96A9AF56BD1833D44EE7E3D65F1A8B5B.worker1e; Path=/global/; HttpOnly Location: http://www.ec21.com/global/basic/MyEcSuccess.jsp?transposition=product&pageNum=1 Content-Length: 0 Vary: User-Agent Content-Type: text/html;charset=utf-8
     * alert(\'Select a category.\'); 表示分类未能完全获取(实际上时描述未设置)
     * @param string $response
     * @return bool
     */
    public function isSucmitSuccess($response){
        if(strpos($response,'global/basic/MyEcSuccess.jsp')) {
            return true;
        }else{
            if(strpos($response,'Exceed the fixed numer of open products')){
                $this->error = '提交的产品数量达到上限';//
            }else{
                $this->error = '未知的错误';
            }
            return false;
        }
    }

    public function getLastSubmit(){
        $url = 'http://www.ec21.com/global/basic/MyProductList.jsp';
        $content = self::get($url,'',$this->cookie_login);
        $len = preg_match_all('/<a\shref=\"([^\"]+)\"\starget=\"\_NEW1">/',$content,$matches);
        if($len  and isset($matches[1][$len-1])) {
            return $matches[1][$len-1];
        } else {
            return '';
        }
    }
//-------------------------------------- 登录 ------------------------------------------------------------------------//

    protected function isLoginSuccess($response){
        return strpos($response,'Set-Cookie')?true:false;
    }

    public function login($username,$password='',$capture=''){
        $this->username = $username;
        //检查cookie之前先检查是否有登录记录
        $id = $this->getIdentify();
        $records = is_file($this->loginrecord)?include $this->loginrecord:[];
        Developer::trace($records);
        if($records and isset($records[$id]) and ($records[$id] + 60/* 距离过期时间60秒便认为是过期 */) > NOW and is_file($this->cookie_login)){
            Developer::trace('cookie存在且未过期');
            return true;/*cookie被删除如何...*/
        }else{
            Developer::trace('cookie不存在或者已过期');
        }
        return parent::login($username);
    }

//------------------------------------------- 注册 ----------------------------------------------------------------------//
    /**
     * @var string 打開註冊頁面 時候的cookie
     */
    private $register_page_cookie = '';
    /**
     * @var string 图片获取cookie
     */
    private $register_image_cookie = '';


    private function getRegisterPage(){
        $url = 'http://www.ec21.com/global/member/MyRegist.jsp';
        $content = self::get($url,'',$this->register_page_cookie,true);
        return $content;
    }

    private function getMagic(){
        $url = 'http://api.solvemedia.com/papi/challenge.script?k=9PRRNhB78ykeJvMH-fDB-ypgIsmsdvyB';
        $content = self::get($url,'','',false);
        if(preg_match('/magic[\s]*:[\s]*\'([\w\d\.-_]+)\'/',$content,$matches) and isset($matches[1])){
            return $matches[1];
        }
        return false;
    }

    private function getChidId($magic=''){
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
     * @param string $capture
     * @return $this
     */
    public function setCapture($capture) {
        $this->capture = $this->postCode($capture,Session::get('childId'));
        return $this;
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
        $size = file_put_contents($path,$content);
        if($size < 300){
            /* 准确来说是227 media-error.gif的大小 */
            return false;
        }else {
            return $img_path;
        }
    }

    /**
     * 获取登录验证码
     * @return string
     */
    public function getRegisterCapture(){
        $this->getRegisterPage();
        $magic = $this->getMagic();
        $childId = $this->getChidId($magic);
        Session::set('childId',$childId);
        return $this->saveImage('',$childId);
    }

    public function postCode($code,$chid=''){
        $chid or $chid = $this->getChidId();
        $url = 'http://www.ec21.com/global/captcha/captchaSubmit.jsp';
        $content = HttpRequest::post($url,http_build_query(array( //application/x-www-form-urlencoded
            'adcopy_response'   => $code,
            'adcopy_challenge'  => $chid,
        )),$this->register_page_cookie);
        if(preg_match("/dataForm\.captchaState\.value[\s]*=[\s]*\'(.*)\'/",$content,$matches) and isset($matches[1])){
            return $matches[1];
        }
        return false;
    }

    /**
     * 注册账号
     * @return array|false
     */
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
        $content = HttpRequest::post('http://www.ec21.com/global/member/myRegistSubmit.jsp',http_build_query($data),$this->register_page_cookie,true);

//        echo var_export(['http://www.ec21.com/global/member/myRegistSubmit.jsp',http_build_query($data),$this->register_page_cookie],true);
//        echo htmlspecialchars($content);die();
        if(strpos($content,'/myRegistOk.jsp')){
            //返回自动登录表单（自动执行submit）
            return [
                'email'     =>  $email,
                'username'  =>  $username,
                'passwd'    =>  $username,
            ];
        }elseif(strpos($content,'MyRegistError.jsp')){
            //信息填写错误
            return false;
        }else{
            //未知原因
            return false;
        }

    }

    /**
     * 船舰产品分类组
     * @return array|bool
     */
    public function createCategory(){
        $url = 'http://www.ec21.com/global/basic/MyPGroupEditSubmit.jsp?actionName=insertPop';
        $content = HttpRequest::post($url,http_build_query([
            'fileFlag'=>'0/',
            'pimg1Local'=>'',
            'pimg1LocalSize'=>'',
            'gcatalog_id'=>'',
            'pageNum'=>'',
            'tag'=>'Y',
            'gcatalog_nm'=>'Default',
            'allDesc'=>'',
            'display'=>'Y',
        ]),$this->cookie_login,true);
        echo htmlspecialchars($content);
        if(preg_match("/option\\svalue='(.*)'\\>(.*)\\<\\//",$content,$matches) and isset($matches[1],$matches[2])){
            return [
                $matches[1],//code
                'Default',//name
            ];
        }
        return false;
    }

    /**
     * 完善公司信息
     * @param array $info
     * @return bool
     */
    public function update(array $info=[]){
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
        $info = array_merge($data,$info);
        $content = HttpRequest::post('http://www.ec21.com/global/basic/MyCompanyProfileSubmit.jsp',http_build_query($info),$this->register_page_cookie);
        $content = htmlspecialchars($content);
//        echo "完善公司信息返回:{$content}<br>";
        return stripos($content,'OK') !== false;
    }
}