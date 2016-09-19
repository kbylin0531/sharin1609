<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/18/16
 * Time: 12:03 PM
 */

namespace Library;


class MemberInfoProvider {

    protected $uid = null;
    private $guid = '';

    public function __construct($uid){
        $this->uid = $uid;
    }




}