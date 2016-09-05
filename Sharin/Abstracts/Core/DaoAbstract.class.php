<?php
/**
 * Powered by linzhv@qq.com.
 * Github: git@github.com:linzongho/sharin.git
 * User: root
 * Date: 16-9-3
 * Time: 下午6:52
 */

namespace Sharin\Abstracts\Core;
use PDO;
use Sharin\Exceptions\Database\ConnectFailedException;
use PDOException;

/**
 * Class DaoAbstract Dao
 * 实现的差异：
 *  ① MySQL的group by在字段未加入聚合函数时会取多条数据的第一条，而SQL Server会提示错误并终止执行
 *  ② mysql中是 ``, sqlserver中是 [], oracle中是 ""
 *
 * @package Kbylin\System\Core\Dao
 */
abstract class DaoAbstract extends PDO {

    /**
     * 创建驱动类对象
     * DatabaseDriver constructor.
     * @param array $config
     * DaoDriver constructor.
     * @param array $config
     * @throws ConnectFailedException
     */
    public function __construct(array $config){
        $dsn = is_string($config['dsn'])?$config['dsn']:$this->buildDSN($config);
        try {
            parent::__construct($dsn,$config['username'],$config['password'],$config['options']);
        } catch(PDOException $e){
            throw new ConnectFailedException($dsn,$config,$e->getMessage());
        }
    }

    /**
     * 将关键字字段转义
     * @param string $field 字段名称
     * @return string
     */
    abstract public function escape($field);

    /**
     * 取得数据表的字段信息
     * @access public
     * @param string $tableName 数据表名称
     * @return array
     */
    abstract public function getFields($tableName);

    /**
     * 取得数据库的表信息
     * @access public
     * @param string $dbName
     * @return array
     */
    abstract public function getTables($dbName=null);

    /**
     * 字段和表名处理(关机那字处理)
     * @access protected
     * @param string $key
     * @return string
     */
    abstract protected function parseKey(&$key);

    /**
     * 根据配置创建DSN
     * @param array $config 数据库连接配置
     * @return string
     */
    abstract public function buildDSN(array $config);

    /**
     * 编译组件成适应当前数据库的SQL字符串
     * @param array $components  复杂SQL的组成部分
     * @param int $actiontype 操作类型
     * @return string
     */
    abstract public function compile(array $components,$actiontype);

}