<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$olt = 0;
$jobid = 0;
$gocheck = false;
set_time_limit(6000);
$time = date('Y-m-d H:i:s');
require ROOT_DIR.'/inc/init.monitor.php';
$optsS = getopt('s:');
$optsJ = getopt('j:');
$olt = isset($optsS['s']) ? Clean::int($optsS['s']): null;
$jobid = isset($optsJ['j']) ? Clean::int($optsJ['j']): null;
$getolt = isset($_GET['getolt']) ? Clean::int($_GET['getolt']) : null;
$pmon = isset($_GET['pmon']) ? Clean::int($_GET['pmon']) : null;
if($getolt && $pmon==7){
	$getSwitch = $db->Fast('switch','*',['id'=>$getolt]);
	if(!empty($getSwitch['id'])){
		$olt = $getSwitch['id'];
		$gocheck = true;	
	}else{
		die('empty_device_id');
	}
}
if($olt && $jobid) {
	$getSwitchCron = $db->Fast('swcron','*',['id'=>$jobid,'oltid'=>$olt]);
	if(!empty($getSwitchCron['id'])){
		$db->SQLdelete('swcron',['id'=>$getSwitchCron['id']]);
		$gocheck = true;
	}
}
if(!$gocheck)
	die('unknown_cmd');
if(!$olt && !$getSwitchCron['id'])
	die('unknown_device_id');
$getSwitch = $db->Fast('switch','*',['id'=>$olt]);
if(!$getSwitch['id'])
	die('empty_device_id');
$getMonitor = new Monitor($getSwitch['id'],$getSwitch['class']);
$supportonu = null;
if(strtotime($getSwitch['updates']) < strtotime($time.' - 1min')){
	$supportonu = $getMonitor->getSupportOnu();
	$tempdata = false;
	if($supportonu){
		$db->SQLupdate('switch',['status'=>'go','updates'=>$time,'jobid'=>0],['id' => $olt]);
		$tempdata = $getMonitor->start();
	}
	$starttime = microtime(true);		
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
	$goport = true;
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
if($supportsaveonu)
	$getMonitor->UpdateInformationOlt();
if($starttime){
	$endTime = microtime(true);
	$executionTime = (int)$endTime - $starttime;
	if($executionTime){
		$db->SQLupdate('switch',['status'=>'no','timecheck'=>$executionTime,'timechecklast'=>(!empty($getSwitch['timecheck'])?$getSwitch['timecheck']:0)],['id'=>$getSwitch['id']]);
	}
}
?>
