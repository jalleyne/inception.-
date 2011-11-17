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
 * Route handler for User interactions.
 *
 * @author Jovan Alleyne <me@jalleyne.ca>
 */
class UserRequestHandler {


	public function forgotPasswordResponder($request_data) {
		/* */
		$u = new User();
		$u->load($request_data['email'], 'email');

		if ($u) {

			/* */
			if ($u->requestPasswordReset(
						'Email subject', BASE_URI.
						$_SERVER['DOCUMENT_ROOT'].'/emails/forgotpassword.txt'
					)
				) {
				/* */
				return array (
					"success" => TRUE
				);
			}
		}

		return array (
			"error" => array (
				"type" => "ResourceException",
				"message" => "Error finding object."
			)
		);
	}

	public function resetPasswordResponder($request_data) {
		/* */
		if (!isset ($_SESSION['user'])) {
			/* */
			$_SESSION['password_reset_token'] = $request_data['_'];
			/* */
			header("Location: ".BASE_URI."admin/reset-password/");
			exit ();
		} else {
			/* */
			header("Location: ".BASE_URI);
			exit ();
		}
	}

	public function changePasswordResponder($request_data) {
		
		/* */
		if ( isset($_SESSION['password_reset_token']) ) {
			$u = $this->userFromToken($_SESSION['password_reset_token']);
			if ($u && $u->changePassword($request_data['pwd'])) {
				$u->invalidateResetToken();
				return array (
					"success" => TRUE
				);
			} else
				return array (
					"error" => array (
						"type" => "ResourceException",
						"message" => "Error changing object."
					)
				);
		} else {
			return array (
				"error" => array (
					"type" => "ResourceException",
					"message" => "Error finding object."
				)
			);
		}
	}
	
	protected function userFromToken($token){
		global $db;

		/* */
		$q = $db->prepare('SELECT `uid`, `token` ' .
				'FROM `password_resets` ' .
				'WHERE `token`=\'%s\' AND `reset`=\'0\'; ',
				
				array(
					$token
				)
			);
		
		$u = mysql_fetch_assoc($db->query($q));
		
		$user = new User();
		$user->load( $u['uid'] );
		
		if( $user )
			return $user;
		else NULL;
		
	}

}