<?php
if(!defined('PONMONITOR')){
	die('Access is denied.');
}

/* 
приклад як має бути 
define('DBHOST', 'localhost');
define('DBUSER', 'root');
define('DBPASS', 'root');
define('DBNAME', 'newtest');
*/
if(!defined('DBHOST')){
	die("Can't connect to MySQL server. Check [local.file] database.php");
}
?>
