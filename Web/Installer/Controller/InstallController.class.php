<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/16
 * Time: 12:09
 */
namespace Application\Installer\Controller;
use Application\Installer\Model\InstallModel;
use Application\Installer\Util\DedeInstallKits;
use Application\Installer\Util\InstallKits;
use Application\Member\Model\MemberModel;
use System\Core\Configer;
use System\Core\Controller;
use System\Core\Router;
use System\Core\Storage;
use System\Util\SEK;
use System\Util\SessionUtil;

/**
 * Class InstallController CMS安装控制器
 * @package Application\Cms\Controller
 */
class InstallController extends Controller{
    /**
     * 安装配置文件目录
     * 同时兼任确定是否已经安装过
     * @var string
     */
    private static $config_file_path = null;

    /**
     * 安装步骤
     * @var array
     */
    private static $steps = array(
        0   => 'installer/install/index',
        1   => 'installer/install/first',
        2   => 'installer/install/second',
        3   => 'installer/install/third',
        4   => 'installer/install/complete'
    );

    public function __construct(){
        parent::__construct();
        $this->initialize();

        self::$config_file_path = $this->module_path.'/Configure/database.config.php';
        if($this->checkInstalled() and 'complete' !== Router::getParsed('a')){
            //已经安装完毕，直接跳转到完成界面，但是必须在并非访问complete的情况下，否则会导致重定向死循环
            $this->takeSteps(4);
        }

        //静态可以直接访问的文件目录
        defined('URL_CMS_STATIC_PATH') or define('URL_CMS_STATIC_PATH',URL_PUBLIC_PATH.'CMS/');
    }
    private function initialize(){

    }

    /**
     * 协议页面
     */
    public function index(){
        $this->display();
    }

    /**
     * 第一步操作页面
     * 运行环境检测
     */
    public function first(){
        $env = $this->checkEnv();
        $funcs = $this->checkFunc();
        $dirfile = $this->checkDirfile();

        SessionUtil::set('step', 1);

        $this->assign('env',$env);
        $this->assign('dirfile', $dirfile);
        $this->assign('funcs',$funcs);
        $this->display();
    }

    /**
     * 数据库配置和安装
     * @param array $db 数据库连接配置
     * @param array $admin 数据库管理员配置
     * @return void
     * @throws \Exception
     */
    public function second($db = null, $admin = null){
        if(IS_POST){
            //检测管理员配置
            if(SEK::checkInvalidValueExistInStrict(true,
                !is_array($admin) ,
                empty($admin[0]) , //名称
                empty($admin[1]),//密码 2-密码确认
                empty($admin[3])//邮箱
            )){
                //任意一项为空
                $this->error('请填写完整管理员信息');
            }else{
                //检测密码
                if($admin[1] !== $admin[2]) $this->error('确认密码和密码不一致');
                //保存信息
                $info = array();
                list($info['username'], $info['password'], $info['repassword'], $info['email']) = $admin;
                //缓存管理员信息
                SessionUtil::set('admin_info', $info);
            }

            //检测数据库配置
            if(SEK::checkInvalidValueExistInStrict(true,
                !is_array($db), empty($db[0]),empty($db[1]) , empty($db[2]) , empty($db[3])) ){
                //任意一项为空
                $this->error('请填写完整的数据库配置');
            }else{
                $config = array();
                list($config['type'], $config['host'], $config['dbname'], $config['username'],
                    $config['password'],$config['port'],$config['prefix']) = $db;
                //缓存数据库配置
                SessionUtil::set('database_info', $config);
                //创建数据库
                $installModel = new InstallModel($config,false);
                if(!$installModel->createDatabase($config['dbname'])){
                    $this->error('创建数据库失败，错误信息：'.$installModel->getErrorInfo());
                }

            }
            //跳转到数据库安装页面
            $this->takeSteps();
        }

        //检查并设置session
        if(SessionUtil::get('error')){
            $this->takeSteps(false,3,'环境检测没有通过，请调整环境后重试！');
        }
        $step = SessionUtil::get('step');
        if($step !== 1 && $step !== 2){
            $this->takeSteps(1,3,'即将跳转以重新检测环境！');
        }

        SessionUtil::set('step', 2);
        $this->display();
    }

    /**
     * 实际安装数据库表并设置管理员账号
     * @return void
     * @throws \Exception
     * @throws \System\Exception\FileNotFoundException
     */
    public function third(){
        $step = SessionUtil::get('step');
        if($step != 2 && $step !== 3){
            $this->takeSteps(0);
        }
        $this->display();


        //创建数据表
        InstallKits::flushMessageToClient('开始安装数据库...');
        $this->createTables();

        //注册创始人帐号
        InstallKits::flushMessageToClient('开始注册创始人帐号...');
        //重新设定数据库连接配置
        $db_info = SessionUtil::get('database_info');
        $memberModel = new MemberModel();
        $memberModel->init($db_info);
        //创建创始人账号
        $admin_info = SessionUtil::get('admin_info');
        $rst = $memberModel->registerMember($admin_info);
        if(is_string($rst) or !$rst){
            InstallKits::flushMessageToClient('创始人帐号注册失败！'.$rst);
        }else{
            InstallKits::flushMessageToClient('创始人帐号注册成功！');
        }

        if(SessionUtil::get('error')){
            InstallKits::flushMessageToClient('安装错误！');
        } else {
            SessionUtil::set('step', 3);
            //成功创建数据库，写入配置信息
            if(!Configer::write(self::$config_file_path, $db_info)){
                throw new \Exception('Store Configure into file failed!');
            }
            $this->takeSteps();
        }
    }

