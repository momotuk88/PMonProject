<?php
date_default_timezone_set('Europe/Kiev');
define('PONMONITOR',true);
define('ROOT_DIR',dirname(__FILE__));
define('ENGINE_DIR',ROOT_DIR.'/inc/');
define('MODULE',ROOT_DIR.'/inc/module/');
if (!@fopen(ROOT_DIR.'/inc/database.php','r')){
	header('Location: /install.php');
	exit();
}
require_once ENGINE_DIR.'load.php';
?>