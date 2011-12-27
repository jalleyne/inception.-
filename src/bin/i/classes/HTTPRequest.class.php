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


	public $base_uri;

	/**
	*
	*/
    function __construct() {
    	
    }
    
	/**
	*
	*/
    function get($url,$response_format="json"){
    	
		session_write_close();
		
		$ch = curl_init();
		
		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL,isset($this->base_uri)?$this->base_uri.$url:$url);
		
		if( isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS'] )
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
	
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,TRUE);
		
		if( isset($_COOKIE['PHPSESSID']) )
			curl_setopt($ch,CURLOPT_COOKIE,'PHPSESSID=' . $_COOKIE['PHPSESSID']);
		
		curl_setopt($ch,CURLOPT_COOKIESESSION,TRUE);
	 	curl_setopt($ch,CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
		
		//execute post
		$result = curl_exec($ch);
		
		//close connection
		curl_close($ch);
		
		@session_start();
		/* */
		switch($response_format){
			case 'json':
				$result = json_decode($result,TRUE);
				break;
		}
		
		/* */
		return $result;
    }
	
	/**
	*
	*/
    function post($url,$data=NULL,$response_format="json",$content_type="application/x-www-form-urlencoded"){
		/* */
		session_write_close();
		/* */
		$ch = curl_init();
		
		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL,isset($this->base_uri)?$this->base_uri.$url:$url);
		
		/* */
		if( count($data) ){
			
			curl_setopt($ch,CURLOPT_POST,TRUE);

			switch($content_type){
				case 'multipart':
				case 'multipart/form-data':
					/* */
					$mp_boundary = generate_multipart_boundary();
					/* */
					$data = build_multipart_body($data,$mp_boundary);
					/* */
					curl_setopt($ch,CURLOPT_HTTPHEADER, array(
												"Content-Type: multipart/form-data; boundary=$mp_boundary",
												"Content-Legnth: ".strlen($data).";")
										);
					curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
					break;
				
				default:
					curl_setopt($ch,CURLOPT_HTTPHEADER,array(
												"Content-type: application/x-www-form-urlencoded")
											);
					curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($data));
					break;
			}
			
		}
		/* */
		if( isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']=='on' )
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
	
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,TRUE); 
	 	curl_setopt($ch,CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
	
		if( isset($_COOKIE['PHPSESSID']) )
			curl_setopt($ch,CURLOPT_COOKIE,'PHPSESSID=' . $_COOKIE['PHPSESSID']);
		
		curl_setopt($ch,CURLOPT_COOKIESESSION,TRUE);
		
		//execute post
		$result = curl_exec($ch);
		
		//close connection
		curl_close($ch);
		
		@session_start();

		/* */
		switch($response_format){
			case 'json':
				$result = json_decode($result,TRUE);
				break;
		}
		
		/* */
		return $result;
    }
	
}