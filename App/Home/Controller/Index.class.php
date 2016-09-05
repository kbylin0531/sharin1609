<?php
/**
 * Email: linzongho@gmail.com
 * Github: https://github.com/linzongho/Pindex
 * User: asus
 * Date: 8/22/16
 * Time: 1:47 PM
 */

namespace App\Home\Controller;

/**
 * Class A
 * @_method mixed index()
 * @package App\Home\Controller
 */
class A {

    protected static $attr = 'AA';

    function __call($name, $arguments)
    {
        echo $name;
    }

    public static function index(){
        echo 'static';
    }

    /**
     * @return string
     */
    public static function getAttr()
    {
        return static::$attr;
    }

    /**
     * @param string $attr
     */
    public static function setAttr($attr)
    {
        static::$attr = $attr;
    }

}


class B extends A{
}

class C extends A {

}

class Index {

    public function index(){
        $a = new A();

        //static
        $a->index();
        A::index();

        return 5;
    }

}