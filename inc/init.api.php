<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
define('CONFIG',true);
require ENGINE_DIR.'init.lang.php';
require ROOT_DIR.'/inc/database.php';
require ENGINE_DIR.'classes/monitor.db.class.php';
require ROOT_DIR.'/inc/init.config.php';
require ENGINE_DIR.'functions/api.php';
require ENGINE_DIR.'classes/core.class.php';
require ENGINE_DIR.'classes/equipment.class.php';
require ENGINE_DIR.'classes/snmp.class.php';
require ENGINE_DIR.'init.olt.php';
require ENGINE_DIR.'classes/api.class.php';
?>
