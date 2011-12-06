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
 * Generic Error.
 *
 * @author Jovan Alleyne <me@jalleyne.ca>
 */

class HTTPRequestError extends HTTPErrorResponse {
	
	/**
	* Constructor
	*/
	function __construct($type=NULL,$message=NULL,$data=NULL){
		if( $type ) $this->type 		= $type;
		if( $message ) $this->message 	= $message;
		if( $data ) $this->data 	= $data;
		
		$this->httpStatusCode = 400;
		$this->httpStatusMessage = 'Bad Request';
	}
}