<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/19/16
 * Time: 9:40 AM
 */
namespace App\Pube\Controller;
use Sharin\Core\Controller;
use Sharin\Core\Dao;

class Setting extends Controller{

    public function index(){
        $dao = Dao::getInstance(1);
        $result = $dao->query('select t.cat_id,cn.cat_cn_name,t.cat_name,all_child_cat,t.alias
from nt_categories t left join nt_categories_cn as cn on cn.cid=t.cat_id
where all_child_cat = \'\' order by t.cat_name asc');

        $this->display();
    }


}