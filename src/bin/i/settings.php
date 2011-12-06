<?php
/*
 * Created on Sep 8, 2011
 * 
 */

/* Load global settings */
require_once $_SERVER['DOCUMENT_ROOT']."/settings.php";



/* Email constants */
define('NOREPLY_EMAIL',						'no-reply@domain.com');
define('CONATCT_REQUEST_RECEPIENT_NAME',	'Consumer Representative');
define('CONATCT_REQUEST_RECEPIENT_EMAIL', 	'consumer-response@domain.com');



/* Paths to load into include path settings */
$lib_paths = array();
$lib_paths[] = getcwd().'/classes';
$lib_paths[] = getcwd().'/handlers';
$lib_paths[] = getcwd();


/* */
define('ROUTE_FILE_PATH', 	$_SERVER['DOCUMENT_ROOT'].'/'.basename(dirname(__FILE__)).'/routes.xml');
define('WS_URI',			BASE_URI.basename(dirname(__FILE__)));


/* */
$path  = implode(PATH_SEPARATOR,$lib_paths);
set_include_path( get_include_path() . PATH_SEPARATOR . $path );


/* */
function __autoload($class_name) {
    include $class_name . '.class.php';
}

