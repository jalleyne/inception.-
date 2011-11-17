<?php
/*
 * @file: 	prp.php 
 * @author: jovan
 */


if( isset($_REQUEST['r']) )
$request_uri = $_REQUEST['r'];
else $request_uri = $_SERVER['REQUEST_URI'];
 

$request = str_replace('#!','',urldecode($request_uri));
$request_parts = explode('/',$request);


while(count($request_parts)){
	array_pop($request_parts);
	$path = implode('/',$request_parts);
	
	if( is_dir(CONTENT_ROOT.$path) )
		break;
}


switch( $request ){
	case '/':
	case '':
		$content = 'index';
		break;
	case $path:
		if ( is_file(CONTENT_ROOT.$path.'/index.php') ) 
			$content = $path.'index';
		break;
	default:

		$or = str_replace('/','',str_replace($path,'',$request));
		if( is_file(CONTENT_ROOT.$path.$or.'.php') )
			$content = $path.$or;
		else $content = str_replace('/','',$request);
		break;
}


chdir(CONTENT_ROOT);

if( file_exists($content.'.php') ){
	
	ob_start();
	include $content.'.php';
	
	if( isset($enforce_login) && $enforce_login ){
		header('HTTP/1.1 403 Forbidden');
		ob_end_clean();
		exit();
	}
	
	ob_end_flush();
}
else {
	header('HTTP/1.0 404 Not Found');
	include '404.php';
}


?>
