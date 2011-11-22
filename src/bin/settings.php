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



/* Set base directory and uri constants. Determines if SSL is enabled and 
*  preceeds the domain with the proper protocal.
*/
define('BASE_DIR', 		'/');
define('BASE_URI',		(isset($_SERVER['HTTPS'])&&
						
						// Note that when using ISAPI with IIS, the value will 
						//be off if the request was not made 
						//through the HTTPS protocol.
						//Ref PHP Manual: http://goo.gl/yD35i
						$_SERVER['HTTPS']!='off' 
						
						?'https':'http')
						.'://'.$_SERVER['HTTP_HOST']
						.BASE_DIR
						);


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






