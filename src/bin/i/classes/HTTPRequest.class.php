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
 * Helper class for making HTTP requests
 *
 * @author Jovan Alleyne <me@jalleyne.ca>
 */
class HTTPRequest {

	/**
	* cURL handle
	*/
	protected $ch;

	/**
	*
	*/
    function __construct() {
    	
		$this->ch = curl_init();
    	
    }
    
	/**
	*
	*/
    function get($url){
    	
		session_write_close();
		
		//set the url, number of POST vars, POST data
		curl_setopt($this->ch,CURLOPT_URL,$url);
		
		if( isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS'] )
			curl_setopt($this->ch,CURLOPT_SSL_VERIFYHOST,2);
	
		curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,TRUE);
		
		if( isset($_COOKIE['PHPSESSID']) )
			curl_setopt($this->ch,CURLOPT_COOKIE,'PHPSESSID=' . $_COOKIE['PHPSESSID']);
		
		curl_setopt($this->ch,CURLOPT_COOKIESESSION,TRUE);
		
		//execute post
		$result = curl_exec($this->ch);
		
		//close connection
		curl_close($this->ch);
		
		session_start();
		
		return $result;
    }
    
}