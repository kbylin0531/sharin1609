<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/12/16
 * Time: 1:59 PM
 */

namespace Library\Model;

use Library\Utils\SQLite;

/**
 * Class Member
 *
 * @property string $username
 * @property string $passwd
 * @property string $email
 * @property string $phone
 * @property string $cateid
 * @property string $total
 * @property string $platform
 *
 * @package Library\Model
 */
class MemberModel extends SQLite {
    protected $tablename = 'member';
    protected $pk = 'username';
    protected $fields = [
        'username'  => '',
        'passwd'  => '',
        'email'  => '',
        'phone'  => '',
        'cateid'  => '',
        'total'  => '0',
        'platform'  => '',
    ];

    /**
     * @param string $dsn
     * @return MemberModel
     */
    public static function getInstance($dsn = ''){
        return parent::getInstance($dsn);
    }

    public function getAvailableAccount(){
        $list = $this->select('total < 15 and platform = \'ec21\'');
        if(count($list)){
            return array_shift($list);
        }else{
            return false;
        }
    }

    public function inc($username){
        $row = $this->query("select total from member where username = '{$username}';")->fetch();
        if(!$row) return false;
        return $this->update($username,[
            'total' => $row['total'] + 1,
        ]);
    }

}