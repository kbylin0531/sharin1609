<?php
/**
 * Created by PhpStorm.
 * User: Lin
 * Date: 2015/10/17 0017
 * Time: 15:53
 */
namespace Application\Admin\Common\Model;
use System\Core\Model;
use System\Util\SEK;

class AdminModel extends Model {


    public function getCurrentUserInfo(){
        return array('username' => 'Administrator');
    }

    public function getSidebarList(){
        return array(
            //type = 0 表示单个无下拉
            array(
                'type'  => 0,
                'icon'  => 'icon-home',
                'title' => '首页',
                'href'  => SEK::url('admin/main/index/index',array('active'=>0)),//参数二表示要激活的参数项
            ),
            array(
                'type'  => 1,
                'icon'  => 'icon-user',
                'title' => '用户管理',
                'items' => array(
                    array(
                        'href'  => SEK::url('admin/main/index/index',array('active'=>1)),
                        'title' => '表单1',
                    ),
                    array(
                        'href'  => SEK::url('admin/member/register/registerMemberForAdmin',array('active'=>1)),
                        'title' => '添加注册',
                    ),
                ),
            ),
            array(
                'type'  => 1,
                'icon'  => 'icon-user',
                'title' => 'XXXX',
                'items' => array(
                    array(
                        'href'  => SEK::url('admin/main/index/index',array('active'=>2)),
                        'title' => '表单1',
                    ),
                    array(
                        'href'  => SEK::url('admin/main/index/index',array('active'=>2)),
                        'title' => '表单2',
                    ),
                ),
            ),
        );
    }

    public function getFooterInfo(){
        return array(
            'copyright' => '2013 &copy; MatAdmin.',
        );
    }

    public function getProcessbarInfo(){
        return array(
            0   => array(
                array(
                    'name'  => '通话时长',
                    'percent'   => 71,
                    'used'  => 213,
                    'total' => 300,
                    'unit'  => '分钟',
                ),
                array(
                    'name'  => '手机流量',
                    'percent'   => 62,
                    'used'  => 621,
                    'total' => 1024,
                    'unit'  => 'MB',
                ),
            ),
            1   => array(
                array(
                    'name'  => '短信剩余',
                    'percent'   => 15,
                    'used'  => 30,
                    'total' => 200,
                    'unit'  => '条',
                ),
            ),
        );
    }

}