    /**
     * 完成显示页面
     * @throws \System\Exception\ParameterInvalidException
     */
    public function complete(){
        if(!Storage::has(self::$config_file_path)){
            // 写入安装锁定文件
            Storage::write(self::$config_file_path, SessionUtil::get('database_info'));
        }
        SessionUtil::set('step',4);
        SessionUtil::clear('error');
        $this->display();
    }

    /**
     * 步骤跳转
     * @param bool|true $forward true表示进入下一步，false表示返回上一步，int类型表示跳到制定的步骤
     * @param int $time          等待时间
     * @param string $message    跳转等待提示语
     */
    private function takeSteps($forward=true,$time=0,$message=''){
        if(is_bool($forward)){
            $curstep = SessionUtil::get('step');
            $forward? ++$curstep: --$curstep;
        }else{
            SessionUtil::set('step',$forward);
            $curstep = $forward;
        }
        $this->redirect(self::$steps[$curstep],array(),$time,$message);
    }

    /**
     * 确认是否安装过
     * @return bool
     */
    private function checkInstalled(){
        return SessionUtil::set('hasInstalled',Storage::has(self::$config_file_path));
    }

    /**
     * 创建数据表
     */
    private function createTables(){
        $dbconfig = SessionUtil::get('database_info');
        $installModel = new InstallModel($dbconfig);
        //读取SQL文件
        $sqls = Storage::read($this->module_path.'Common/install.sql');
        //设置前缀
        $sqls = str_replace(' `onethink_'," `{$dbconfig['prefix']}",  $sqls);
        $sqls = str_replace("\r", "\n", $sqls);//windows下转化换行符
        $sqls = explode(";\n", $sqls);

        //开始安装
        foreach ($sqls as $sql) {
            $sql = trim($sql);
            if(empty($sql) or substr($sql,0,2) === '--') continue;
            $msg = $installModel->execSql($sql);
            if(is_array($msg)){
                if(false === $msg[0]){
                    SessionUtil::set('error',true);
                }
                InstallKits::flushMessageToClient($msg[1]);
            }
        }
    }

    /**
     * 函数、扩展、方法的检测
     * @return array 检测数据
     */
    private function checkFunc(){
        $items = array(
            array('pdo',    '支持',   'success',  '类'),
            array('pdo_mysql',  '支持',   'success',  '模块'),
            array('file_get_contents',  '支持',   'success',  '函数'),
            array('mb_strlen',		   '支持',    'success',  '函数'),
        );

        foreach ($items as &$val) {
            if(('类'==$val[3] && !class_exists($val[0]))
                || ('模块'==$val[3] && !extension_loaded($val[0]))
                || ('函数'==$val[3] && !function_exists($val[0]))
            ){
                $val[1] = '不支持';
                $val[2] = 'error';
                SessionUtil::set('error', true);
            }
        }
        return $items;
    }

     /**
     * 目录，文件读写检测
     * @return array 检测数据
     */
    private function checkDirfile(){
        $items = array(
            //文件类型、所需状态、检测结果、目录名称(不完整)
            array('dir',  '可写', 'success', 'Data/CMS/'),
        );

        //目录检测
        foreach ($items as &$val) {
            $item =	BASE_PATH . $val[3];
            if('dir' == $val[0]){//如果是目录
                if(!is_writable($item)) {
                    if(is_dir($item)) {
                        $val[1] = '可读';
                        $val[2] = 'error';
                        SessionUtil::set('error', true);
                    } else {
                        $val[1] = '不存在';
                        $val[2] = 'error';
                        SessionUtil::set('error', true);
                    }
                }
            }else{//如果是文件
                if(file_exists($item)) {
                    if(!is_writable($item)) {
                        $val[1] = '不可写';
                        $val[2] = 'error';
                        SessionUtil::set('error', true);
                    }
                } else {
                    if(!is_writable(dirname($item))) {
                        $val[1] = '不存在';
                        $val[2] = 'error';
                        SessionUtil::set('error', true);
                    }
                }
            }
        }
        return $items;
    }

    /**
     * 运行环境检测
     * @return array
     */
    private function checkEnv(){
        $items = array(
            //检测相名称、所需配置、？、当前详细信息、检测结果
            'os'      => array('操作系统', '不限制', '类Unix', PHP_OS, 'success'),
            'php'     => array('PHP版本', '5.3', '5.3+', PHP_VERSION, 'success'),
            'upload'  => array('附件上传', '不限制', '2M+', '未知', 'success'),
            'gd'      => array('GD库', '2.0', '2.0+', '未知', 'success'),
            'disk'    => array('磁盘空间', '5M', '不限制', '未知', 'success'),
        );

        //PHP环境检测
        if($items['php'][3] < $items['php'][1]){
            $items['php'][4] = 'error';
            SessionUtil::set('error', true);
        }

        //附件上传检测
        if(@ini_get('file_uploads')){
            $items['upload'][3] = ini_get('upload_max_filesize');
        }else{
            //配置项目不存在
            SessionUtil::set('error', true);
        }

        //GD库检测
        $tmp = function_exists('gd_info') ? gd_info() : array();
        if(empty($tmp['GD Version'])){
            $items['gd'][3] = '未安装';
            $items['gd'][4] = 'error';
            SessionUtil::set('error', true);
        } else {
            $items['gd'][3] = $tmp['GD Version'];
        }
        unset($tmp);

        //磁盘空间检测
        if(function_exists('disk_free_space')) {
            $items['disk'][3] = SEK::byteFormat(disk_free_space(BASE_PATH));
        }
        return $items;
    }

}