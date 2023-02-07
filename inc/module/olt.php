<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$page = isset($_GET['page']) ? Clean::str($_GET['page']) : null;
$id = isset($_GET['id']) ? Clean::int($_GET['id']) : null;
if(!$id){
	$go->redirect('main');	
}
$dataSwitch = $db->Fast('switch','*',['id'=>$id]);
if(!$dataSwitch['id']){
	$go->redirect('main');	
}
$$SQLPortDevice = null;
$SQLPon = null;
if($dataSwitch['device']=='olt')
	$SQLPon = $db->Multi('switch_pon','*',['oltid'=>$id]);
if($dataSwitch['device']=='switch')
	$SQLPortDevice = $db->Multi('switch_port','*',['deviceid'=>$id]);
$Connection = getConnection($id);
$SQLcountONUTemp = $db->Multi('onus','*',['olt'=>$id]);
$metatags = array('title'=>$lang['pt_detail'].' '.$dataSwitch['place'],'description'=>$lang['pd_detail'],'page'=>'detail');
/*
	Panel control
*/
$panel ='<div class="moder-panel">';
if(count($SQLcountONUTemp) && $dataSwitch['device']=='olt')
	$panel .='<a href="'.$config['url'].'/?do=terminal&id='.$id.'"><img src="../style/img/m1.png">'.$lang['btn_olt_allonu'].'</a>';
if($dataSwitch['connect']=='yes' && $USER['class']>=4)
	$panel .='<a href="'.$config['url'].'/?do=detail&act='.$dataSwitch['device'].'&page=connect&id='.$id.'"><img src="../style/img/addconnect.png">'.$lang['btn_olt_allconn'].'</a>';
