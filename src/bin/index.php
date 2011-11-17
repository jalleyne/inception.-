<?php

/* */
require_once "settings.php";


/* */
define('DOMAIN',	$_SERVER['HTTP_HOST']);
define('BASE_DIR', 	'/');
define('BASE_URI',(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']?'https':'http').'://'.DOMAIN.BASE_DIR);


/* */
require_once "pageproxy.php";
