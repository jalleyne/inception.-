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
* Captcha 
*/
require_once $_SERVER['DOCUMENT_ROOT'].'/php/libs/securimage/securimage.php';


/**
 * Route handler for Contact form requests.
 *
 * @author Jovan Alleyne <me@jalleyne.ca>
 */
class ContactRequestHandler {

    public function postResponder($request_data){
    	
	    $captcha = new Securimage();
		if( $captcha->check($request_data['captcha']) == TRUE ){
			
			/* */
			$subject = '';
			/* */
			$message = get_message_template( 
											$_SERVER['DOCUMENT_ROOT'].'/emails/contact.tpl.txt' 
										);
			/* */
			$vars = array(
						'[$NAME]',
						'[$EMAIL]',
						'[$COMPANY]',
						'[$ADDRESS]',
						'[$ENV]'
					);
					
			$values = array(
						$request_data['name'],
						$request_data['email'],
						$request_data['company'],
						$request_data['address'],
						$_SERVER['HTTP_USER_AGENT']
					);
	
			/* */
			if( send_email( 
								$subject, 
								"BlueBand Media - Free Brains request",
								CONATCT_REQUEST_RECEPIENT,
								$request_data['email'],
								$request_data['name'],
								replace_message_vars( $vars, $values, $message )
						)){
				return array("success"=>TRUE);			
			}
			else {
				/* */
				return array("error"=>array(
										"type" 		=> "RequestException",
										"message"	=> "Error sending contact email. Please try again."
									)
							);
			}
		}
		else {
			/* */
			$error = new HTTPRequestError(
								"RequestException",
								"Error sending contact email. Please correct the errors."
							);
			$error->errors = array(
						new HTTPRequestError(
								"captcha",
								"Security code incorrect"
							);
					);
					
			return $error;
			
		}
    	
    }
}
