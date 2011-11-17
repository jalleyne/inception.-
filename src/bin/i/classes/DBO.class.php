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
 * Connects to database.
 *
 * @author Jovan Alleyne <me@jalleyne.ca>
 */
 
class DBO {
	
	private $db;
	
	function __construct(){
		$this->db = @mysql_connect( DB_HOST, DB_USER, DB_PASS );
		if( $this->db ){
			mysql_select_db( DB_NAME, $this->db );
		}
	}
	
	public function prepare($q,$v){
		/* */		
		foreach ( $v as &$value )
			if ( is_string($value) )
				$value = trim(mysql_real_escape_string($value));
		/* */
		array_unshift($v,$q);
		return call_user_func_array('sprintf',$v);
	}
	
	public function query($q){
		return mysql_query($q,$this->db);
	}
	
	public function getLastInsertID(){
		return mysql_insert_id($this->db);
	}
}