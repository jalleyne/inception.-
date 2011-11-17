<?php
/*
 * Created on Sep 8, 2011
 * 
 */

ini_set('display_errors',TRUE);


/* Load global settings */
require_once $_SERVER['DOCUMENT_ROOT']."/settings.php";


/* Paths to load into include path settings */
$lib_paths = array();
$lib_paths[] = getcwd().'/classes';
$lib_paths[] = getcwd().'/handlers';
$lib_paths[] = getcwd();


/* */
define('ROUTE_FILE_PATH', 	$_SERVER['DOCUMENT_ROOT'].'/'.basename(dirname(__FILE__)).'/routes.xml');
define('BASE_DIR', 			'/');
define('BASE_URI',			(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']?'https':'http').'://'.$_SERVER['HTTP_HOST'].BASE_DIR);
define('WS_URI',			BASE_URI.basename(dirname(__FILE__)));


/* */
$path  = implode(PATH_SEPARATOR,$lib_paths);
set_include_path( get_include_path() . PATH_SEPARATOR . $path );


/* */
function __autoload($class_name) {
    include $class_name . '.class.php';
}

