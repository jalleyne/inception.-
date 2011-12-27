<?php
/*
 * Created on Sep 8, 2011
 * 
 */





/* Google analytics account number - format UA-XXXXX-Y */
define('ANALYTICS_UA','UA-XXXXX-Y');


/* Define Database Access constants */
switch( $_SERVER['HTTP_HOST'] ){
	/* Local Development server*/
	case 'localhost':
		define('DB_HOST','localhost');
		define('DB_USER','dev_user');
		define('DB_PASS','password');
		define('DB_NAME','database_name');
		break;
	
	/* Live server*/
	default:
		define('DB_HOST','localhost');
		define('DB_USER','dev_user');
		define('DB_PASS','password');
		define('DB_NAME','database_name');
		break;
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
* Set content root based on language setting, if 
* folder does not exist look for default language folder 
*/
define('CONTENT_ROOT',	
			$_SERVER['DOCUMENT_ROOT'].'/' . 
			(is_dir($_SERVER['DOCUMENT_ROOT'].'/'.LANGUAGE)?
			LANGUAGE:getenv('DEFAULT_LANGUAGE'))
	);
	
	
	

/* */
$path  = implode(PATH_SEPARATOR,array(CONTENT_ROOT));
set_include_path( get_include_path() . PATH_SEPARATOR . $path );

