<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/14/16
 * Time: 4:09 PM
 */

namespace App\Test\Controller;


use Sharin\Core\URLer;

class Index {

    public function index(){
        echo md5(sha1('123456'));
    }



    protected function testURLCreater(){
        $url = '';

        $url .= URLer::create(array('admin','UserManagement'),'UserList','ulist01',['param01'=>'value01']);

        echo "<a href='{$url}' target='_blank'>{$url}</a><br />";
        echo __METHOD__;
    }

    /**
     * 测试URL常规解析，实际测试地址:
     * index.php?_m=admin/user_management&_c=user_list&_a=ulist01&param01=value01
     * index.php/admin/user_management/user_list/ulist01/co/param02/value02
     * index.php?_pathinfo=/admin/user_management/user_list/ulist01/co/param02/value02
     *
     */
    protected function testURLParse(){
        $rst1 = URLer::parse('_m=admin/user_management&_c=user_list&_a=ulist01&param01=value01');
        $rst2 = URLer::parse('/admin/user_management/user_list/ulist01/co/param02/value02');
        $rst3 = URLer::parse('_pathinfo=/admin/user_management/user_list/ulist01/co/param02/value02');
        \Sharin\dump($rst1,$rst2,$rst3);
    }


}