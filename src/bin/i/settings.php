<?php
/*
 * Created on Sep 8, 2011
 * 
 */


/* Load global settings */
require_once $_SERVER['DOCUMENT_ROOT']."/settings.php";

/* Load global functions */
require_once $_SERVER['DOCUMENT_ROOT'].'/php/functions.inc.php';
require_once 'functions.inc.php';



/* Email constants */
define('NOREPLY_EMAIL',						'no-reply@domain.com');
define('CONATCT_REQUEST_RECEPIENT_NAME',	'Consumer Representative');
define('CONATCT_REQUEST_RECEPIENT_EMAIL', 	'consumer-response@domain.com');



/* Paths to load into include path settings */
$lib_paths = array();
$lib_paths[] = $_SERVER['DOCUMENT_ROOT'].'/i/classes';
$lib_paths[] = $_SERVER['DOCUMENT_ROOT'].'/i/handlers';
$lib_paths[] = $_SERVER['DOCUMENT_ROOT'].'/i';


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



/* If database constants defined, instanciate $db object */
if( defined('DB_HOST') && defined('DB_NAME') ){
	$db = new DBO();
}