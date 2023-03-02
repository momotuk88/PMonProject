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

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$olt = isset($_POST['olt']) ? (int)$_POST['olt'] : null;
	$jobid = isset($_POST['jobid']) ? (int)$_POST['jobid'] : null;
	if($olt && $jobid){
		exec('php monitor.php --switch "'.$olt.'" --jobid "'.$jobid.'"  > /dev/null 2>&1');
	}
}
?>