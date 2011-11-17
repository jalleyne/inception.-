<?php
/*
 * @file: 	set_language.php 
 * @author: jovan
 */


/* */
setcookie('LANGUAGE', $_GET['l']);


/* */
header('Location: '.$_SERVER['HTTP_REFERER']);
