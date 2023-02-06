<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
require ROOT_DIR.'/inc/init.monitor.php';
$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
$result_port = null;
if($id){
	$getSwitch = $db->Fast('switch','*',['id'=>$id]);
	if(!empty($getSwitch['id'])){	
		$getMonitor = new Monitor($getSwitch['id'],$getSwitch['class']);
		$IndexData = $getMonitor->start();
		$IndexPort = $getMonitor->getPort();
		if(is_array($IndexPort))
			$getMonitor->savePort($IndexPort);
		sleep(2);
		$getAllPort = $db->Multi('switch_port','*',['deviceid' => $getSwitch['id']]);
		if(count($getAllPort)){
			$getOID = $db->Fast('oid','*',['oidid' => $getSwitch['oidid'],'inf'=>'monitor','pon'=>'port','types'=>'status']);
			$arrayport = array();
			foreach ($getAllPort as $port) {
				if(!empty($getOID['oid'])){
					$arrayport[$port['id']]['name'] = $port['nameport'];
					if(!empty($port['descrport']))
						$arrayport[$port['id']]['descr'] = $port['descrport'];
					$arrayport[$port['id']]['portid'] = $port['id'];
					$arrayport[$port['id']]['deviceid'] = $port['deviceid'];
					$arrayport[$port['id']]['llid'] = $port['llid'];
					$arrayport[$port['id']]['status'] = $port['operstatus'];
					$arrayport[$port['id']]['result'] = unserialize($getOID['result']);
					$arrayport[$port['id']]['oid'] = str_replace('keyport',$port['llid'],$getOID['oid']);
				}
			}
			if(is_array($arrayport)) {
				foreach ($arrayport as $portid => $data){
					$result_port[$portid] = get_curl_api(array('do' => 'oid', 'oid' => $data['oid'], 'id' => $getSwitch['id']), true, 10);
				}
			}
			if(is_array($result_port)){
				foreach ($result_port as $id => $val){
					if(preg_match('/INT/i',$val['result'])){
						$result = clInteger($val['result']);
					}else{
						$result = $val['result'];
					}
					if(!preg_match('/up/i',$val['result']) || !preg_match('/down/i',$val['result'])){
						if(!empty($getOID['result'])){
							$getSetupResult = unserialize($getOID['result']);
							$SQLstatus = $getSetupResult[$result];
						}
					}else{
						$SQLstatus = $result;
					}
					if(!preg_match('/up/i',$SQLstatus) || !preg_match('/down/i',$SQLstatus)){
						$db->SQLupdate('switch_port',['operstatus'=>$SQLstatus],['id'=>$id]);
					}
				}
			}
		}
	}
}
PMonStats();
?>
