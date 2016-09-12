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

    public function inc($username){
        $row = $this->query("select total from member where username = '{$username}';")->fetch();
        if(!$row) return false;
        return $this->update($username,[
            'total' => $row['total'] + 1,
        ]);
    }

}