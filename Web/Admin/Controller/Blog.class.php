<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/25/16
 * Time: 2:09 PM
 */

namespace App\Admin\Controller;

class Blog extends Admin {

    public function detail(){
        $this->display();
    }

    public function lists(){
        $this->display();
    }

}