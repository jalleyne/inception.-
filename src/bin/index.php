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
 * Bootstrap app for Inception.*. This file starts the app and handles URI
 * requests.
 *
 * @author Jovan Alleyne <me@jalleyne.ca>
 */



/**
 * NOTE: This is not your the place to put your html. Place all content inside your localized
 * folder. example /en-CA/index.php will replace what your would expect here as
 * your default page.
 */



/* Begin session */
if (!session_id()) {
	session_start();
}

/** 
 * Set language constant, first check the cookie 
 * then resort to default language set in .htaccess file
 */
if( !defined('LANGUAGE') )
	define('LANGUAGE', 		
				empty($_COOKIE['LANGUAGE'])?
				getenv('DEFAULT_LANGUAGE'):$_COOKIE['LANGUAGE']
			);

/* */
require_once $_SERVER['DOCUMENT_ROOT'].'/i/settings.php';



/*
 * 
 */
$api = new HTTPRequest();
$api->base_uri = WS_URI;


/**
 * Include page proxy file 
 * (handles content negotiation) 
 */
require_once "pageproxy.php";
