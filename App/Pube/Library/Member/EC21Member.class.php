<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/7/16
 * Time: 4:03 PM
 */

namespace Library\Member;
use Library\Member;
use Sharin\Developer;
use Exception;
/**
 * Class EC21Member EC21平台的会员实体类
 *
 * 备注：EC21免费会员最多提交15个产品：You have 15 products and 1 group. ( Your product posting limit is reached, you are not allowed to post products anymore. )
 */
class EC21Member extends Member {
    protected $login_addresss = 'https://login.ec21.com/global/login/LoginSubmit.jsp';
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

    public function __construct($username, $password='', $verifycode=''){
        parent::__construct($username, $password, $verifycode);
        self::touch($this->loginrecord = PUBE_DATA_DIR.'ec21.record.php');
    }

    public static function getAvailableAccount(){
        $list = \Library\Model\MemberModel::getInstance()->select('total < 15 and platform = \'ec21\'');
        if(count($list)){
            return array_shift($list);
        }else{
            return false;
        }
    }

    /**
     * 检查用户是否登录
     * @return bool
     */
    public function login(){
        //检查cookie之前先检查是否有登录记录
        $id = $this->getIdentify();
        $records = is_file($this->loginrecord)?include $this->loginrecord:[];
        Developer::trace($records);
        if($records and isset($records[$id]) and ($records[$id] + 60/* 距离过期时间60秒便认为是过期 */) > NOW and is_file($this->getCookie())){
            Developer::trace('cookie存在且未过期');
            return true;/*cookie被删除如何...*/
        }else{
            Developer::trace('cookie不存在或者已过期');
        }
        return parent::login();
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
                if(!is_array($this->loginrecord)){
                    $records = [];
                }
                $records[$id] = $timestamp;//覆盖式
                if(!file_put_contents($this->loginrecord,'<?php /* 该文件中记录着cookie到期时间 */ return '.var_export($records,true).';')){
                    throw new Exception('EC21登录记录文件不可写!');
                }
            }
        }
        return false;
    }

}