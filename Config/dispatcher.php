<?php
return [
    'DRIVER_DEFAULT_INDEX' => 0,//默认驱动ID，类型限定为int或者string
    //驱动类的列表
    'DRIVER_CLASS_LIST' => [
        'Sharin\\Core\\Dispatcher\\LiteDispatcher',
        'Explorer\\DispatchHandler',
    ],
    'DRIVER_CONFIG_LIST'  => [
        [
            //空缺时默认补上,Done!
            'INDEX_MODULE'      => 'Pube',
            'INDEX_CONTROLLER'  => 'Index',
            'INDEX_ACTION'      => 'index',
        ],
    ],
];