if(!empty($USER['class']) && $USER['class']>=6){
	if($dataSwitch['device']=='olt' && $dataSwitch['monitor']=='yes'){
		$panel .='<a href="'.$config['url'].'/?do=detail&act='.$dataSwitch['device'].'&page=monitoring&id='.$id.'"><img src="../style/img/uptime.png">'.$lang['edit_monitor'].'</a>';
	}
	$panel .='<a href="'.$config['url'].'/?do=setup&id='.$id.'"><img src="'.$config['url'].'/style/img/accept.png">'.$lang['setup'].'</a>';
}
if($dataSwitch['gallery']=='yes' && !empty($USER['class']) && $USER['class']>=6){
	$panel .='<a href="'.$config['url'].'/?do=detail&act=olt&id='.$id.'&page=photo"><img src="../style/img/photo-gallery.png">'.$lang['photo'].'</a>';
}
if(!empty($dataSwitch['snmprw']) && $USER['class']>=6 && $dataSwitch['oidid']==1){
	$panel .='<div id="ajaxonu"><a href="#" onclick="fun_ajax('.$id.','.$dataSwitch['oidid'].',\'rebootall\')"><img src="../style/img/rotate.png">'.$lang['rebootall'].'</a></div>';
}
$panel .='<div id="ajaxonu"><a href="/?do=switchlog&id='.$dataSwitch['id'].'" ><img src="../style/img/m3.png">'.$lang['log'].'</a></div>';
if(strtotime($dataSwitch['updates']) < strtotime(date('Y-m-d H:i:s').' - 1min')){
	$panel .='<div id="ajaxonu"><a href="#" onclick="ajaxcmd(5,'.$dataSwitch['id'].')"><img src="../style/img/rotate.png">'.$lang['checker'].'</a></div>';
}
$panel .='</div>';
if(!empty($dataSwitch['username']) && $USER['class']>=6 && $dataSwitch['oidid']==1){
#$panel .='<div class="telnet_device">';
// список всіх VLAN BDCOM
#$panel .='<span onclick="ajaxcmd(2,'.$id.')">'.$lang['listvlan'].'</span>';
#$panel .='<span onclick="ajaxcmd(7,'.$id.')">Перезавантажити OLT</span>';
#$panel .='<span onclick="ajaxcmd(8,'.$id.')">'.$lang['savecfg'].'</span>';
#$panel .='</div>';
}
/*
	Get list port
*/
if(!$page){
	/// Switch
	if($dataSwitch['device']=='switch'){
		if($SQLPortDevice){
			$tplRes .='<div class="connect-list-head"><h2>'.$lang['port'].'</h2></div><div class="list_pon_switch">';
				foreach($SQLPortDevice as $PortData => $Port){
					$tplRes .='<div class="port_switch"><div class="port_img"><img src="../style/img/port_'.$Port['typeport'].'_'.$Port['operstatus'].'.png"></div><div class="port_name_switch text_'.$Port['operstatus'].'">';
					if($Port['monitor']=='yes')
						$tplRes .='<img class="port_mark" src="../style/img/mark.png">';
					$tplRes .=''.$Port['nameport'].'</div></div>';
				}
			$tplRes .='</div>';
		}
	}
	$SQLPortDevice = $db->Multi('switch_port','*',['deviceid'=>$id]);
	$NextArray = array();
	if(count($SQLPortDevice)){
		foreach($SQLPortDevice as $PortDevice => $PortData){
			if(!empty($PortData['monitor']) && $PortData['monitor']=='yes')
				$SQLonusCount = $db->Simple('SELECT SUM(newin) as countin, deviceid FROM `switch_port_err` WHERE deviceid = '.$id.' AND llid = '.$PortData['llid'].' AND added  >= curdate()');
			if(!empty($SQLonusCount['countin']) && $SQLonusCount['countin']>100){
				$NewDataPort[$PortData['id']]['id'] = $PortData['id'];
				$NewDataPort[$PortData['id']]['name'] = $PortData['nameport'];
				$NewDataPort[$PortData['id']]['type'] = $PortData['typeport'];
				$NewDataPort[$PortData['id']]['llid'] = $PortData['llid'];
				$NewDataPort[$PortData['id']]['count'] = $SQLonusCount['countin'];
			}
			$NextArray[$PortData['id']]['id'] = $PortData['id'];
			$NextArray[$PortData['id']]['name'] = $PortData['nameport'];
			$NextArray[$PortData['id']]['type'] = $PortData['typeport'];
			$NextArray[$PortData['id']]['monitor'] = $PortData['monitor'];
			$NextArray[$PortData['id']]['status'] = $PortData['operstatus'];
			$NextArray[$PortData['id']]['timedown'] = $PortData['timedown'];
			$NextArray[$PortData['id']]['llid'] = $PortData['llid'];
			if(!empty($PortData['llid']) || $port['typeport']=='epon' || $port['typeport']=='gpon'){
				$dataPon[$id][$PortData['llid']]['name'] = $PortData['nameport'];
				$dataPon[$id][$PortData['llid']]['descr'] = $PortData['descrport'];
			}			
		}
		if(is_array($NewDataPort))
			$port_this_device = true;
	}
	/// OLT
	if($dataSwitch['device']=='olt'){
		if(count($SQLPon)){
			usort($SQLPon, function($a, $b){
				return ($a['sort'] - $b['sort']);
			});
			$tplRes .='<div class="connect-list-head"><h2>PON</h2></div><div class="list_pon">';
			foreach($SQLPon as $PortData => $Pon){
				$SQLbadonu = $db->SimpleWhile("SELECT idonu FROM onus WHERE rx BETWEEN '-28' AND '-40' AND olt = ".$id." AND portolt = ".$Pon['sfpid']."");
				$SQLbadonu = count($SQLbadonu);
				$tplRes .='<div class="style_pon"><a href="'.$config['url'].'/?do=terminal&id='.$id.'&port='.$Pon['id'].'" class="sc-psedN fLDHlO"></a>';			
				$tplRes .='<div class="sc-qQWDO frpbEt"><img src="../style/img/pon.png" style="max-width: 36px;">';
				if(!empty($Pon['count']))				
					$tplRes .='<span class="pon_support'.$Pon['support'].'">'.$Pon['count'].'</span>'; //	'.$Pon['support'].' - щоб показувало скільки можна
				$tplRes .='</div><div class="sc-qZtVr brvuoL">'.$Pon['pon'].'';
				if(!empty($dataPon[$id][$Pon['sfpid']]['descr'])){
					$tplRes .='<span class="olt-descr-pon">'.$dataPon[$id][$Pon['sfpid']]['descr'].'</span>';	
				}
				$css_load_bar = loadbarpon($Pon['support'],$Pon['count']);
				$tplRes .='<div class="loadpon"><div class="load '.$css_load_bar['css'].'" style="width: '.$css_load_bar['width'].'%;"></div><span></span></div>';
				$tplRes .='<div class="dropdown-content">';
				#$tplRes .='<div class="poptech"><span class="lang">Технологія</span><span class="types">EPON</span></div>';
				$tplRes .='<div class="poptech"><span class="lang">'.$lang['dilen'].'</span><span class="types">1:'.$Pon['support'].'</span></div>';
				$tplRes .='<div class="poptech"><span class="lang">'.$lang['sfpconn'].'</span><span class="types"> '.$Pon['count'].'</span></div>';
				if($SQLbadonu)
					$tplRes .='<div class="poptech"><span class="lang-red">'.$lang['portbadrx'].'</span><span class="types"> '.$SQLbadonu.'</span></div>';
				$tplRes .='</div></div></div>';
			}
			$tplRes .='</div>';
		}
		$getpage = 'pon';
	}
	if($port_this_device && $config['errorport']=='on'){
		$tplRes .='<div class="error-list-head"><h2>'.$lang['olterr'].'</h2></div><div class="list_pon">';
			foreach($NewDataPort as $PortARRAY => $DataARAAY){
				$tplRes .='<div class="port-error"><span class="name">'.$DataARAAY['name'].'</span><span class="err">+'.$DataARAAY['count'].'</span></div>';
			}
		$tplRes .='</div>';
	}
}
/// OLT + SWITCH
if($page=='photo' && $dataSwitch['gallery']=='yes'){
	$SQLSwPhoto = $db->Multi('switch_photo','*',['deviceid'=>$id]);
	$tplRes .='<div class="gallery-btn"><div onclick="ajaxaddphoto('.$id.')"><i class="fi fi-rr-picture"></i>'.$lang['addphoto'].'</div></div>';
	if(count($SQLSwPhoto)){
		$tplRes .='<div class="gallery">';
		foreach($SQLSwPhoto as $PhotoData){
			$tplRes .='<div class="photo" onclick="ajaxviewphoto('.$PhotoData['id'].')">';			
				$tplRes .='<div class="img"><img src="'.$config['url'].'/file/photo/'.$PhotoData['photo'].'"></div>';
				$tplRes .='<div class="name"><div class="date">'.$PhotoData['added'].'</div><h2>'.$PhotoData['name'].'</h2></div>';
			$tplRes .='</div>';			
		}
		$tplRes .='</div>';
	}else{
		$tplRes .='<div class="empty_connect"><i class="fi fi-rr-comment-info"></i>'.$lang['empty'].'</div>';
	}
}
/// OLT + SWITCH
if($page=='monitoring' && $dataSwitch['monitor']=='yes'){
	$dataPortSwitch = $db->Multi('switch_port','*',['deviceid'=>$dataSwitch['id']]);
	if(count($dataPortSwitch)){
		$tplRes .='<form action="/?do=send" method="post" id="formadd"><input name="act" type="hidden" value="saveportmonitor"><input name="id" type="hidden" value="'.$dataSwitch['id'].'"><div class="monitor-port-ajax"><div class="monitor-port-name portimg1">'.$lang['monport'].'<p>'.$lang['monportdescr'].'</p></div><div class="monitor-port-input">';
		foreach($dataPortSwitch as $PortData){
			$tplRes .='<div class="port '.($PortData['monitor']=='yes'?'selectport':'').'"><input class="checkcss" name="monitorport[]" value="'.$PortData['id'].'" type="checkbox" '.($PortData['monitor']=='yes'?'checked':'').'><b>'.$PortData['nameport'].'</b></div>';
		}
		$tplRes .='</div><div class="monitor-port-name portimg2">'.$lang['indescrerr'].'<p>'.$lang['indescrerr'].'</p></div><div class="monitor-port-input">';
		foreach($dataPortSwitch as $PortData){
			$tplRes .='<div class="port '.($PortData['error']=='yes'?'selectport':'').'"><input class="checkcss" name="monitorerr[]" value="'.$PortData['id'].'" type="checkbox" '.($PortData['error']=='yes'?'checked':'').'><b>'.$PortData['nameport'].'</b></div>';
		}
		$tplRes .='</div><div class="monitor-port-name portimg3">'.$lang['sennametg'].'<p>'.$lang['sennametgdescr'].'</p></div><div class="monitor-port-input">';
		foreach($dataPortSwitch as $PortData){
			$tplRes .='<div class="port '.($PortData['sms']=='yes'?'selectport':'').'"><input class="checkcss" name="monitortelegram[]" value="'.$PortData['id'].'" type="checkbox" '.($PortData['sms']=='yes'?'checked':'').'><b>'.$PortData['nameport'].'</b></div>';
		}
		$tplRes .='</div></div><button type="submit" form="formadd" class="monitorsave" value="submit">'.$lang['save'].'</button></form>';
	}
}
if($page=='connect' && $dataSwitch['connect']=='yes'){
	$SQLPortSFP = $db->Multi('switch_port','*',['deviceid'=>$id]);
	if(count($SQLPortSFP)){
		$ListPort = getAllPortDevice($SQLPortSFP);
		$hiddenUserPort = (isset($auth->member[0]['port']) && !empty($auth->member[0]['port']) ? unserialize($auth->member[0]['port']) : '');
		foreach($ListPort as $PortData => $resData){
			$IDBlock = idblock($PortData);
			$tplRes .='<table class="tableport" border="0" cellspacing="0" cellpadding="10" style="width:100%;"><tr><td class="name-port" colspan="5">'.$PortData.'';
			$tplRes .='<span style="cursor: pointer;" onclick="javascript: block_switch(\''.$id.$IDBlock.'\',\''.$auth->member[0]['id'].'\');"> ';
			$tplRes .='<img border="0" src="../style/img/'.($hiddenUserPort[$id.$IDBlock]=='hide'?'plus':'minus').'.gif" id="picb'.$id.$IDBlock.'" title="'.($hiddenUserPort[$id.$IDBlock]=='hide'?$lang['view']:$lang['hide']).'"></span></td></tr></table>';
			$tplRes .='<table id="sb'.$id.$IDBlock.'" style="display: '.($hiddenUserPort[$id.$IDBlock]=='hide'?'none':'block').';width:100%;" class="tableport" border="0" cellspacing="0" cellpadding="10" style="width: 99%;">';
			foreach($resData as $portID => $port){
				$connect = true;
				$tplRes .='<tr class="hover"><td class="td0 status-'.$port['operstatus'].' '.($port['operstatus']=='up'?'blink_status':'').'">'.$port['operstatus'].'<div class="pmon_new"></div></td>';
				$tplRes .='<td class="td1 name">';
				if($port['typeport']=='epon' || $port['typeport']=='gpon' && !empty($PonInf['sfpid'])){
					$PonInf = getPonPortOLt($id,$port['llid']);
					$tplRes .='<a href="/?do=terminal&id='.$id.'&port='.$PonInf['id'].'">';
				}
				$tplRes .='<h2>'.$port['name'].'';
				#if($port['sms']=='yes')
				#	$tplRes .='<div class="m2_inf"><i class="fi fi-rr-envelope"></i></div>';
				#if($port['monitor']=='yes')
				#	$tplRes .='<div class="m1_inf"><i class="fi fi-rr-exclamation"></i></div>';
				#if($port['error']=='yes')
				#	$tplRes .='<div class="m3_inf"><i class="fi fi-rr-bolt"></i></div>';
				#if($port['monitor']=='yes')
				#	$tplRes .='<div class="m4_inf"><i class="fi fi-rr-clock"></i>'.$port['updates'].'</div>';
				$tplRes .='</h2>';
				if($port['typeport']=='epon' || $port['typeport']=='gpon' && !empty($PonInf['sfpid'])){
					$tplRes .='</a>';
				}
				$tplRes .='<div class="descr">';
				#if($USER['class']>=5)				
				#	$tplRes .='	<span class="add" onclick="port(\'setup\','.$port['id'].')">'.$lang['edit_monitor'].'</span>';			
				if(!empty($port['descr']) && $USER['class']>=5)
					$tplRes .='	<span class="add" onclick="port(\'edit\','.$port['id'].')">'.$lang['edit_descr'].'</span>';
				if(!empty($port['descr'])){
					$tplRes .='<h3>'.$port['descr'].'</h3>';
				}else{
					if($USER['class']>=5)
						$tplRes .='	<span class="add" onclick="port(\'descr\','.$port['id'].')">'.$lang['add_descr'].'</span>';
				}
				$tplRes .='</div></td>';
				// PON статистика
				$tplRes .='<td class="td2">';
				if($port['typeport']=='gepon' || $port['typeport']=='gpon' || $port['typeport']=='epon'){
					if(!empty($PonInf['support']))
						$tplRes .= statsPonPort($PonInf,$port['typeport']);
				}
				$tplRes .='</td>';
				// помилки на портах
				$tplRes .='<td class="td3">';
				if($port['error']=='yes')
					$tplRes .= tplErrorPort($port['deviceid'],$port['llid']);
				$tplRes .='</td>';
				// список ону
				$tplRes .='<td class="tdempty">';
					if(!empty($Connection[$port['id']]['id'])){
						$tplRes .= getPortConnect($Connection,$port['id']);
						$connect = false;
					}else{
						if($port['typeport']!=='epon' || $port['typeport']!=='gpon' && !$PonInf['sfpid'] && $USER['class']>=5){
							$tplRes .= '<span class="connect-btn" onclick="ajaxconnect(\'add\','.$port['id'].');"><img src="../style/img/no-internet.png">'.$lang['connect'].'</span>';
						}
					}
					if($port['typeport']=='gepon' || $port['typeport']=='gpon' || $port['typeport']=='epon'){
						$UnitgetPon = $db->Multi('unitpontree','*',['portid'=>$port['id'],'deviceid'=>$id]);
						if(count($UnitgetPon)){
							$tplRes .= '<div class="devicetree">';
							foreach($UnitgetPon as $getPon => $PonTree){
								$tplRes .= '<a href="'.$config['url'].'/?do=pon&act=tree&id='.$PonTree['id'].'"><img src="../style/img/conn1.png">'.$PonTree['name'].'</a>';
							}
							$tplRes .= '</div>';
						}
					}
				$tplRes .='</td></tr>';
			}
			$tplRes .='</table>';
		}
	}else{
		$tplRes .='<span id="infEmpty">'.$lang['empty'].'</span>';
	}
}
/// OLT + SWITCH
if(!$page){
	if(is_array($NextArray)){
		foreach($NextArray as $IDMonitorPort => $MonitorPortData){
			if($MonitorPortData['status']=='down' && $MonitorPortData['monitor']=='yes'){
				$StatsPort[$IDMonitorPort]['status'] = $MonitorPortData['status'];
				$StatsPort[$IDMonitorPort]['name'] = $MonitorPortData['name'];
				$StatsPort[$IDMonitorPort]['timedown'] = $MonitorPortData['timedown'];
			}
		}
	}
	if(is_array($StatsPort) && $config['statusport']=='on'){
		$tplRes .='<div class="monitor-list-head"><h2>'.$lang['statport'].'</h2></div><div class="list_pon">';
			foreach($StatsPort as $IDMonitorPort => $MonitorPortData){
				$tplRes .='<div class="port-status status-port-'.$MonitorPortData['status'].'"><div class="port-status-name">'.$MonitorPortData['name'].'</div><div class="port-status-time"><b>Down</b>: '.aftertime($MonitorPortData['timedown']).'</div></div>';
			}
		$tplRes .='</div>';
	}
	if($config['statusport']=='on'){
		$DataStatusPort = $db->SimpleWhile('SELECT * FROM `'.$PMonTables['swlogport'].'` WHERE deviceid = '.$dataSwitch['id'].' AND added >= curdate()');
		if($dataSwitch['device']=='olt' && $DataStatusPort){
			$tplRes .='<div class="status-list-head"><h2>'.$lang['curport'].'</h2></div><div class="list_pon">';
				foreach($DataStatusPort as $IDport => $PortData){
					$tplRes .='<div class="port-status status-port-'.$PortData['status'].'"><div class="port-status-name">'.$NextArray[$PortData['portid']]['name'].'</div><div class="port-status-time">'.$PortData['added'].'</div></div>';
				}
			$tplRes .='</div>';
		}
	}
}
/// OLT 
if($getpage=='pon' && ($USER['class']>=4)){
	$SQLSfp = $db->Multi('connect_port','*',['curd'=>$dataSwitch['id']]);
	if(count($SQLSfp)){
		if(count($SQLPon) && $dataSwitch['connect']=='yes'){
			$tplRes .='<div class="connect-list-head"><h2>'.$lang['connects'].'</h2></div><div class="connect-list">';
			$tplRes .= getPortConnectSFP($dataSwitch['id']);
			$tplRes .='</div>';
		}
	}
}
$tpl->load_template('olt/block.tpl');
$tpl->set('{id}',$dataSwitch['id']);
$tpl->set('{script_device_ajax}','<script>ajaxdevicestatus('.$id.');</script>');
$tpl->set('{panel}',$panel);
$tpl->set('{countonu}',($dataSwitch['allonu']?'<span><img src="../style/img/online.png" style="height: 16px;">'.$lang['count_port'].'<b>'.$dataSwitch['allonu'].'</b></span>':''));
$tpl->set('{countport}',($SQLPon?'<span><img src="../style/img/port.png" style="height: 16px;">'.$lang['count_port_pon'].'<b>'.count($SQLPon).'</b></span>':''));
$tpl->set('{sn}',($dataSwitch['sn']?'<span><img src="../style/img/code.png">'.$lang['serial'].'<b>'.$dataSwitch['sn'].'</b></span>':''));
$tpl->set('{mac}',($dataSwitch['mac']?'<span><img src="../style/img/mac.png">MAC<b>'.$dataSwitch['mac'].'</b></span>':''));
$tpl->set('{interval}',($dataSwitch['typecheck']?'<span><img src="../style/img/checker.png">'.$lang['timeinterval'].':<b>'.$lang[$dataSwitch['typecheck']].'</b></span>':''));
$tpl->set('{updates}',($dataSwitch['updates']?'<span><img src="../style/img/on-time.png">'.$lang['timecheck'].':<b>'.$dataSwitch['updates'].'</b>'.(!empty($dataSwitch['timecheck'])?$dataSwitch['timecheck'].'sec':'').'</span>':''));
$tpl->set('{updates_port}',($dataSwitch['updates_port']?'<span><img src="../style/img/on-time.png">'.$lang['timecheckport'].':<b>'.$dataSwitch['updates_port'].'</b></span>':''));
$tpl->set('{updates_rx}',($dataSwitch['updates_rx']?'<span><img src="../style/img/on-time.png">'.$lang['timecheckrx'].':<b>'.$dataSwitch['updates_rx'].'</b></span>':''));
$tpl->set('{timecheck}',($dataSwitch['timecheck']?'<span><img src="../style/img/on-time.png">'.$lang['timework'].':<b>'.$dataSwitch['timecheck'].'</b> '.$lang['sec'].'</span>':''));
$tpl->set('{timechecklast}',($dataSwitch['timechecklast']?'<span><img src="../style/img/on-time.png">'.$lang['timeworklast'].':<b>'.$dataSwitch['timechecklast'].'</b> '.$lang['sec'].'</span>':''));
$tpl->set('{update}',$dataSwitch['update']);
$tpl->set('{updaterx}',$dataSwitch['update_rx']);
$tpl->set('{place}',$dataSwitch['place']);
$tpl->set('{netip}',($USER['class']>=4?$dataSwitch['netip']:'255.255.255.255'));
$tpl->set('{typesdevice}',$dataSwitch['device']);
$tpl->set('{photomodel}',$dataSwitch['img']);
$tpl->set('{firmware}',$dataSwitch['firmware']);
$tpl->set('{blockonline}',($blockonline?$blockonline:''));
$tpl->set('{blockoffline}',($blockoffline?$blockoffline:''));
$tpl->compile('block-content');
$tpl->clear();
$tpl->load_template('block/block.tpl');
$control = '';
if(!empty($USER['class']) && $USER['class']>=6){
	$control .='<span class="monitorsetup" onclick="ajaxcore(\'monitor\','.$dataSwitch['id'].');"><img src="../style/img/settings.png"></span>';
	$control .='<span class="monitorsetup" onclick="ajaxcore(\'delete\','.$dataSwitch['id'].');"><img src="../style/img/delet.png"></span>';
	if($dataSwitch['monitor']=='yes' && $dataSwitch['status']=='go' && !empty($dataSwitch['jobid']))
		$control .='<span class="monitorsetup"><img src="../style/img/play.png"></span>';
}
$tpl->set('{name}',$dataSwitch['inf'].' '.$dataSwitch['model'].''.$control);
$tpl->set('{result}',$tpl->result['block-content']);
$tpl->set('{pagerbottom}','');
$tpl->compile('block-olt');
$tpl->clear();
$tpl->load_template('olt/main.tpl');
$tpl->set('{listdevice}',$lang['alldevice']);
$tpl->set('{model}',$dataSwitch['place']);
$tpl->set('{result}',$tplRes);
$tpl->set('{block-content}',$tpl->result['block-olt']);
$tpl->set('{pagerbottom}','');
$tpl->compile('content');
$tpl->clear();
?>