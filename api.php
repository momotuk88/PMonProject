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
date_default_timezone_set('Europe/Kiev');
error_reporting ( E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE );
ini_set ( 'error_reporting', E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE );

define('PONMONITOR',true);
define('ROOT_DIR',dirname(__FILE__));
define('API_DIR',ROOT_DIR.'/engine/');
define('ENGINE_DIR',ROOT_DIR.'/inc/');

require_once API_DIR.'/api.php';
?>