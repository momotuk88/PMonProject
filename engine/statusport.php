<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
require ROOT_DIR.'/inc/init.monitor.php';
$getAllSwitch = $db->Multi('switch');
if(count($getAllSwitch)){
	foreach ($getAllSwitch as $switch) {
		$switcharray[$switch['id']]['place'] = $switch['place'];
		$switcharray[$switch['id']]['oid'] = $switch['oidid'];
	}
}else{
	die('add_new_switch');
}
/*
$getMonitorONU = $db->Multi($PMonTables['mononu']);
if(count($getMonitorONU)){
	foreach ($getMonitorONU as $onu) {
		$getONU = $db->Fast($PMonTables['onus'],'*',['idonu' => $onu['idonu']]);
		if(!empty($getONU['idonu'])){
			$getOID = $db->Fast('oid','*',['oidid' => $switcharray[$getONU['olt']]['oid'],'inf'=>'onu','pon'=>$getONU['type'],'types'=>'status']);
			if(!empty($getOID['oid'])){				
				$onuarray[$onu['id']]['oid'] = formatOID($getOID['oid'],$getONU['keyonu'],$getONU['zte_idport']);
				if(!empty($getOID['result'])){
					$onuarray[$onu['id']]['result'] = $getOID['result'];
				}
				$onuarray[$onu['id']]['sqlid'] = $onu['id'];
				$onuarray[$onu['id']]['idonu'] = $getONU['idonu'];
				$onuarray[$onu['id']]['olt'] = $getONU['olt'];
				$onuarray[$onu['id']]['idoid'] = $switcharray[$getONU['olt']]['oid'];
			}
		}
	}
	if(is_array($onuarray)){
		foreach ($onuarray as $onuid => $onuget) {
			$dataAPI = array('do' => 'oid','oidid' => $onuget['idoid'],'oid' => $onuget['oid'],'id' => $onuget['olt']);
			$resultMon[$onuid] = get_curl_api($dataAPI,true,10);
		}
	}
}
*/
$getAllPort = $db->Multi('switch_port','*',['monitor' => 'yes']);
if(count($getAllPort)){
	$arrayport = array();
	foreach ($getAllPort as $port) {
		if($switcharray[$port['deviceid']]['oid']==15){
			$types = 'port'.$port['typeport'];
		}else{
			$types = 'port';			
		}
		$getOID = $db->Fast('oid','*',['oidid' => $switcharray[$port['deviceid']]['oid'],'inf'=>'monitor','pon'=>$types,'types'=>'status']);
		if(!empty($getOID['oid'])){
			$arrayport[$port['id']]['name'] = $port['nameport'];
			if(!empty($port['descrport']))
				$arrayport[$port['id']]['descr'] = $port['descrport'];
			$arrayport[$port['id']]['portid'] = $port['id'];
			$arrayport[$port['id']]['id'] = $port['deviceid'];
			$arrayport[$port['id']]['monitor'] = $port['monitor'];
			if(!empty($port['sms']))
				$arrayport[$port['id']]['sms'] = $port['sms'];
			$arrayport[$port['id']]['place'] = $switcharray[$port['deviceid']]['place'];
			if(!empty($switcharray[$port['deviceid']]['netip']))
				$arrayport[$port['id']]['ip'] = $switcharray[$port['deviceid']]['netip'];
			$arrayport[$port['id']]['oidid'] = $switcharray[$port['deviceid']]['oid'];
			$arrayport[$port['id']]['status'] = $port['operstatus'];
			$arrayport[$port['id']]['result'] = unserialize($getOID['result']);
			$arrayport[$port['id']]['oid'] = str_replace('keyport',$port['llid'],$getOID['oid']);
		}
	}
	if(is_array($arrayport)) {
		foreach ($arrayport as $portid => $data){
			$result_port[$portid] = get_curl_api(array('do' => 'oid', 'oid' => $data['oid'], 'id' => $data['id']), true, 10);
		}
	}
	if(is_array($result_port) && is_array($arrayport)){
		foreach ($result_port as $id => $val){
			// HUAWEI EPON+GPON+ETH
			if($arrayport[$id]['oidid']==14) {
				$status = portstatusHuawei($val['result']);
			}else{
				$status = $val['result'];
			}
			savePortMonitor($arrayport[$id]['place'],$arrayport[$id]['name'],(!empty($arrayport[$id]['descr'])?$arrayport[$id]['descr']:''),$status,$arrayport[$id]['status'],$arrayport[$id]['portid'],$arrayport[$id]['id'],$arrayport[$id]['sms']);
		}
	}
}

$getAllPortErr = $db->Multi('switch_port','*',['monitor' => 'yes','error' => 'yes','operstatus' => 'up']);
if(count($getAllPortErr)){
	$listPon = array();
	foreach($getAllPortErr as $port) {
		$listPon[$port['id']]['id'] = $port['id'];
		$listPon[$port['id']]['deviceid'] = $port['deviceid'];
		$listPon[$port['id']]['place'] = $switcharray[$port['deviceid']]['place'];
		$listPon[$port['id']]['sms'] = $port['sms'];
		$listPon[$port['id']]['llid'] = $port['llid'];
		$listPon[$port['id']]['descrport'] = $port['nameport'].($port['descrport']?' ('.$port['descrport'].')':'').' ';
	}
	if(is_array($listPon)){
		foreach($listPon as $idPort => $dataSwitch){
			$resultErr[$idPort] = get_curl_api(array('do' => 'port','types' => 'error','keyport' => $dataSwitch['llid'],'id' => $dataSwitch['deviceid']),true,10);
		}
	}
	if(is_array($resultErr)){
		foreach($resultErr as $idPort => $dataResult){
			saveErrPort($listPon[$idPort]['deviceid'],$listPon[$idPort]['llid'],$dataResult,$listPon[$idPort]['descrport'],$listPon[$idPort]['place']);
		}
	}
}
PMonStats();
?>
