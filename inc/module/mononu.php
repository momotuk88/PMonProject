<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$sqlmonitoronu = $db->SimpleWhile('SELECT onus.idonu as id, onus.type, onus.inface, onus.status, onus.olt, onus.online, onus.offline, monitoronu.monitor, monitoronu.name from onus, monitoronu where onus.idonu = monitoronu.idonu ORDER BY monitoronu.monitor ASC, onus.status DESC');
if(count($sqlmonitoronu)){
	foreach($sqlmonitoronu as $mononu){
		$tpl->load_template('mononu/list.tpl');
		$tpl->set('{id}',$mononu['id']);
		$tpl->set('{name}',$mononu['name']);
		$tpl->set('{url}','?do=onu&id='.$mononu['id']);
		if($mononu['status']==1){
			$tpl->set('{timestatus}',$mononu['online']);
			$tpl->set('{cssstatus}','upmononu');
		}else{
			$tpl->set('{timestatus}',aftertime($mononu['offline']));
			$tpl->set('{cssstatus}','downmononu');
		}
		$tpl->compile('mononu');
		$tpl->clear();			
	}
}else{
	$tpl->load_template('mononu/empty.tpl');
	$tpl->set('{result}','');
	$tpl->compile('mononu');
	$tpl->clear();	
}
$tpl->load_template('mononu/main.tpl');
$tpl->set('{result}',$tpl->result['mononu']);
$tpl->set('{speedbar}','<div id="onu-speedbar"><span class="brmspan"><i class="fi fi-rr-thumbtack"></i>'.$lang['monitor_onu'].'</span></div>');
$tpl->compile('content');
$tpl->clear();
?>