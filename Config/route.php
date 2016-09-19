<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/14/16
 * Time: 9:30 AM
 */
return [
    'DRIVER_DEFAULT_INDEX' => 0,//默认驱动ID，类型限定为int或者string
    'DRIVER_CLASS_LIST' => [
        'Sharin\\Core\\Router\\LiteRouter',
        'Explorer\\ExplorerRouter',
    ],//驱动类的列表
    'DRIVER_CONFIG_LIST' => [
        [
            //@see LiteRouter's config
        ]
    ],//驱动类列表参数
];