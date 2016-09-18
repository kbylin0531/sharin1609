<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/25/16
 * Time: 9:52 PM
 */

namespace App\Admin\Controller;

/**
 * Class Website manage the website
 * @package App\Admin\Controller
 */
class Website extends Admin {

    public function info(){
        $this->show();
    }

    public function menu(){
        $this->display();
    }

}