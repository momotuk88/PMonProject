<?php
date_default_timezone_set('Europe/Kiev');
define('PONMONITOR',true);
define('ROOT_DIR',dirname(__FILE__));
define('API_DIR',ROOT_DIR.'/engine/');
define('ENGINE_DIR',ROOT_DIR.'/inc/');
require_once API_DIR.'/statusport.php';
@unlink(dirname(__FILE__).'/export/snmpcache/snmpdebug_oid.log');
?>