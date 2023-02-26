<?php
/*
=====================================================
 PMonProject - PON Device Management UA
-----------------------------------------------------
 Copyright (c) 2023 
 -----------------------------------------------------
 Developer @momotuk88  
=====================================================
 This code is protected by copyright
=====================================================
*/
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
define('CONFIG',true);
require ENGINE_DIR.'init.lang.php';
require ROOT_DIR.'/inc/init.sql.php';
require ROOT_DIR.'/inc/database.php';
require ENGINE_DIR.'classes/monitor.db.class.php';
require ENGINE_DIR.'classes/clean.class.php';
require ROOT_DIR.'/inc/init.config.php';
require ENGINE_DIR.'functions/monitor.php';
require ENGINE_DIR.'functions/core.php';
require ENGINE_DIR.'classes/core.class.php';
require ENGINE_DIR.'classes/equipment.class.php';
require ENGINE_DIR.'classes/snmp.class.php';
require ENGINE_DIR.'classes/cron.class.php';
require ENGINE_DIR.'init.olt.php';
?>
