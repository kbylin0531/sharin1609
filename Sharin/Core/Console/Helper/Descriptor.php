<?php
// +----------------------------------------------------------------------
// | TopThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.topthink.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhangyajun <448901948@qq.com>
// +----------------------------------------------------------------------

namespace think\console\helper;

use Sharin\Core\Console\Output;

class Descriptor extends Helper
{

    /**
     * @var Descriptor
     */
    private $descriptor;

    /**
     * 构造方法
     */
    public function __construct()
    {
        $this->descriptor = new Descriptor();
    }

    /**
     * 描述
     * @param Output $output
     * @param object $object
     * @param array  $options
     * @throws \InvalidArgumentException
     */
    public function describe(Output $output, $object, array $options = [])
    {
        $options = array_merge([
            'raw_text' => false
        ], $options);

        $this->descriptor->describe($output, $object, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'descriptor';
    }
}
