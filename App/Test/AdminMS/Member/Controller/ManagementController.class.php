<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/19
 * Time: 12:50
 */
namespace Application\Admin\Member\Controller;
use Application\Admin\Common\Controller\AdminController;

/**
 * Class ManagementController 用户管理控制器
 * @package Application\Admin\Member\Controller
 */
class ManagementController extends AdminController {


    public function index(){
        $this->display();
    }



}