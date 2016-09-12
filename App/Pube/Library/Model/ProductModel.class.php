<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/12/16
 * Time: 3:23 PM
 */

namespace Library\Model;


use Library\Utils\SQLite;

/**
 * Class Product
 *
 * pid,name,image,url,atime,platform,type
 *
 * @package Library\Model
 */
class ProductModel extends SQLite {

    protected $tablename = 'product';
    protected $pk = 'pid';
    protected $fields = [
        'pid'  => '',
        'name'  => '',
        'image'  => '',
        'url'  => '',
        'atime'  => '',//addtime
        'platform'  => '',
        'type'  => 1,
        'uname' => '',//username
    ];

}