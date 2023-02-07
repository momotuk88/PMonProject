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
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$olt = isset($_POST['olt']) ? (int)$_POST['olt'] : null;
	$jobid = isset($_POST['jobid']) ? (int)$_POST['jobid'] : null;
	if($olt && $jobid){
		exec('php monitor.php --switch "'.$olt.'" --jobid "'.$jobid.'"  > /dev/null 2>&1');
	}
}
?>