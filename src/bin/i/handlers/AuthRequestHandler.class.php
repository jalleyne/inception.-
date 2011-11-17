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
 * Route handler for Authentication requests.
 *
 * @author Jovan Alleyne <me@jalleyne.ca>
 */
class AuthRequestHandler {

    function logoutResponder($request_data){
    	if( isset($_SESSION['user']) ){
    		$u = unserialize($_SESSION['user']);
    		if( $u ){
    			$u->logout();
	    		header('Location: http://'.$_SERVER['HTTP_HOST'].'/#!/admin/');
    		}
    	}
    	return FALSE;
    }
    
    function loginResponder($request_data){
    	$u = $this->loginUser(
    					$request_data['email'],
    					$request_data['pwd']
					);
    	if( $u ){
    		
    		$u->login();
    		
    		header('Location: http://'.$_SERVER['HTTP_HOST'].'/#!/admin/');
    		
    		return $u;
    	}
    	else {
    		return null;
    	}
    }
    
    function loginStatusResponder($request_data){
    	if( isset($_SESSION['user']) ){
    		return array('status'=>200);
    	}
    	else {
    		return array('status'=>403);
    	}
    }
    
    function loginUser($email,$pwd){
    	global $db;
    	
    	$q = $db->prepare("SELECT `id`, `role`, `username` " .
    						"FROM `users` " .
    						"WHERE `email` = '%s' AND `password` = MD5('%s') AND `activated` = '1' " .
    						"LIMIT 1;",
    						
    						array(
    							$email,
    							$pwd
    						)
						);
		$r = $db->query($q); 
		$user = mysql_fetch_assoc($r);
		
		if( $user ){
			
			
			$u 				= new User();
			$u->id 			= $user['id'];
			$u->role 		= $user['role'];
			$u->username 	= $user['username'];
			
			return $u;
		}
		else {
			return FALSE;
		}
    }
    
    
}