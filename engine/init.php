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
//// clear system
// clear switch port error
$db->query('DELETE FROM '.$PMonTables['porterror'].' WHERE added < curdate() - interval 100 day');
// clear switch log
$db->query('DELETE FROM '.$PMonTables['swlog'].' WHERE added < curdate() - interval 30 day');
// clear history rx
$db->query('DELETE FROM '.$PMonTables['historyrx'].' WHERE datetime < curdate() - interval 300 day');
// clear history port up/down
$db->query('DELETE FROM '.$PMonTables['swlogport'].' WHERE added < curdate() - interval 30 day');
// clear stats on/off all onu
$db->query('DELETE FROM '.$PMonTables['pmonstats'].' WHERE datetime < curdate() - interval 35 day');
?>
