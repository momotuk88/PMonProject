<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
function inserUnitDevice($id){
	$insert='
		<div class="insert-list">
			<div class="img"><img src="/style/insert/switch.png"></div>
			<div class="dev-switch"><h2>TPLINK 34-34</h2></div>
		</div>		
		<div class="insert-list">
			<div class="img"><img src="/style/insert/switch.png"></div>
			<div class="dev-switch"><h2>TPLINK 34-34</h2></div>
		</div>';
		return $insert;
}
function getListConnection($id){
	global $db;
	$SQLport = $db->Multi('switch_port','*',['deviceid'=>$id]);
	if(count($SQLport)){
		foreach($SQLport as $port){
			$array_port[$port['id']]['id'] = $port['id'];
			$array_port[$port['id']]['nameport'] = $port['nameport'];
			$array_port[$port['id']]['descrport'] = $port['descrport'];
			$array_port[$port['id']]['operstatus'] = $port['operstatus'];
		}
	}
	$SQLconnect = $db->Multi('connectport','*',['curd'=>$id]);
	if(count($SQLconnect)){
		$array_conn = array();
		foreach($SQLconnect as $connect){
			$SQLconnectDevice = $db->Fast('switch_port','*',['id'=>$connect['connp']]);
			if(!empty($SQLconnectDevice['id'])){
				$array_conn[$connect['curp']]['id'] = $SQLconnectDevice['id'];
				$array_conn[$connect['curp']]['nameport'] = $SQLconnectDevice['nameport'];
				$array_conn[$connect['curp']]['descrport'] = $SQLconnectDevice['descrport'];
				$array_conn[$connect['curp']]['operstatus'] = $SQLconnectDevice['operstatus'];
			}
		}
	}
	foreach($array_port as $p){		
	$l .='<div class="port">';
		$l .='<span class="name">'.$p['nameport'];
		if(!empty($p['descrport']))
			$l .='<b>'.$p['descrport'].'</b>';
		$l .='</span>';
		$l .='<span class="cable">';
		if(!empty($array_conn[$p['id']]['id'])){
			$l .='<img src="/style/img/connect.png">';
		}
		$l .='</span>';
		$l .='<span class="connect">';
		if(!empty($array_conn[$p['id']]['id'])){
			$l .='<span class="connectport">';
			$l .= $array_conn[$p['id']]['nameport'];
			if(!empty($array_conn[$p['id']]['descrport']))
				$l .='<b>'.$array_conn[$p['id']]['descrport'].'</b>';
			$l .='</span>';
		}
		$l .='</span>';
	$l .='</div>';	
	}
	return $l;					
}
