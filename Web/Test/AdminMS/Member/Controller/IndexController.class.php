<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/20
 * Time: 9:58
 */
namespace Application\Admin\Member\Controller;
use Application\Admin\Common\Controller\AdminController;

class IndexController extends AdminController {

    public function __construct(){
        parent::__construct();
        //侧边栏设置
        $this->assignSideBar([
            'menus' => [
                [
                    'icon'  => 'icon-home',
                    'name'  => 'Member Group',
                    'submenus'  => [
                        [
                            //末端需要进行index编号
                            'index' => 1,
                            'name'  => 'tabletest',
                            'url'   => '#',
                            'meta'   => 'New',
                        ],
                        [
                            'index' => 2,
                            'name'  => 'name2',
                            'url'   => '#',
                            'meta'   => '',
                        ],
                    ],
                ],
                [
                    'icon'  => 'icon-smile',
                    'name'  => 'Elements',
                    'submenus'  => [
                        [
                            'index' => 3,
                            'name'  => 'name1',
                            'url'   => '#',
                            'meta'   => 'New',
                        ],
                        [
                            'index' => 4,
                            'name'  => 'name2',
                            'url'   => '#',
                            'meta'   => '',
                        ],
                    ],
                ],
            ],
        ]);
    }

}
