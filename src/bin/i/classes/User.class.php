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
 * Generic User object.
 *
 * @author Jovan Alleyne <me@jalleyne.ca>
 */
class User {

	public $id;

	public $role;

	public $username;

	public $email;

	public $access_token;

	function __construct() {
	}

	/*
	 * access control 
	 */
	public function login() {
		$_SESSION['user'] 			= serialize($this);
		$_SESSION['access_token'] 	= 
		$this->access_token			= base64_encode(md5($_SESSION['user'].microtime())).microtime();
	}

	public function logout() {
		unset ($_SESSION['user']);
		unset ($_SESSION['access_token']);
	}

	/*
	 * password management
	 */
	public function changePassword($newPwd) {
		global $db;
		/* */
		$q = $db->prepare('UPDATE `users` ' .
		'SET `password`=MD5(\'%s\') ' .
		'WHERE `id`=%d; ', array (
			$newPwd,
			$this->id
		));

		$r = $db->query($q);

		if ($r)
			return true;
		else
			return false;
	}

	public function invalidateResetToken() {
		global $db;
		/* */
		$q = $db->prepare('UPDATE `password_resets` ' .
		'SET `reset`=\'1\' ' .
		'WHERE `uid`=%d; ', array (
			$this->id
		));

		$r = $db->query($q);

		/* */
		unset ($_SESSION['password_reset_token']);
		
		/* */
		return (boolean) $r;
	}

	public function hasValidResetToken() {
		/* */
		$q = $db->prepare('SELECT `uid`, `token` ' .
		'FROM `password_resets` ' .
		'WHERE `token`=\'%s\' AND `reset`=\'0\';', array (
			$_SESSION['password_reset_token']
		));

		$r = $dbo->query($q);

		return mysql_num_rows($r) ? TRUE : FALSE;
	}

	/*
	 * 
	 */
	public function requestPasswordReset($emailsubject, $emailpath) {
		global $db;
		/* */
		$token = $this->generatePasswordResetToken();
		/* */
		if ($this->id && $token) {
			$q = $db->prepare('INSERT INTO `password_resets` ' .
			'(`uid`,`token`) ' .
			'VALUES(%d,\'%s\'); ', array (
				$this->id,
				$token
			));
			$r = $db->query($q);

			if ($r) {

				/* */
				$this->sendPasswordResetEmail($emailsubject, $emailpath, $token);

				/* */
				return true;
			} else
				return false;
		} else
			return false;
	}

	private function generatePasswordResetToken() {
		if ($this->id && $this->username && $this->email)
			return base64_encode(MD5($this->id . $this->username . $this->email) . $this->id) . '.' . microtime();
		else
			return null;
	}

	public function getPasswordResetUrl($token = null) {
		return WS_URI . 'u/resetpassword/?_=' . ($token?$token:$this->generatePasswordResetToken());
	}

	public function sendPasswordResetEmail($subject, $messagepath, $token) {
		/* */
		$message = get_message_template($messagepath);
		/* */
		$vars = array (
			'[$USERNAME]',
			'[$RESET_URL]'
		);

		$values = array (
			$this->username,
			$this->getPasswordResetUrl($token)
		);

		return send_email(
					$subject, 
					$this->username, 
					$this->email, 
					NOREPLY_EMAIL, 
					EMAIL_SENDER_NAME, 
					replace_message_vars($vars, $values, $message)
				);
	}

	/*
	 * load user details
	 */

	public function load($user, $from = 'id') {
		global $db;

		$q = $db->prepare("SELECT `id`, `username`, `email`, `activated` " .
		"FROM `users` " .
		"WHERE `%s` = '%s' " .
		"LIMIT 1;", array (
			$from,
			$user
		));

		$r = $db->query($q);

		if ($r) {
			/* */
			$u = mysql_fetch_assoc($r);
			$this->id 			= $u['id'];
			$this->email 		= $u['email'];
			$this->username 	= $u['username'];
			$this->activated 	= $u['activated'];
		} else {
			return null;
		}
	}

}
?>