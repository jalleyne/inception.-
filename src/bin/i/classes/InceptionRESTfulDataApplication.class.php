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
 * Initializes mapping of URI to request handler classes.
 *
 * @author Jovan Alleyne <me@jalleyne.ca>
 */
class InceptionRESTfulDataApplication {
	
    /**
    * Version.
    */
    const VERSION = '0.1';

	/**
	* Raw URI of the request
	*/
	protected $uri;
	
	/**
	* URI split by /
	*/
	protected $uri_parts;
	
	/**
	* Data send along with request
	*/
	protected $request_data;
	
	/**
	* Route XML
	*/
	protected $route_map;
	
	/**
	* Route XML object matched by URI
	*/
	protected $route_data;
	
	/**
	* RouteHandler XML object matched by URI
	*/
	protected $route_handler;
	
	/**
	* Method name matched by $_SERVER['REQUEST_METHOD'] in
	* the $this->route_handler
	*/
	protected $route_method;
	
	
	/*
	* Constructor
	**/
	function __construct(){
		/* */
		$this->parseRequestURI();
	}
	
	/*
	* Getter
	**/
	public function __get($name){
		return $this->$name;
	}
	
	
	/*
	* Parse Request URI for meaningful parts
	**/
	protected function parseRequestURI(){
		/* */
		$this->uri = str_ireplace(
							dirname($_SERVER['SCRIPT_NAME']),
							'',
							str_ireplace(
								'?' . $_SERVER['QUERY_STRING'],
								'',
								$_SERVER['REQUEST_URI'] 
							)
						);
		/* */
		$this->uri_parts = array_values(
								array_filter(
									explode( '/', $this->uri )));	

	}
	
	
	/*
	* Read the request stream for data
	*
	* @param string $method Request method used to search for mapped route 
	**/
	public function readRequestData($method=NULL){
		/* */
		if( !$method ) $method = $_SERVER['REQUEST_METHOD'];
		
		/* */
		switch($method){
			case 'GET':
				$this->request_data = $_GET;
				break;		
			case 'POST':
				$this->request_data = $_POST;
				break;
			case 'PUT':
				parse_str(file_get_contents("php://input"),$this->request_data);
				break;
			default:
				$this->request_data = NULL;
				return FALSE;
				break;
		}
		
		return TRUE;
	}
	
	/*
	* Load Route Map definitions from XML file
	*
	* @param string $map Path to XML file containing Route definitions
	**/
	public function loadRouteMap($map="RouteMap.xml"){
		/* */
		if (file_exists($map)) {
			$this->route_map = simplexml_load_file($map);
			return TRUE;
		}
		else return NULL;
	}
	
	/*
	* Parse the XML definitions and select the Route matching the URI requested
	*
	* @param string $method Request method used to search for mapped route 
	**/
	public function parseRouteMap($method=NULL){
		
		if( !$this->route_map ) 
			throw new Exception(
						'RouteMap not loaded. '.
						'Please call $obj->loadRouteMap(...) '.
						'before calling $obj->parseRouteMap(...)'
					);
		
		/* */
		if( !$method ) $method = $_SERVER['REQUEST_METHOD'];
		
		/* */
		$this->getRoute();
	
		/* */
		if( $this->route_data ){
			
			/* */
			$handler = $this->getRouteHandler($method);	
			
			/* */
			$handlerFunc 	= (string)$handler->attributes()->responder;
			
			/* */
			$handlerInst =  $this->getRouteHandlerInstance();

			/* */
			if( isset($handlerFunc) && method_exists($handlerInst,$handlerFunc) ){
				$this->route_handler 	= $handler;
				$this->route_method 	= $handlerFunc;

				return TRUE;
			}
			else return NULL;
		}
		else return NULL;
	}
	
	/*
	* Select the matching Route from Mapping
	*
	**/
	protected function getRoute(){
		/* */
		foreach ( $this->route_map->Route as $route ){
			$pattern = (string)$route->attributes()->pattern;
			if( @preg_match('/^'.$pattern.'$/i', $this->uri) ||
					$pattern == $this->uri ){
				/* */
				$this->route_data = $route;
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	
	/*
	* Select the matching handler based on method
	*
	* @param string $method Request method used to search for mapped route 
	**/
	protected function getRouteHandler($method){
		/* */
		foreach ( $this->route_data->RouteHandler as $handler ){
			if( $handler->attributes()->method == $method ){
				return $handler;
			}
		}
		return NULL;
	}
	

	/*
	* Get instance of hanlder class defined in Route map
	*
	* @param string $handlerObj Create an instance of the 
	* handler Object mapped from URI
	**/
	protected function getRouteHandlerInstance($handlerObj=NULL){
		/* */
		if( !$handlerObj ) 
			$handlerObj = (string)$this->route_data->attributes()->handler;
		return new $handlerObj();
	}
	
	
	/*
	* Validate the request based on Route map parameter definitions
	*
	* @param string $handler XML object representing Route map
	**/
	public function validateRequest($handler=NULL){
		
		/* */
		if( !$handler ) $handler = $this->route_handler;
		
		/* */
		if( $handler->parameters ){

			/* */
			$errors = array();
			foreach( $handler->parameters as $params ){
				foreach( $params as $param ){
					/* */
					if( !isset($_REQUEST[ (string)$param->attributes()->name ]) || 
						(isset($_REQUEST[ (string)$param->attributes()->name ]) && 
							empty($_REQUEST[ (string)$param->attributes()->name ])) ){
						$errors[] = new HTTPRequestError( 
												(string)$param->attributes()->name,
												(string)$param->attributes()->name . " is a required field"
									 		);
					}else if( $error = validate( 
										$_REQUEST[ (string)$param->attributes()->name ], 
										(string)$param->attributes()->type 
									) ){
									
						$errors[] = new HTTPRequestError( 
												(string)$param->attributes()->name,
												$error
									 		);
									
					}
				}
			}
			
			/* */
			if( count($errors) ) return $errors;
		}
		
		return NULL;
	}
	
	/*
	* Determine if the current user (if any) is allowed to access 
	* the requested URI
	*
	**/
	public function canAccess(){
		/* */
		$handler_role = (int)$this->route_handler->attributes()->access;
		
		return (boolean)( isset($_SESSION['access_token']) && 
							($handler_role && 
								(int)$_SESSION['user_role'] >= $handler_role && 
								$_SESSION['access_token'] == $this->request_data['access_token']) || 
								!$handler_role );		
	}
	
	/*
	* Delegate the request to the matched Route handler and method
	*/
	public function delegateRequest(){
		/* */
		$obj 			= $this->getRouteHandlerInstance();
		$handlerFunc 	= $this->route_method;
		
		/* */
		$response = $obj->$handlerFunc($this->request_data);
			
		/* */
		if( isset($_REQUEST['response_redirect']) ){
			$response->redirect( $_REQUEST['response_redirect'] );
		}
		else {
			$response->send();
		}
	}
}
