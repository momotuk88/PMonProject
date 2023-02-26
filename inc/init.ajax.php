<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
if (!defined('AJAX')){
	die('Hacking attempt!');
}	
$time = date('Y-m-d H:i:s');
define('CONFIG',true);
require ENGINE_DIR.'init.lang.php';
require ROOT_DIR.'/inc/database.php';
require ROOT_DIR.'/inc/init.sql.php';
require ENGINE_DIR.'classes/monitor.db.class.php';
require ENGINE_DIR.'classes/clean.class.php';
require ENGINE_DIR.'classes/users.class.php';
require ENGINE_DIR.'classes/telnet.class.php';
if(!empty($USER['id'])){
	if(defined('ONT')){
		require ENGINE_DIR.'classes/equipment.class.php';
		require ENGINE_DIR.'classes/snmp.class.php';
		require ENGINE_DIR.'init.olt.php';
		require ENGINE_DIR.'classes/ont.class.php';
	}
	require ENGINE_DIR.'functions/monitor.php';
	require ROOT_DIR.'/inc/init.config.php';
	require ENGINE_DIR.'functions/core.php';
}else{
	die('Authentication failed');
}
?>
