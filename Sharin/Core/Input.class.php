<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 9/16/16
 * Time: 4:18 PM
 */

namespace Sharin\Core;


class Input {

    private static $_convention = array(
        /**
         * By default CodeIgniter enables access to the $_GET array.  If for some
         * reason you would like to disable it, set 'allow_get_array' to FALSE.
         */
        'allow_get_array'   =>  true,
        /**
         * Determines whether the XSS filter is always active when GET, POST or
         * COOKIE data is encountered
         */
        'enable_xss'        =>  false,
        /**
         * Enables a CSRF cookie token to be set. When set to TRUE, token will be
         * checked on a submitted form. If you are accepting user data, it is strongly
         * recommended CSRF protection be enabled.
         */
        'enable_csrf'       =>  false,

    );

    private static $headers = array();

    /**
     * Is ajax Request?
     * Test to see if a request contains the HTTP_X_REQUESTED_WITH header
     * @return bool
     */
    public static function isAjax(){
        return ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') );
    }

    /**
     * Is cli Request?
     * Test to see if a request was made from the command line
     * @return bool
     */
    public static function isClient(){
        return (php_sapi_name() === 'cli' OR defined('STDIN'));
    }

    public static function getRequestHeaders($xss_clean = false){
        if (function_exists('apache_request_headers')){
            $headers = apache_request_headers();
        }else{
            $headers['Content-Type'] = (isset($_SERVER['CONTENT_TYPE'])) ? $_SERVER['CONTENT_TYPE'] : @getenv('CONTENT_TYPE');
            foreach ($_SERVER as $key => $val){
                if (strncmp($key, 'HTTP_', 5) === 0){
//                    $headers[substr($key, 5)] = $this->_fetch_from_array($_SERVER, $key, $xss_clean);
                }
            }
        }
        // take SOME_HEADER and turn it into Some-Header
        foreach ($headers as $key => $val){
            $key = str_replace('_', ' ', strtolower($key));
            $key = str_replace(' ', '-', ucwords($key));

            self::$headers[$key] = $val;
        }
        return self::$headers;
    }


}