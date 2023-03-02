<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$starttime = microtime(true);
$olt = $olt ?? null;
$jobid = $jobid ?? null;
$gocheck = $gocheck ?? null;
$goport = $goport ?? null;
$tempdata = $tempdata ?? null;
$supportonu = $supportonu ?? null;
$resultrxarray = $resultrxarray ?? null;
$starttime = 0;
set_time_limit(6000);
$time = date('Y-m-d H:i:s');
require ROOT_DIR.'/inc/init.monitor.php';
$long_options = ["switch:","jobid:"];
$options = getopt("s:j:", $long_options);
if(isset($options["s"]) || isset($options["switch"]))
    $olt = isset($options["s"]) ? $options["s"] : $options["switch"];
if(isset($options["j"]) || isset($options["jobid"]))
    $jobid = isset($options["j"]) ? $options["j"] : $options["jobid"];
if(!$olt && !$jobid)
	die('corect_system_cron');
if(is_numeric($olt)){
	$getswitch = $db->Fast($PMonTables['switch'],'*',['id'=>$olt]);
	if(!$getswitch['id'])
		die('empty_device_id');
	if(!empty($getswitch['id'])){
		$db->SQLdelete($PMonTables['swcron'],['oltid'=>$getswitch['id']]);
		$gocheck = true;
		switchLog($getswitch['id'],'cron',$lang['gocheckcron']);
	}
}else{
	die('unknown_device');
}
if(!$gocheck)
	die('unknown_cmd');
$getmonitor = new Monitor($getswitch['id'],$getswitch['class']);
$supportonu = $getmonitor->getSupportOnu();
if($supportonu){
	$db->SQLupdate($PMonTables['switch'],['status'=>'go','updates'=>$time,'jobid'=>0],['id' => $olt]);
	$tempdata = $getmonitor->start();
}
if($supportonu && !$tempdata){
	switchLog($getswitch['id'],'cron',$lang['emptytemponu']);
}
if($supportonu && is_array($tempdata)){
	foreach($tempdata as $idont => $getdata){
		$result = get_curl_api($getdata,true);
		if(isset($result)){
			$tempresult[$idont] = array_merge($getdata, $result);
		}else{
			$tempresult[$idont] = $getdata;
		}
	}
	sleep(2);	
	if(is_array($tempresult)){
		foreach($tempresult as $getdataont){
			if($getdataont['pon']=='epon'){
				$getmonitor->tempSaveEpon($getdataont);
			}
			if($getdataont['pon']=='gpon'){
				$getmonitor->tempSaveGpon($getdataont);
			}
		}
		$goont = true;
	}else{
		switchLog($getswitch['id'],'cron',$lang['checkapisystem']);
	}
	$getlistrxcheck = $getmonitor->getListSignal();
	if($goont && is_array($getlistrxcheck)){
		foreach($getlistrxcheck as $key => $getrxdata){
			if(isset($getrxdata)){
				$resRxApi = get_curl_api($getrxdata,true);
			}
			if(is_array($resRxApi) && is_array($getrxdata)){
				$resultrxarray[$key] = array_merge($getrxdata,$resRxApi);
			}else{
				$resultrxarray[$key] = $getrxdata;
			}
		}
	}
	if(is_array($resultrxarray)){
		foreach($resultrxarray as $getrxdataont){
			if($getrxdataont['pon']=='epon'){
				$getmonitor->tempSaveSignalEpon($getrxdataont);
			}
			if($getrxdataont['pon']=='gpon'){
				$getmonitor->tempSaveSignalGpon($getrxdataont);
			}
		}	
	}
}
sleep(2);
$supportport = $getmonitor->getSupportPort();
if($supportport){
	$indexport = $getmonitor->getPort();
	if(is_array($indexport)){
		$getmonitor->savePort($indexport);
	}
}
sleep(2);
if(is_array($tempresult))
	$getmonitor->UpdateInformationOlt();
if(!empty($getswitch['id'])){
	$executionTime = (int)microtime(true) - $starttime;
	$db->SQLupdate($PMonTables['switch'],['status'=>'no','timecheck'=>$executionTime,'timechecklast'=>(!empty($getswitch['timecheck'])?$getswitch['timecheck']:0)],['id'=>$getswitch['id']]);
}
?>
