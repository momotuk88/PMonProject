<?php
/*
=====================================================
 Project PMon
-----------------------------------------------------
 https://t.me/pon_monitor
-----------------------------------------------------
 Copyright (c) 2022-2023 PMon
=====================================================
 This code is protected by copyright
=====================================================
*/

ob_start();
ob_implicit_flush(false);

error_reporting ( E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE );
ini_set ( 'error_reporting', E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE );

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