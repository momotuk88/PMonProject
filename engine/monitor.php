<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$olt = null;
$jobid = null;
$gocheck = null;
$goport = false;
$tempdata = null;
$supportonu = null;
$starttime = 0;
set_time_limit(6000);
$time = date('Y-m-d H:i:s');
require ROOT_DIR.'/inc/init.monitor.php';
$long_options = ["switch:","jobid:"];
$options = getopt("s:j:", $long_options);
if(isset($options["s"]) || isset($options["switch"])) {
    $olt = isset($options["s"]) ? $options["s"] : $options["switch"];
}
if(isset($options["j"]) || isset($options["jobid"])) {
    $jobid = isset($options["j"]) ? $options["j"] : $options["jobid"];
}
$olt = 12;
$gocheck = true;
if(!$olt && !$jobid) {
	die('corect_system_cron');
}
if($olt) {
	$getSwitch = $db->Fast($PMonTables['switch'],'*',['id'=>$olt]);
	if(!$getSwitch['id'])
		die('empty_device_id');
	if(!empty($getSwitch['id'])){
		$db->SQLdelete($PMonTables['swcron'],['oltid'=>$getSwitch['id']]);
		$gocheck = true;
		switchLog($getSwitch['id'],'cron',$lang['gocheckcron']);
	}
}else{
	die('unknown_device');
}
if(!$gocheck)
	die('unknown_cmd');
$getMonitor = new Monitor($getSwitch['id'],$getSwitch['class']);
$supportonu = $getMonitor->getSupportOnu();
if($supportonu){
	$db->SQLupdate($PMonTables['switch'],['status'=>'go','updates'=>$time,'jobid'=>0],['id' => $olt]);
	$tempdata = $getMonitor->start();
}
$starttime = microtime(true);
if($supportonu && !$tempdata){
	switchLog($getSwitch['id'],'cron',$lang['emptytemponu']);
}
if($supportonu && is_array($tempdata)){
	foreach($tempdata as $idont => $getdata){
		#usleep(random_int(1,5));
		$resapi = get_curl_api($getdata,true);
		if(isset($resapi)){
			$resarray[$idont] = array_merge($getdata, $resapi);
		}else{
			$resarray[$idont] = $getdata;
		}
	}	
	sleep(2);	
	if(is_array($resarray)){
		foreach($resarray as $getdataont){
			if($getdataont['pon']=='epon'){
				$getMonitor->tempSaveEpon($getdataont);
			}
			if($getdataont['pon']=='gpon'){
				$getMonitor->tempSaveGpon($getdataont);
			}
		}
		$goont = true;
		$db->SQLdelete($PMonTables['onus'],['cron' => 2, 'olt' => $olt]);
	}else{
		switchLog($getSwitch['id'],'cron',$lang['checkapisystem']);
	}
	$getlistrxcheck = $getMonitor->getListSignal();
	if($goont && is_array($getlistrxcheck)){
		foreach($getlistrxcheck as $idrx => $getrxdata){
			#usleep(random_int(1,5));
			if(isset($getrxdata)){
				$resRxApi = get_curl_api($getrxdata,true);
			}
			if(is_array($resRxApi) && is_array($getrxdata)){
				$resultrxarray[$idrx] = array_merge($getrxdata,$resRxApi);
			}else{
				$resultrxarray[$idrx] = $getrxdata;
			}
		}
	}
	if(is_array($resultrxarray)){
		foreach($resultrxarray as $getrxdataont){
			if($getrxdataont['pon']=='epon'){
				$getMonitor->tempSaveSignalEpon($getrxdataont);
			}
			if($getrxdataont['pon']=='gpon'){
				$getMonitor->tempSaveSignalGpon($getrxdataont);
			}
		}	
	}
}
sleep(2);
$supportport = $getMonitor->getSupportPort();
if($supportport){
	$indexport = $getMonitor->getPort();
	if(is_array($indexport)){
		$getMonitor->savePort($indexport);
	}
}
sleep(2);
if(is_array($resarray))
	$getMonitor->UpdateInformationOlt();
if($starttime){
	$endTime = microtime(true);
	$executionTime = (int)$endTime - $starttime;
	if($executionTime){
		$db->SQLupdate($PMonTables['switch'],['status'=>'no','timecheck'=>$executionTime,'timechecklast'=>(!empty($getSwitch['timecheck'])?$getSwitch['timecheck']:0)],['id'=>$getSwitch['id']]);
	}
}
?>
