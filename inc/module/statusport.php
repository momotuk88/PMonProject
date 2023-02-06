<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$metatags = array('title'=>'Моніторинг портів','description'=>'Моніторинг портів','page'=>'statusport');
$SQLPortDevice = $db->Multi('switch_port','*',['monitor'=>'yes','operstatus'=>'down']);
$SQLListDevice = ListSwitchMonitor();
$NextArray = array();
if(count($SQLPortDevice)){
	foreach($SQLPortDevice as $IDMonitorPort => $MonitorPortData){
		$tplResData .='<div class="port-status status-port-'.$MonitorPortData['operstatus'].'">';
		$tplResData .='<div class="port-status-name"><a href="/?do=detail&act=olt&id='.$MonitorPortData['deviceid'].'">'.$SQLListDevice[$MonitorPortData['deviceid']]['place'].' -> '.$MonitorPortData['nameport'].'</a></div>';
		$tplResData .='<div class="port-status-time"><b>Вимкнено</b>: '.aftertime($MonitorPortData['timedown']).'</div>';
		$tplResData .='</div>';
	}
}else{
	$tplResData='';
}
$tplRes .='<div class="monitor-list-head"><h2><i class="fi fi-rr-clock"></i>Моніторинг портів</h2></div>';
$tplRes .='<div class="list_pon_main">'.$tplResData.'</div>';
$result .='<div class="mainblocklite">'.$tplRes.'</div>';
$tpl->load_template('page-status-port.tpl');
$tpl->set('{result}',$result);
$tpl->compile('content');
$tpl->clear();
?>