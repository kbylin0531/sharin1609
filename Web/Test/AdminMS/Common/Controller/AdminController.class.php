<?php
/**
 * Created by PhpStorm.
 * User: Lin
 * Date: 2015/10/17 0017
 * Time: 20:32
 */
namespace Application\Admin\Common\Controller;
use Application\Admin\Common\Model\AdminModel;
use System\Core\Controller;
use System\Util\SEK;

class AdminController extends Controller {
    /**
     * @var AdminModel
     */
    private $model = null;


    protected function assignGeneralData($active=null){
        if(!isset($active)){
            $active = isset($_GET['active']) ? $_GET['active'] : 0;
        }
        $this->assignUserNav();
        $this->assignSideBar($active);
        $this->assignProgressBars();
        $this->assignFooter();
    }


    private function initModel(){
        if(null === $this->model){
            $this->model = new AdminModel();
        }
    }

    /**
     * 用户信息栏
     */
    private function assignUserNav(){
        $this->initModel();
        $info = $this->model->getCurrentUserInfo();
        $this->assign('userinfo',$info);
    }

    /**
     * 菜单项
     * @param int $active 激活顺序
     */
    private function assignSideBar($active=0){
        $this->initModel();
        $modules = $this->model->getSidebarList();
        $this->assign('active',$active);//被激活的选项
        $this->assign('modules',$modules);
    }

    /**
     * 足部版权 说明
     */
    private function assignFooter(){
        $this->initModel();
        $info = $this->model->getFooterInfo();
        $this->assign('footinfo',$info);
    }

    /**
     * 侧边栏快速信息统计
     */
    private function assignProgressBars(){
        $this->initModel();
        $datalist = $this->model->getProcessbarInfo();
        $this->assign('process_bar',$datalist);
    }

}