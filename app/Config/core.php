<?php

session_start();
$_SESSION['app_start']=true;
session_write_close();

define("DS", DIRECTORY_SEPARATOR);
define("APP", dirname(dirname(__FILE__)) . DS);

require_once(APP . "Lib" . DS . "ClassLoader.php");
$classLoader = new Lib\ClassLoader();
$classLoader->addPrefix('', APP);
$classLoader->register();

require_once(APP . "Config" . DS . "config.php");
require_once(APP . "Config" . DS . "routes.php");

use Controller\AppController;
use Lib\RouterLoader;

if (USE_ROUTER) {
    RouterLoader::register();
}