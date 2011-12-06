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
require_once $_SERVER['DOCUMENT_ROOT'].'/php/functions.inc.php';
	
	
/* */
if( isset($_REQUEST['r']) )
$request_uri = urldecode($_REQUEST['r']);
else $request_uri = $_SERVER['REQUEST_URI'];
 
/* Remove query string from uri to match file */
$request_uri = str_replace(
					'?'.$_SERVER['QUERY_STRING'],
					'',
					$request_uri
				);

/* strip hash and split url into parts*/
$request = str_replace('#!','',$request_uri);
$request_parts = explode('/',$request);



/* loop over uri parts to match to path on file system*/
while(count($request_parts)){
	array_pop($request_parts);
	$path = CONTENT_ROOT.implode('/',$request_parts);
	
	if( is_file($path.'/index.php') ){
		$content = '/index.php';
		break;
	}
	else if( is_file($path.'.php') ){
		$content = '.php';
		break;
	}
}

/* */
if( !(count($request_parts)===1 && 
	$content=='/index.php' && $request != '/') && 
	file_exists($path.$content) ){
	/* */
	$base_dir = pathinfo($path.$content);
	chdir($base_dir['dirname']);
	
	/* */
	ob_start();
	include $path.$content;
	
	if( isset($enforce_login) && $enforce_login ){
		
		if( isset($_REQUEST['r']) ){
			header('HTTP/1.1 403 Forbidden');
			ob_end_clean();
		}
		else {
			ob_end_clean();
			header("Location: $login_redirect");
		}
		
		exit();
	}
	
	ob_end_flush();
}
else {
	header('HTTP/1.0 404 Not Found');
	include CONTENT_ROOT.'/404.php';
}