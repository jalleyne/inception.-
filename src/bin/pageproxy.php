<?php
/**
 * Copyright 2011 Jovan Alleyne <me@jalleyne.ca>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */


/**
 * Script to determine what file to load based on uri
 *
 * @author Jovan Alleyne <me@jalleyne.ca>
 */


/* */
if( !defined('CONTENT_ROOT') )
	require_once $_SERVER['DOCUMENT_ROOT'].'/settings.php';
	
	
/* */
if( isset($_REQUEST['r']) )
$request_uri = urldecode($_REQUEST['r']);
else $request_uri = $_SERVER['REQUEST_URI'];
 

$request = str_replace('#!','',$request_uri);
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
	echo CONTENT_ROOT;
	//echo $content;
	//header('HTTP/1.0 404 Not Found');
	//include '404.php';
}


?>
