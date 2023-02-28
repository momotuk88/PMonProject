<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$insert = $insert ?? null;
$timeout = 100000;
$retries = 5;
require ROOT_DIR.'/inc/init.monitor.php';
$sqlselectswitch = $db->Multi('switch','id,netip,snmpro,ping',['monitor'=>'yes']);
if(count($sqlselectswitch)){
	snmp_set_quick_print(false);
	snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
	foreach($sqlselectswitch as $arr){
		$starttime = microtime(true);
		$data = @snmp2_get($arr['netip'],$arr['snmpro'],'1.3.6.1.2.1.1.1.0',$timeout,$retries);
		if($data){
			$insert['time'] = sprintf('%.2f',(microtime(true) - $starttime));
			$insert['datetime'] = date('Y-m-d H:i:s');
			$insert['system'] = $arr['id'];
			$insert['status'] = 1;
			$updates['timeping'] = date('Y-m-d H:i:s');	
			if($arr['ping']=='down'){
				$updates['ping'] = 'up';	
			}
		}else{
			if($arr['ping']=='up'){
				$insert['time'] = 0;
				$insert['datetime'] = date('Y-m-d H:i:s');
				$insert['system'] = $arr['id'];
				$insert['status'] = 2;			
				$updates['ping'] = 'down';
				$updates['timeping'] = date('Y-m-d H:i:s');	
				#$updates['monitor'] = 'no';				
			}			
		}
		if(is_array($insert))
			$db->SQLinsert($PMonTables['pingstats'],$insert);
		if(is_array($updates))
			$db->SQLupdate($PMonTables['switch'],$updates,['id' => $arr['id']]);
	}
}
sleep(3);
$sqlselectswitchafter = $db->Multi('switch','id,netip,snmpro,oidid',['monitor'=>'yes']);
if(count($sqlselectswitchafter)){
foreach($sqlselectswitchafter as $switch){
	if($switch['oidid']==2 || $switch['oidid']==3 || $switch['oidid']==1){
		$res_snmp = get_curl_api(array('do' => 'device','id' => $switch['id']), true, 10);
		if(is_array($res_snmp['result'])){
			foreach($res_snmp['result'] as $type => $value) {
				/// BDCOM TEMP+CPU
				if($switch['oidid']==2 || $switch['oidid']==1 || $switch['oidid']==3){
					if($type=='cpu')
						$arrhealth['cpu'] = $value;
					if($type=='temp') 
						$arrhealth['temp'] = $value;
					if($type=='temp' || $type=='cpu'){
						$arrays = serialize($arrhealth);
						$health['types'] = 'com1';
					}
				}
				if($arrays && !empty($health['types'])){
					$dataswitch[$switch['id']]['data'] = $arrays;
					$dataswitch[$switch['id']]['types'] = $health['types'];
				}
			}
		}
	}
}
if(is_array($dataswitch)){
	foreach($dataswitch as $switch => $value){
		if($switch && !empty($value['data'])){
			$db->query("INSERT INTO `".$PMonTables['monitoring']."` (`datetime`,`types`,`values`,`deviceid`) VALUES ('".date('Y-m-d H:i:s')."','".$value['types']."','".$value['data']."','".(int)$switch."')");
		}
	}
}
}
?>
