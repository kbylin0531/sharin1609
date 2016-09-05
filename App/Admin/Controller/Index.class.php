<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/25/16
 * Time: 9:52 PM
 */

namespace App\Admin\Controller;

class Index extends Admin{

    public function index(){
        $panels = [
            [
                'title' => 'Explorer',
                'action'=> SR_PUBLIC_URL.'/explorer.php?desktop',
                'icon'  => 'desktop',
                'color' => 'red',
            ],[
                'title' => 'Database',
                'action'=> SR_PUBLIC_URL.'/adminer.php',
                'icon'  => 'database',
                'color' => 'purple',
            ],[
                'title' => 'Wechat',
                'action'=> '#',
                'icon'  => 'weixin',
                'color' => 'blue',
            ],[
                'title' => 'Home',
                'action'=> 'http://www.baidu.com',
                'icon'  => 'home',
                'color' => 'green',
            ],
        ];
        $this->assign([
            'panels'        => json_encode($panels),
        ]);
        $this->show();
    }

}