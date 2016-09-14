<?php
/**
 * Created by PhpStorm.
 * User: lich4ung
 * Date: 9/14/16
 * Time: 1:31 PM
 */


define('SR_PATH_BASE',dirname(__DIR__).'/');
include SR_PATH_BASE.'Config/vendor/phpliteadmin.php';

chdir(dirname(__DIR__).'/Sharin/Vendor/phpliteadmin/');
include 'phpliteadmin.php';