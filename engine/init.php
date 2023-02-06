<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
require ROOT_DIR.'/inc/init.monitor.php';
$Sheduler = new Cron();
$getListCron = $Sheduler->start();
if(is_array($getListCron)){
	foreach($getListCron as $tempSw){
		if(!empty($tempSw['olt']) && !empty($tempSw['jobid'])){			
			$Sheduler->StartJobMonitor($tempSw['jobid'],$tempSw['olt']);
		}
	}
}
?>
