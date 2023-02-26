<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$metatags = array('title'=>'Моніторинг портів','description'=>'Моніторинг портів','page'=>'statusport');
$SQLPortDevice = $db->Multi('switch_port','*',['monitor'=>'yes','operstatus'=>'down']);
$SQLListDevice = ListSwitchMonitor();
$NextArray = array();
if(count($SQLPortDevice)){
	$tplresult = '';
	foreach($SQLPortDevice as $IDMonitorPort => $MonitorPortData){
		$tplresult .='<div class="port-status status-port-'.$MonitorPortData['operstatus'].'">';
		$tplresult .='<div class="port-status-name"><a href="/?do=detail&act=olt&id='.$MonitorPortData['deviceid'].'">'.$SQLListDevice[$MonitorPortData['deviceid']]['place'].' -> '.$MonitorPortData['nameport'].'</a></div>';
		$tplresult .='<div class="port-status-time"><b>Вимкнено</b>: '.aftertime($MonitorPortData['timedown']).'</div>';
		$tplresult .='</div>';
	}
}else{
	$tplresult = '-----';
}
$result ='<div class="mainblocklite"><div class="monitor-list-head"><h2><i class="fi fi-rr-clock"></i>'.$lang['monitorport'].'</h2></div><div class="list_pon_main">'.$tplresult.'</div></div>';
$tpl->load_template('page-status-port.tpl');
$tpl->set('{result}',$result);
$tpl->compile('content');
$tpl->clear();
?>