<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
switch($act){
	case 'savebdcomconfig':
	print_R($_POST);
	break;
}
die;
?>