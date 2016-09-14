<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/14/16
 * Time: 4:09 PM
 */

namespace App\Test\Controller;


class Index {

    public function index(){
        echo md5(sha1('123456'));
    }

}