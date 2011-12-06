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
* User defined functions
*/





/**
* Functions
*/


/*
 *
 */
$queued_scripts = array();

function queue_js($scripts){
	global $queued_scripts;
	if( is_array($scripts) )
		$queued_scripts = array_merge($queued_scripts,$scripts);
	else $queued_scripts[] = $scripts;
}

function print_queued_scripts(){
	global $queued_scripts;
	foreach ( $queued_scripts as $script )
		echo "<script src=\"$script\"></script>";
} 

/*
 *
 */
$queued_inline_js = array();

function queue_inline_js($script){
	global $queued_inline_js;
	$queued_inline_js[] = $script;
}

function print_queued_inline_js(){
	global $queued_inline_js;
	foreach ( $queued_inline_js as $script )
		echo PHP_EOL."<script type=\"text/javascript\">".$script."</script>".PHP_EOL;
}




/* */
define('CRLF',"\n");

/**
*
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
*
*/
function is_valid_phone( $phone ){
	return true;
}

/**
*
*/
function is_valid_password( $pwd ){
	return strlen($pwd)>4;
}

/**
*
*/
function is_valid_postalcode( $pcode ){
	$pc_regex = '/^[ABCEGHJKLMNPRSTVXY]{1}\d{1}[A-Z]{1} *\d{1}[A-Z]{1}\d{1}$/i';
	return preg_match($pc_regex, $pcode);
}



/**
*
*/
function mysql_resultset_to_array($r){
	$col = array();
	while( $item = mysql_fetch_assoc($r) )
		$col[] = $item;
		
	return $col;
}

/**
*
*/
function get_message_template( $message_path ){
	return file_get_contents( $message_path );
}

/**
*
*/
function replace_message_vars($vars,$values,$message){
	return str_replace(
							$vars,
							$values,
							$message
						);
}


/**
*
*/
function email_headers($from,$from_name){
		$headers  = 'MIME-Version: 1.0'.CRLF;
		$headers .= 'From: "'.$from_name.'" <'.$from.'>'.CRLF;
		$headers .= 'Reply-To: '.$from.CRLF;
		$headers .= 'Return-Path: <'.$from.'>'.CRLF;
		$headers .= 'Content-Type: text/plain'. CRLF;
		$headers .= 'Content-Transfer-Encoding: utf-8' . CRLF;
		$headers .= 'X-Mailer: PHP/' . phpversion() . CRLF;
		
		return $headers;
}


/**
*
*/

function send_email( $subject, $recepientName, $to, $from, $from_name, $message ){
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
*
*/
function validate($val,$type){
	
	switch($type){
		case 'email':
			if( !is_valid_email( $val ) ) return "Invalid email address"; 
			break;
	}
	
	return null;
}

