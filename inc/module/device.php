<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$cssgroups = '';
$view = (isset($_GET['view']) ? Clean::str($_GET['view']) : null);
if(!empty($USER['id']) && $view=='list' || $view=='block'){
	if($view=='list'){
		$sqlview = 'yes';
		$viewstyle = 'list';
	}else{
		$sqlview = 'no';
		$viewstyle = 'block';
	}
	$db->SQLupdate('users',['viewlist'=>$sqlview],['id'=>$USER['id']]);
}else{
	if($USER['viewlist']=='yes'){
		$viewstyle = 'list';	
	}elseif($USER['viewlist']=='no'){
		$viewstyle = 'block';		
	}else{
		$viewstyle = 'list';	
	}
}
if($viewstyle=='block'){
	$SQLgroups = $db->Multi('groups');
	if(count($SQLgroups)){
		$cssgroups .= '<div class="list_gropus_css">';
		foreach($SQLgroups as $groups){
			$cssgroups .= '<a class="link_group" href="/?do=device&group='.$groups['id'].'"><i class="fi fi-rr-hastag"></i>'.$groups['name'].'</a>';
		}
		$cssgroups .= '</div>';
	}
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
$pagerlink = '';
if (!empty($addparam)) {
    if (!empty($pagerlink))
        $addparam = $addparam . $pagerlink;
} else {
    $addparam = $pagerlink;
}
$SQLCount = $db->Multi('switch','*',$Where);
if(count($SQLCount)){
	$array_error = array();
	foreach($SQLCount as $Dev){
		if(!empty($Dev['id']) && $Dev['monitor']=='yes')
		$SQLonusCount = $db->SimpleWhile('SELECT SUM(newin) as countin, deviceid FROM `switch_port_err` WHERE deviceid = '.$Dev['id'].' AND added  >= curdate()');
		$sqlnewonutoday = $db->SimpleWhile('SELECT idonu FROM `onus` WHERE olt = '.$Dev['id'].' AND added  >= curdate()');
		if(!empty($SQLonusCount[0]['countin']) && $SQLonusCount[0]['countin']>=100)
			$array_error[$Dev['id']]['count'] = $SQLonusCount[0]['countin'];
		if(count($sqlnewonutoday))
			$arraynewonutoday[$Dev['id']]['count'] = count($sqlnewonutoday);
	}
}
$pagecount = $config['countviewpageswitch'];
list($pagertop, $pagerbottom, $limit, $offset) = pager($pagecount,count($SQLCount),'/?do=device'.$addparam);
$SQLDevice = $db->Multi('switch','*',$Where,null,$offset,$limit);
if(count($SQLDevice)){
	foreach($SQLDevice as $Device){
		if($viewstyle=='block'){
			$tpl->load_template('device/block.tpl');
			$tpl->set('{url}','/?do=detail&act='.$Device['device'].'&id='.$Device['id']);
			$tpl->set('{name}',$Device['place']);
			$SQLcountONU = $db->Multi('onus','*',['olt'=>$Device['id']]);
			$SQLcountONUonline = $db->Multi('onus','*',['olt'=>$Device['id'],'status'=>1]);
			$SQLcountPORT = $db->Multi('switch_port','*',['deviceid'=>$Device['id']]);
			$SQLcountPON = $db->Multi('switch_pon','*',['oltid'=>$Device['id']]);
			$SQLbadonu = $db->SimpleWhile("SELECT idonu FROM onus WHERE rx BETWEEN '-".$config['badsignalstart']."' AND '-".$config['badsignalend']."' AND olt = ".$Device['id']);
			$SQLcountONUoffe = (int)count($SQLcountONU)-count($SQLcountONUonline);
			$tpl->set('{time}',aftertime($Device['updates']));
			$tpl->set('{mon}',($Device['monitor']=='yes'?'<span class="mon_on">on</span>':'<span class="mon_off">off</span>'));
			$tpl->set('{countonu}',(count($SQLcountONU)?'<div class="css10"><span>ONU</span><b>'.count($SQLcountONU).'</b></div>':''));
			$tpl->set('{countonuon}',(count($SQLcountONUonline)?'<div class="css11"><span>On</span><b>'.count($SQLcountONUonline).'</b></div>':''));
			$tpl->set('{countonuoff}',($SQLcountONUoffe?'<div class="css12"><span>Off</span><b>'.$SQLcountONUoffe.'</b></div>':''));
			$tpl->set('{counport}','<div class="css13"><span>Port</span><b>'.($SQLcountPON?count($SQLcountPON).'/':0).''.($SQLcountPORT?count($SQLcountPORT):'').'</b></div>');
			$tpl->set('{counbadrx}',(count($SQLbadonu)?'<div class="css14"><span>RX</span><b>'.count($SQLbadonu).'</b></div>':''));
			$tpl->set('{model}','<i class="fi fi-rr-database"></i> '.$Device['inf'].' '.$Device['model'].'');
			$tpl->compile('device');
			$tpl->clear();			
		}elseif($viewstyle=='list'){
			$tpl->load_template('device/list.tpl');
			$tpl->set('{url}','/?do=detail&act='.$Device['device'].'&id='.$Device['id']);
			$SQLcountONU = $db->Multi('onus','*',['olt'=>$Device['id']]);
			if(!empty($Device['groups']) && isset($Device['groups']) && $Device['groups']>0)
				$Group = $db->Fast('groups','*',['id'=>$Device['groups']]);
			$SQLcountONUonline = $db->Multi('onus','*',['olt'=>$Device['id'],'status'=>1]);
			$SQLcountPORT = $db->Multi('switch_port','*',['deviceid'=>$Device['id']]);
			$SQLcountPON = $db->Multi('switch_pon','*',['oltid'=>$Device['id']]);
			$SQLbadonu = $db->SimpleWhile("SELECT idonu FROM onus WHERE rx BETWEEN '-".$config['badsignalstart']."' AND '-".$config['badsignalend']."' AND olt = ".$Device['id']);
			$tpl->set('{info}',infstatus($Device['monitor'],count($SQLcountONU)));
			$tpl->set('{svg_icon}',($Device['device']=='olt' ? $svg_full_box:$svg_min_box));
			$tpl->set('{todayonu}',(!empty($arraynewonutoday[$Device['id']]['count'])?'<span class="todayonu">+'.$arraynewonutoday[$Device['id']]['count'].'</span>':''));
			$tpl->set('{place}',$Device['place']);
			$tpl->set('{location}',(!empty($Device['locationname'])?$Device['locationname']:''));
			if($Device['groups']>0 && !empty($Group['name'])){
				$tpl->set('{group}','<a href="/?do=device&group='.$Group['id'].'">'.$Group['name'].'</a>');
			}else{
				$tpl->set('{group}','');
			}
			$tpl->set('{netip}',($USER['class']>=4 && !empty($USER['class']) ?($config['viewipswitch']=='on' ? $Device['netip'] : ''):''));
			$SQLcountONUoffline = (int)(count($SQLcountONU) ? count($SQLcountONU) : 0)-(count($SQLcountONUonline) ? count($SQLcountONUonline) : 0);
			$tpl->set('{onus}','<div class="onu_stats">'.(isset($SQLbadonu)?'<div class="berr">'.count($SQLbadonu).' '.$lang['listrxdescr'].'</div>':'').'	'.(!empty($array_error[$Device['id']]['count']) ? '<div class="berr">+'.$array_error[$Device['id']]['count'].' '.$lang['errdescr'].'</div>':'').'</div>');
			$tpl->set('{monitor_status}',($Device['monitor']=='yes'?'<span class="olt_conn_on">'.$lang['descron'].'</span>':'<span class="olt_conn_off">'.$lang['descroff'].'</span>'));
			$tpl->set('{conn_status}',($Device['connect']=='yes'?'<span class="olt_conn_on">'.$lang['descron'].'</span>':'<span class="olt_conn_off">'.$lang['descroff'].'</span>'));
			$tpl->set('{time}','<span>'.aftertime($Device['updates']).'</span>');
			$tpl->set('{port}','<b>'.$lang['descrport'].''.($SQLcountPON?' Pon/Port':'').':</b> '.($SQLcountPON?count($SQLcountPON).'/':'').''.($SQLcountPORT?count($SQLcountPORT):'--'));
			$tpl->set('{countonu}','<b>'.$lang['descronus'].':</b> '.($SQLcountONU?count($SQLcountONU):'').'<br><b>'.$lang['online'].'/'.$lang['offline'].':</b> '.(count($SQLcountONUonline)?count($SQLcountONUonline).'/':'--/').''.($SQLcountONUoffline?$SQLcountONUoffline:'--').'');
			$tpl->set('{model}',$Device['inf']);
			$tpl->set('{firmware}',$Device['model']);
			$tpl->compile('device');
			$tpl->clear();
		}
	}
}else{
	$go->go('/?do=add&act=all');	
}
$tpl->load_template('device/main-'.$viewstyle.'.tpl');
$tpl->set('{activeblock}',($viewstyle=='block'?'active':''));
$tpl->set('{activelist}',($viewstyle=='list'?'active':''));
$tpl->set('{groups}',$cssgroups);
$tpl->set('{result}',$tpl->result['device']);
$tpl->set('{pagerbottom}',$pagertop);
$tpl->compile('block-main');
$tpl->clear();
$tpl->load_template('main/main.tpl');
$tpl->set('{block-main}',$tpl->result['block-main']);
$tpl->compile('content');
$tpl->clear();
?>