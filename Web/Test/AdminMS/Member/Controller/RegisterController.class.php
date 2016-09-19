<?php
/**
 * Created by PhpStorm.
 * User: Lin
 * Date: 2015/10/17 0017
 * Time: 20:00
 */
namespace Application\Admin\Member\Controller;
use Application\Admin\Common\Controller\AdminController;
use Application\Admin\Member\Model\MemberModel;

class RegisterController extends AdminController{


    public function showMemberList(){
        $memberModel = new MemberModel();

    }

    /**
     * 管理界面添加成员
     * 无需审核
     */
    public function registerMemberForAdmin(){
        $this->assign('dft_nickname','mist_'.time().'_'.mt_rand(0,1000));
        $this->assignGeneralData();
        $this->display();
    }

    /**
     * 外部用户注册添加
     * 可以设置是否审核
     */
    public function registerMember(){
        $this->display();
    }

    public function createMember(){

    }

}