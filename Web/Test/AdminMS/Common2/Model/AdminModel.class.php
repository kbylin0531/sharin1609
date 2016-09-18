<?php
/**
 * Created by PhpStorm.
 * User: Lin
 * Date: 2015/12/19
 * Time: 20:50
 */
namespace Application\Admin\Common\Model;
use System\Core\Model;
use System\Util\SEK;

class AdminModel extends Model {

    /**
     * 获取顶部菜单项设置
     * @return array
     */
    public function getTopBarMenuConfig(){
        return [
            'menus' => [
                //Home模块
                [
                    'index'     => 1,
                    'name'      => 'User',
                    'url'       => $this->buildUrl('admin/member/auth/index',1,1),
                ],
                //About单个菜单
                [
                    'index'     => 2,
                    'name'      => 'System',
                    'url'       => '#'
                ],
                //复合菜单一
                [
                    'index'    => 3,
                    'name'  => 'Contact',
                    'type'  => 1,
                    'menus'     => [
                        [
                            'index'    => 3,
                            'name'  => 'Action',
                            'url'       => '#'
                        ],
                        [
                            'index'    => 3,
                            'name'  => 'Another',
                            'url'       => '#'
                        ],
                        [
                            'index'    => 3,
                            'name'  => 'Something',
                            'menus'     => [
                                [
                                    'index'    => 3,
                                    'name'  => 'Look',
                                    'url'       => '#'
                                ],
                                [
                                    'index'    => 3,
                                    'name'  => 'Nice',
                                    'url'       => '#'
                                ],
                            ],
                        ],
                    ],
                ],
                //复合菜单二
                [
                    'index'    => 4,
                    'name'  => 'Knowledge',
                    'type'  => 2,
                    'menus'     => [
                        [
                            [
                                'index'     => 4,
                                'name'      => 'Look',
                                'url'       => '#',
                            ],
                            [
                                'index'     => 4,
                                'name'      => 'Nice',
                                'url'       => '#'
                            ],
                        ],
                        [
                            [
                                'index'     => 4,
                                'name'      => 'Look',
                                'icon'      => 'icon-gear', // 设置了图标之后无法使url生效
                            ],
                            [
                                'index'     => 4,
                                'name'      => 'Nice',
                                'url'       => '#'
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * 建立URL
     * @param string $url url
     * @param int $tai current topbar index
     * @param int $sai default sidebar index
     * @return string
     */
    protected function buildUrl($url,$tai,$sai){
        return SEK::url($url,['_tai'=>$tai,'_sai'=>$sai]);
    }

}