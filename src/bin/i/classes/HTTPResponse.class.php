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
 * HTTP Response for an InceptionWebServiceApplication instance
 *
 * @author Jovan Alleyne <me@jalleyne.ca>
 */
class HTTPResponse {

	/**
	* Response Http Status Code
	*/
	public $httpStatusCode = 200;
	
	/**
	* Response Http Status Message
	*/
	public $httpStatusMessage = 'Ok';

	/**
	* Response format
	*/
	public $format = 'json';
	
	/**
	* Response body
	*/
	public $data;

    function __construct() {
    }
    
    
    public function send($data=NULL){
		/* */
		if( $data ) $this->data = $data;
		/* */
    	header("Content-Type: text/javascript; charset=UTF-8");
		header('HTTP/1.0 '.$this->httpStatusCode.' '.$this->httpStatusMessage);
		exit($this);
    }

	public function redirect($location,$data=NULL){
		if( $data ) 
			header("Location: $location&data=".(string)$this);
		else 
			header("Location: $location");
		/* */
		exit();
	}
	
	
	public function __toString(){
		
		$data = $this->data;
		
		if( !$data || !is_array($data) ){
			$data = array('success' => TRUE);
		}
		
		return json_pretty_print(
				json_encode(
					array_merge($data,
						array(
						'date'		=> time()
						))
					)
				);
	}
	
}