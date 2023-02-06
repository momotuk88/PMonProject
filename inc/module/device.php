<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}

$metatags = array('title'=>$lang['pl_device'],'description'=>$lang['pd_device'],'page'=>'device');
if($act=='olt'){
	$addparam .= '&act=olt';
	$Where = ['device'=>'olt'];
}elseif($act=='switch'){
	$addparam .= '&act=switch';
	$Where = ['device'=>'switch'];	
}elseif($act=='ups'){
	$addparam .= '&act=ups';
	$Where = ['device'=>'ups'];	
}else{
	$urlPager = '';
	$Where = null;	
}
$group = isset($_GET['group']) ? Clean::int($_GET['group']) : null;
if($group){
	$addparam .= '&group='.$group;
	$Where = ['groups'=>$group];		
}
if (!empty($addparam)) {
    if (!empty($pagerlink))
        $addparam = $addparam . $pagerlink;
} else {
    $addparam = $pagerlink;
}
$SQLCount = $db->Multi('switch','*',$Where);
if(count($SQLCount)){
	foreach($SQLCount as $Dev){
		if(!empty($Dev['id']) && $Dev['monitor']=='yes')
		$SQLonusCount = $db->SimpleWhile('SELECT SUM(newin) as countin, deviceid FROM `switch_port_err` WHERE deviceid = '.$Dev['id'].' AND added  >= curdate()');
		if(!empty($SQLonusCount[0]['countin']) && $SQLonusCount[0]['countin']>=100)
			$array_error[$Dev['id']]['count'] = $SQLonusCount[0]['countin'];
	}
}
list($pagertop, $pagerbottom, $limit, $offset) = pager(20,count($SQLCount),'/?do=device'.$addparam);
$SQLDevice = $db->Multi('switch','*',$Where,null,$offset,$limit);
if(count($SQLDevice)){
	foreach($SQLDevice as $Device){
		$tpl->load_template('device/list.tpl');
		$tpl->set('{url}','/?do=detail&act='.$Device['device'].'&id='.$Device['id']);
		$SQLcountONU = $db->Multi('onus','*',['olt'=>$Device['id']]);
		if(!empty($Device['groups']) && isset($Device['groups']) && $Device['groups']>0)
			$Group = $db->Fast('groups','*',['id'=>$Device['groups']]);
		$SQLcountONUonline = $db->Multi('onus','*',['olt'=>$Device['id'],'status'=>1]);
		$SQLcountPORT = $db->Multi('switch_port','*',['deviceid'=>$Device['id']]);
		$SQLcountPON = $db->Multi('switch_pon','*',['oltid'=>$Device['id']]);
		$SQLbadonu = $db->SimpleWhile("SELECT idonu FROM onus WHERE rx BETWEEN '-28' AND '-40' AND olt = ".$Device['id']."");
		$tpl->set('{info}',infstatus($Device['monitor'],count($SQLcountONU)));
		$tpl->set('{svg_icon}',($Device['device']=='olt' ? $svg_full_box:$svg_min_box));
		$tpl->set('{place}',$Device['place']);
		$tpl->set('{location}',(!empty($Device['locationname'])?$Device['locationname']:''));
		if($Device['groups']>0 && !empty($Group['name'])){
		$tpl->set('{group}','<a href="/?do=device&group='.$Group['id'].'">'.$Group['name'].'</a>');
		}else{
		$tpl->set('{group}','');
		}
		$tpl->set('{netip}',($USER['class']>=4?$Device['netip']:'255.255.255.255'));
		$ONLINE = count($SQLcountONUonline);
		$SQLcountONUoffline = (int)count($SQLcountONU)-count($SQLcountONUonline);
		$tpl->set('{onus}','<div class="onu_stats">
		
		'.(count($SQLbadonu)?'<div class="berr">'.count($SQLbadonu).' погані сигнали</div>':'').'
		'.($array_error[$Device['id']]['count'] ? '<div class="berr">+'.$array_error[$Device['id']]['count'].' помилок</div>':'').'
		
		</div>');
		$tpl->set('{monitor_status}',($Device['monitor']=='yes'?'<span class="olt_conn_on">вкл</span>':'<span class="olt_conn_off">викл</span>'));
		$tpl->set('{conn_status}',($Device['connect']=='yes'?'<span class="olt_conn_on">вкл</span>':'<span class="olt_conn_off">викл</span>'));
		$tpl->set('{time}','<span>'.aftertime($Device['updates']).'</span>');
		$tpl->set('{port}','<b>Портів'.($SQLcountPON?' Pon/Port':'').':</b> '.($SQLcountPON?count($SQLcountPON).'/':'').''.($SQLcountPORT?count($SQLcountPORT):'--').($SQLcountONU ? ' <br><b>К-ть ONT:</b> '.count($SQLcountONU):''));
		$tpl->set('{countonu}','<b>К-ть ONT:</b> '.($SQLcountONU?count($SQLcountONU):'').'<br><b>Онлайн/Оффлайн:</b> '.(count($SQLcountONUonline)?count($SQLcountONUonline).'/':'--/').''.($SQLcountONUoffline?$SQLcountONUoffline:'--').'');
		$tpl->set('{model}',$Device['inf']);
		$tpl->set('{firmware}',$Device['model']);
		$tpl->compile('device');
		$tpl->clear();			
	}
}else{
	$go->go('/?do=add&act=all');	
}
$tpl->load_template('device/main.tpl');
$tpl->set('{urladd}',(checkAccess(6)?'<div class="navigation mbottom20"><a class="deviceadd" href="'.$config['url'].'/?do=add">'.$lang['add'].'</a></div>':''));
$tpl->set('{result}',$tpl->result['device']);
$tpl->set('{pagerbottom}',$pagertop);
$tpl->compile('block-main');
$tpl->clear();
$tpl->load_template('main/main.tpl');
$tpl->set('{block-main}',$tpl->result['block-main']);
$tpl->compile('content');
$tpl->clear();
?>