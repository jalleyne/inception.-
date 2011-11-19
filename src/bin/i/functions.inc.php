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
 * Helper functions 
 *
 * @author Jovan Alleyne <me@jalleyne.ca>
 */



/**
 * Validate email address. This email validator checks the domain
 * portion of the address as a valid domain using PHP checkdnsrr function
 * to validate the domain is reachable. Other checks are also made against
 * address length and checking for other valid characters.
 *
 * @param string $str Email address to be validated
 *
 * @return 
 */
function is_valid_email($str){
	$atIndex = strrpos($str, "@");
	if (is_bool($atIndex) && !$atIndex) {
			return false;
		} else {
			$domain = substr($str, $atIndex +1);
			$local = substr($str, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64) {
				// local part length exceeded
				return false;
			} else
				if ($domainLen < 1 || $domainLen > 255) {
					// domain part length exceeded
					return false;
				} else
					if ($local[0] == '.' || $local[$localLen -1] == '.') {
						// local part starts or ends with '.'
						return false;
					} else
						if (preg_match('/\\.\\./', $local)) {
							// local part has two consecutive dots
							return false;
						} else
							if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
								// character not valid in domain part
								return false;
							} else
								if (preg_match('/\\.\\./', $domain)) {
									// domain part has two consecutive dots
									return false;
								} else
									if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
										if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
											return false;
										}
									}
			if (!(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
				// domain not found in DNS
				return false;
			}
	}
	return true;
}

/**
 * Validate phone number against 10 digit western format
 *
 * @param string $str Value to be validated
 *
 * @return 
 */
function is_valid_phone( $str ){
	$regex = '/^(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:\(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9])\s*\)'.
			'|([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*(?:[.-]\s*)?)?([2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*'.
			'(?:[.-]\s*)?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?$/i';
	return preg_match($regex, $str);
}

/**
 * Validate password
 *
 * @param string $str Value to be validated
 *
 * @return 
 */
function is_valid_password( $str ){
	return strlen($str)>4;
}

/**
 * Validate postal code
 *
 * @param string $str Value to be validated
 *
 * @return 
 */
function is_valid_postalcode( $pcode ){
	$pc_regex = '/^[ABCEGHJKLMNPRSTVXY]{1}\d{1}[A-Z]{1} *\d{1}[A-Z]{1}\d{1}$/i';
	return preg_match($pc_regex, $pcode);
}


/**
 * Validation function
 *
 * @param string $val Value to be validated
 * @param string $type Type of value expected
 *
 * @return 
 */
function validate($val,$type){
	
	switch($type){
		
		case 'array':
			if( is_array($val) && count($val) ){
				foreach( $val as $v ){
					$v = trim($v);
					if( empty($v) )
						return 'Required field';
				}
			}
			else return 'Required field';
			break;
			
		case 'postalcode':
			if( !is_valid_postalcode( $val ) ) return "Invalid Postal code"; 
			break;
			
		case 'email':
			if( !is_valid_email( $val ) ) return "Invalid email address"; 
			break;
	}
	
	return null;
}



/**
 * Indents a flat JSON string to make it more human-readable.
 *
 * @param string $json The original JSON string to process.
 *
 * @return string Indented version of the original JSON string.
 */
function json_pretty_print($json) {

    $result      = '';
    $pos         = 0;
    $strLen      = strlen($json);
    $indentStr   = '  ';
    $newLine     = "\n";
    $prevChar    = '';
    $outOfQuotes = true;

    for ($i=0; $i<=$strLen; $i++) {

        // Grab the next character in the string.
        $char = substr($json, $i, 1);

        // Are we inside a quoted string?
        if ($char == '"' && $prevChar != '\\') {
            $outOfQuotes = !$outOfQuotes;
        
        // If this character is the end of an element, 
        // output a new line and indent the next line.
        } else if(($char == '}' || $char == ']') && $outOfQuotes) {
            $result .= $newLine;
            $pos --;
            for ($j=0; $j<$pos; $j++) {
                $result .= $indentStr;
            }
        }
        
        // Add the character to the result string.
        $result .= $char;

        // If the last character was the beginning of an element, 
        // output a new line and indent the next line.
        if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
            $result .= $newLine;
            if ($char == '{' || $char == '[') {
                $pos ++;
            }
            
            for ($j = 0; $j < $pos; $j++) {
                $result .= $indentStr;
            }
        }
        
        $prevChar = $char;
    }

    return $result;
}


/**
 * Convert resultset to array
 *
 * @param resource $r Mysql database resource
 *
 * @return array
 */
function mysql_resultset_to_array($r){
	$col = array();
	while( $item = mysql_fetch_assoc($r) )
		$col[] = $item;
		
	return $col;
}

/**
 * Load email template from folder
 *
 * @param string $message_path Path to message file
 *
 * @return array
 */
function get_message_template( $message_path ){
	return file_get_contents( $message_path );
}

/**
 * Replace vars in message template
 *
 * @param array $vars Variables listed in template
 * @param array $values Values to be replaced in template
 * @param string $message The message body
 *
 * @return string
 */
function replace_message_vars($vars,$values,$message){
	return str_replace(
							$vars,
							$values,
							$message
						);
}


/**
 * Create headers for email 
 *
 * @param string $from Email address of sender
 * @param string $from_name Name of sender
 *
 * @return 
 */
function email_headers($from,$from_name){
		$headers  = 'MIME-Version: 1.0'.PHP_EOL;
		$headers .= 'From: "'.$from_name.'" <'.$from.'>'.PHP_EOL;
		$headers .= 'Reply-To: '.$from.PHP_EOL;
		$headers .= 'Return-Path: <'.$from.'>'.PHP_EOL;
		$headers .= 'Content-Type: text/plain'. PHP_EOL;
		$headers .= 'Content-Transfer-Encoding: utf-8' . PHP_EOL;
		$headers .= 'X-Mailer: PHP/' . phpversion() . PHP_EOL;
		
		return $headers;
}



/**
 * Send an email message using the standard mail function
 *
 * @param string $subject The subject of the email.
 * @param string $recepient_name The name of the person receiving the email "John Smith"
 * @param string $to The email address of the recepient "jsmith@domain.com". This is 
 *                   concatenated with $recepient_name to form "John Smith<jsmith@domain.com>".
 * @param string $from The senders email address, used in the reply-to header
 * @param string $from_name The senders name
 * @param string $message The email message body
 *
 * @return boolean TRUE if message was sent, FALSE otherwise
 */
function send_email( $subject, $recepient_name, $to, $from, $from_name, $message ){
		/*
		 * send messages to recepient
		 */
		 
		return mail( 
							"$recepientName<$to>", 
							$subject, 
							$message,
							email_headers( $from, $from_name ) 
						);
}


