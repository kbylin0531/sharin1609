<?php

/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/25/16
 * Time: 5:36 PM
 */
namespace App\Admin\Controller;
use App\Admin\Model\MemberModel;
use App\Admin\Model\WebsiteModel;
use Sharin\Core\Controller;
use Sharin\Core\SEK;
use Sharin\Extension\Loginout;

abstract class Admin extends Controller {
    /**
     * @var MemberModel
     */
    protected static $memberModel = null;
    /**
     * IndexController constructor.
     */
    public function __construct(){
        self::$memberModel or self::$memberModel = new MemberModel();
        $status = Loginout::check();
//        \Pindex\println($status);
        if(!$status){
            $this->redirect('/Admin/Publics/login');
        }
        define('REQUEST_PATH','/'.SR_REQUEST_MODULE.'/'.SR_REQUEST_CONTROLLER.'/'.SR_REQUEST_ACTION);
    }

    /**
     * @param string|null $template
     */
    protected function show($template=null){
        $this->assign('userinfo',Loginout::getUserinfo());
        $model = new WebsiteModel();
        //is different by website
        $webinfo = $model->lists(true);
        $menu_list = $model->getSideMenu(true);
        $user_menu_list = $model->getUserMenu();
        $webinfo['menu_list'] = $menu_list;
        $webinfo['user_menu'] = $user_menu_list;
        $this->assign('website',$webinfo);
        //is different by page
        $this->assign('page',[
            'active_id' => 3,
            'title'         => 'This is an heading title',
            'breadcrumb'    => [
                [
                    'title' => '222',
                    'url'   => '#',
                ],
                [
                    'title' => '444',
                    'url'   => '#',
                ],
            ],
        ]);

        null === $template and $template = SEK::backtrace(SEK::ELEMENT_FUNCTION,SEK::PLACE_FORWARD);
        $this->display($template /* substr($template,4) 第五个字符开始 */);
    }

}