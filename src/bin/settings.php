<?php
/*
 * Created on Sep 8, 2011
 * 
 */

$permissions = array(
	'read' 		=> 1,
	'write'		=> 2,
	'update'	=> 4,
	'delete'	=> 8
);




/* Google analytics account number - format UA-XXXXX-Y */
define('ANALYTICS_UA','UA-XXXXX-Y');


/* Define Database Access constants */
switch( $_SERVER['HTTP_HOST'] ){
	/* Local Development server*/
	case 'localhost':
		define('DB_HOST','localhost');
		define('DB_USER','dbuser_ecot');
		define('DB_PASS','5u@@u3Re7eDu');
		define('DB_NAME','ecot');
		break;
	/* Live server*/
	case 'eco.dev.transmitterstudios.com':
		define('DB_HOST','localhost');
		define('DB_USER','economic_dev');
		define('DB_PASS','passw0rd');
		define('DB_NAME','economic_dev2');
		break;	
	/* Live server*/
	default:
		define('DB_HOST','localhost');
		define('DB_USER','economic_live');
		define('DB_PASS','9LGUZWyczDvP');
		define('DB_NAME','economic_cms2');
		break;
}


/* */
$acceptable_mime_types = array(
							'image/pjpeg',
							'image/jpeg',
							'image/x-png',
							'image/png',
							
							'application/pdf',
							
							'video/quicktime',
							
							'audio/mp3'
						);

/* */
$upload_folder = "/media/";

if( !is_writable($_SERVER['DOCUMENT_ROOT'].$upload_folder) ){
	chmod($_SERVER['DOCUMENT_ROOT'].$upload_folder, 0777);
	if( !is_writable($_SERVER['DOCUMENT_ROOT'].$upload_folder) )
		trigger_error(
				'Uploads folder is not writable and the script '.
				'was unable to modify the directory permissions.');
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

