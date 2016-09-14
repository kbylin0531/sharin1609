<?php
/**
 * Email: linzongho@gmail.com
 * Github: https://github.com/linzongho/Pindex
 * User: asus
 * Date: 8/23/16
 * Time: 1:21 PM
 */
return [
    'DRIVER_DEFAULT_INDEX' => 1,
    'DRIVER_CLASS_LIST' => [
        'Sharin\\Core\\View\\Smarty',
        'Sharin\\Core\\View\\Think',
    ],
    'DRIVER_CONFIG_LIST' => [
        [
            'SMARTY_DIR'        => SR_PATH_BASE.'/Vendor/smarty3/libs/',
            'TEMPLATE_CACHE_DIR'    => SR_PATH_RUNTIME.'/View/',
            'SMARTY_CONF'       => [
                //模板变量分割符号
                'left_delimiter'    => '{',
                'right_delimiter'   => '}',
                //缓存开启和缓存时间
                'caching'        => true,
                'cache_lifetime'  => 15,
            ],
        ],
        [
            'CACHE_ON'         => true,//缓存是否开启
            'CACHE_EXPIRE'     => 10,//缓存时间，0便是永久缓存,仅以设置为30
            'CACHE_UPDATE_CHECK'=> true,//是否检查模板文件是否发生了修改，如果发生修改将更新缓存文件（实现：检测模板文件的时间是否大于缓存文件的修改时间）

            'CACHE_PATH'       =>   SR_PATH_RUNTIME.'/View/',
            'TEMPLATE_SUFFIX'  =>  '.html',     // 默认模板文件后缀
            'CACHFILE_SUFFIX'  =>  '.php',      // 默认模板缓存后缀
            'TAGLIB_BEGIN'     =>  '<',  // 标签库标签开始标记
            'TAGLIB_END'       =>  '>',  // 标签库标签结束标记
            'L_DELIM'          =>  '{',            // 模板引擎普通标签开始标记
            'R_DELIM'          =>  '}',            // 模板引擎普通标签结束标记
            'DENY_PHP'         =>  false, // 默认模板引擎是否禁用PHP原生代码
            'DENY_FUNC_LIST'   =>  'echo,exit',    // 模板引擎禁用函数
            'VAR_IDENTIFY'     =>  'array',     // 模板变量识别。留空自动判断,参数为'obj'则表示对象

            'TMPL_PARSE_STRING'=> [],//用户自定义的字符替换
        ],
    ],

    //模板文件提示错误信息模板
    'TEMPLATE_EMPTY_PATH'   => 'notpl',
];