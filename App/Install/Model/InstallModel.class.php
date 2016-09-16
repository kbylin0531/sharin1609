<?php
/**
 * Created by PhpStorm.
 * User: Lin
 * Date: 2015/9/16
 * Time: 17:46
 */
namespace Application\Installer\Model;
use System\Core\Model;

class InstallModel extends Model{

    /**
     * 构造
     * @param string|array $config 数据库连接标识符 或者 连接信息数组
     * @param bool $withdb false时删除配置中的数据库名称信息，因为此时数据库还不存在
     * @throws \Exception
     */
    public function __construct($config='0',$withdb=true){
//        isset($config) and unset($config['dbname']);/不行，因为unset是操作符，同throw
        if(!$withdb and isset($config['dbname'])){
            unset($config['dbname']);//创建数据库时不能带有数据库名称，否则会尝试连接这个不存在的数据库而导致错误的发生
        }
        isset($config) and $this->init($config);
        parent::__construct();
    }

    /**
     * 创建数据库
     * @param string $dbname 数据库名称
     * @return bool
     */
    public function createDatabase($dbname){
        $rst = $this->dao->createDatabase($dbname);
        return $rst?true:false;
    }

    /**
     * 执行创建数据库和插入记录的操作
     * @param string $sql 执行的SQL语句
     * @return array|false|int
     */
    public function execSql($sql){
        if(strtoupper(substr($sql, 0, 12)) == 'CREATE TABLE') {
            $name = preg_replace('/^CREATE TABLE `(\w+)` .*/s', '\1', $sql);
            $msg  = "正在创建数据表'{$name}'";
            $rst = $this->dao->exec($sql);
            if(false !== $rst){
                return array(true, "{$msg}...成功,影响结果为{$rst}！");
            }else{
                return array(false,"{$msg}...失败,影响结果为{$rst}！");
            }
        }
        return $this->dao->exec($sql);
    }



}