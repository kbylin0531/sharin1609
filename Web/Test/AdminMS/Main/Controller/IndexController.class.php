<?php
/**
 * Created by PhpStorm.
 * User: Lin
 * Date: 2015/10/16 0016
 * Time: 18:51
 */
namespace Application\Admin\Main\Controller;
use Application\Admin\Common\Controller\AdminController;

class IndexController extends AdminController {

    /**
     * 主体显示
     */
    public function index(){
        $this->assignGeneralData();
        $this->display();
    }

}