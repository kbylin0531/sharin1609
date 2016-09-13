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
 * @property
 * @property int $pid
 * @property string $name
 * @property string $image
 * @property string $url
 * @property string $atime
 * @property string $platform
 * @property string $type
 * @property string $uname
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

    /**
     * @param string $dsn
     * @return ProductModel
     */
    public static function getInstance($dsn = ''){
        return parent::getInstance($dsn);
    }
}