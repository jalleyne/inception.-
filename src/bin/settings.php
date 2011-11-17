<?php
/*
 * Created on Sep 8, 2011
 * 
 */


/* Google analytics account number - format UA-XXXXX-Y */
define('ANALYTICS_UA','UA-XXXXX-Y');


/* Define Database Access constants */
if( $_SERVER['HTTP_HOST']!='localhost' ){
	
	define('DB_HOST','localhost');
	define('DB_USER','username');
	define('DB_PASS','password');
	define('DB_NAME','database_name');
	
}else{	
	
	/* Local server */
	define('DB_HOST','localhost');
	define('DB_USER','username');
	define('DB_PASS','password');
	define('DB_NAME','database_name');
}


/* Email constants */
define('NOREPLY_EMAIL',				'no-reply@domain.com');
define('EMAIL_SENDER_FROM',			'Sender Name');
define('CONATCT_REQUEST_RECEPIENT', 'consumer-response@domain.com');



/* 
* Set language constant, first check the cookie 
* then resort to default language set in .htaccess file
*/
define('LANGUAGE', 		
		empty($_COOKIE['LANGUAGE'])?
		getenv('DEFAULT_LANGUAGE'):$_COOKIE['LANGUAGE']);

/* 
* Set content root based on language setting, if 
* folder does not exist look for default language folder 
*/
define('CONTENT_ROOT',	
		'./'.(is_dir($_SERVER['DOCUMENT_ROOT'].'/'.LANGUAGE)?
		LANGUAGE:getenv('DEFAULT_LANGUAGE')));






