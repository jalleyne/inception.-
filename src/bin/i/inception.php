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
 * Initializes the Inception RESTful Data application
 *
 * @author Jovan Alleyne <me@jalleyne.ca>
 */

/* Instanciate InceptionRESTfulDataApplication class */
$i = new InceptionRESTfulDataApplication();

/* Load Route map from file system */
$i->loadRouteMap(ROUTE_FILE_PATH);

/* Parse loaded XML Route map */
if( $map=$i->parseRouteMap() ){
	
	/* Read request data based on request method */
	$i->readRequestData();
	
	/* Determine if user can access resource based on Route definition */
	if( $i->canAccess() ){
		
		/* Validate request parameters */
		if( $errors = $i->validateRequest() ){
			
			/* */
			$response = new HTTPRequestError(
								'ResourceParameterException',
								'Required parameters not included.'
							);
			
			/* If redirect param is set redirect the response */
			if( isset($_REQUEST['response_redirect']) ){
					
				/* */
				if( isset($_REQUEST['formid']) )
					$data = array(
										"errors" => $errors,
										"form"	 => $i->request_data
									);
				else $data = $errors;
				
				/* */
				$response->redirect(
							$_REQUEST['response_redirect'],
							$data
						);
			}
			
			/* else print the response as body*/
			else {
				$response->send($errors);
			}
		}
		else{
			/* Delegate request to mapped handler */
			$response = $i->delegateRequest();
			$i->handleResponse($response);
		}
	}
	/* Respond with access denied */
	else {
		$response = new HTTPRequestError(
							'AuthenticationException',
							'Access to the requested resource has been denied.'
						);
		$response->send();
	}
}

/* Route was found but no method mapped to $_SERVER['REQUEST_METHOD'] */
else if( $map === FALSE ){
	/* */
	$response = new HTTPRequestError(
						'ResourceHandlerException',
						'The request [:'.$i->uri.'] does not support '.
						'the request method [:'.$_SERVER['REQUEST_METHOD'].'].'
					);
	$response->send();
	
}

/* No Route found in mapping data */
else {
	/* */
	$response = new HTTPRequestError(
						'ResourceHandlerException',
						'The request [:'.$i->uri.'] does not exist.'
					);
	$response->send();
}
