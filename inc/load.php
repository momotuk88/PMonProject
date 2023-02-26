<?php
/*
*	PMonProject
*	2023
*	@momotuk88
*/
$time = date('Y-m-d H:i:s');
define('CONFIG',true);
global $USER;
global $act;
global $do;
global $config;
require ENGINE_DIR.'init.lang.php';
require ROOT_DIR.'/inc/database.php';
require ROOT_DIR.'/inc/init.sql.php';
require ENGINE_DIR.'classes/monitor.db.class.php';
require ENGINE_DIR.'classes/users.class.php';
require ROOT_DIR.'/inc/init.config.php';
require ENGINE_DIR.'classes/telnet.class.php';
require ENGINE_DIR.'functions/core.php';
require ENGINE_DIR.'classes/clean.class.php';
require ENGINE_DIR.'classes/core.class.php';
require ENGINE_DIR.'classes/tpl.class.php';
require ENGINE_DIR.'classes/route.class.php';
require ROOT_DIR.'/inc/init.license.php';
$go =  new Route();
$tpl =  new TemplateMonitor;
$checkLicenseSwitch = $db->Multi($PMonTables['switch']);
$SQLListlocation = $db->Multi($PMonTables['gr']);
$SQLCountMonitor = $db->Multi($PMonTables['mononu']);
require ROOT_DIR.'/inc/init.module.php';
require ROOT_DIR.'/inc/init.html.php';
require ROOT_DIR.'/inc/init.cms.php';
?>