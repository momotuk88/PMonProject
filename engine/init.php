<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}

require ROOT_DIR.'/inc/init.monitor.php';
// removing terminals that are not tied to switches
$getlistswitch = $db->Multi($PMonTables['switch'],'netip,id');
if(count($getlistswitch)){
	foreach($getlistswitch as $switch){
		$dataswitch[$switch['id']]['id'] = $switch['id'];
	}
}
$getlistonu = $db->Multi($PMonTables['onus'],'inface,type,olt,idonu');
if(count($getlistonu)){
	foreach($getlistonu as $onu){
		if(empty($dataswitch[$onu['olt']]['id'])){
			delete_onu($onu['idonu']);
			$db->SQLinsert($PMonTables['log'],['progress' =>'onudelet','message' =>$lang['deletonunomark'].$onu['type'].$onu['inface'],'added' =>date('Y-m-d H:i:s')]);
		}
	}
}
post_system_health(1);
sleep(1);
// removing terminals that are not tied to switches
$Sheduler = new Cron();
$getListCron = $Sheduler->start();
if(is_array($getListCron)){
	foreach($getListCron as $tempSw){
		if(!empty($tempSw['olt']) && !empty($tempSw['jobid'])){			
			$Sheduler->StartJobMonitor($tempSw['jobid'],$tempSw['olt']);
		}
	}
}
$db->query('DELETE FROM '.$PMonTables['porterror'].' WHERE added < curdate() - interval 100 day');
$db->query('DELETE FROM '.$PMonTables['swlog'].' WHERE added < curdate() - interval 30 day');
$db->query('DELETE FROM '.$PMonTables['historyrx'].' WHERE datetime < curdate() - interval 300 day');
$db->query('DELETE FROM '.$PMonTables['swlogport'].' WHERE added < curdate() - interval 30 day');
$db->query('DELETE FROM '.$PMonTables['pmonstats'].' WHERE datetime < curdate() - interval 30 day');
$db->query('DELETE FROM '.$PMonTables['pingstats'].' WHERE datetime < curdate() - interval 10 day');
// ping ip2long
?>
