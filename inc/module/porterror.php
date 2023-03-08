<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$metatags = array('title'=>$lang['page_title_stats'],'description'=>$lang['page_title_descr'],'page'=>'porterror');
$id = (isset($_GET['id']) ? Clean::int($_GET['id']) : null);
$urlswitch ='';
if(count($checkLicenseSwitch)){
	if($id){
		$selectswitch = $id;	
	}else{
		$firstKey = array_key_first($checkLicenseSwitch);
		$selectswitch = $checkLicenseSwitch[$firstKey]['id'];
	}
	foreach($checkLicenseSwitch as $switch){
		$urlswitch .='<a href="/?do=porterror&id='.$switch['id'].'" class="'.($selectswitch==$switch['id']?'tab-active':'tab-url').'">'.$switch['place'].'</a>';		
	}
}
if($selectswitch){
	$sqlpondevice = $db->Multi($PMonTables['switchpon'],'*',['oltid'=>$selectswitch]);
	if(count($sqlpondevice)){
		foreach($sqlpondevice as $pon => $pn){
			$as[$pn['sfpid']]['llid'] = $pn['sfpid'];
			$as[$pn['sfpid']]['count'] = $pn['count'];
			$as[$pn['sfpid']]['online'] = $pn['online'];
			$as[$pn['sfpid']]['offline'] = $pn['offline'];
			$as[$pn['sfpid']]['support'] = $pn['support'];
		}
	}
	$sqlportdevice = $db->Multi($PMonTables['switchport'],'*',['deviceid'=>$selectswitch]);
	if(count($sqlportdevice)){
		foreach($sqlportdevice as $port => $pv){
			$ap[$pv['typeport']]['data'][$pv['id']]['id'] = $pv['id'];
			$ap[$pv['typeport']]['data'][$pv['id']]['name'] = $pv['nameport'];
			$ap[$pv['typeport']]['data'][$pv['id']]['monitor'] = $pv['monitor'];
			$ap[$pv['typeport']]['data'][$pv['id']]['status'] = $pv['operstatus'];
			$ap[$pv['typeport']]['data'][$pv['id']]['llid'] = $pv['llid'];
			$ap[$pv['typeport']]['data'][$pv['id']]['updates'] = $pv['updates'];
		}
	}
}
if(is_array($ap)){
	$page_error = '';
	foreach($ap as $dataport => $datavalue){
		$page_error .='<div class="port-sfp">Група '.$dataport.'</div>';
		if(is_array($datavalue['data'])){			
			foreach($datavalue['data'] as $pv => $vport){
				$page_error .='<div class="port-data">';
				$page_error .='<div class="port-data-status"><img src="../style/img/eth'.$vport['status'].'.png"></div>';
				$page_error .='<div class="port-data-name text-'.$vport['status'].'">'.$vport['name'].'</div>';
				$page_error .='<div class="port-data-updates">'.($vport['monitor']=='yes'?'<img src="../style/img/on-time.png">'.$vport['updates']:'').'</div>';
				$page_error .='<div class="port-data-error">';
				if($vport['monitor']=='yes'){
				$dataerror = $db->Simple("SELECT * FROM `switch_port_err` WHERE `llid` = '".$vport['llid']."' AND `deviceid` = '".$selectswitch."' ORDER BY `added` DESC LIMIT 1");
					if(is_array($dataerror)){
					$datacurrenterror = $db->Simple("SELECT sum(newin) as counterror FROM `switch_port_err` WHERE `llid` = '".$vport['llid']."' AND `deviceid` = '".$selectswitch."' AND added > DATE_ADD(NOW(), INTERVAL -1 DAY)");
					$page_error .='<span class="error-all">Всього:<span>'.$dataerror['inerror'].'</span></span>';
					if(!empty($datacurrenterror['counterror']))
						$page_error .='<span class="error-current">'.$lang['day_curent'].':<span>'.$datacurrenterror['counterror'].'</span></span>';
					if(!empty($dataerror['newin']))
						$page_error .='<span class="error-last"><span>+'.$dataerror['newin'].'</span></span>';
					}
				}
				$page_error .='</div></div>';
			}			
		}
	}
}
$result ='<div id="onu-speedbar"><a class="brmhref" href="/?do=device"><i class="fi fi-rr-apps"></i>'.$lang['alldevice'].'</a><span class="brmspan"><i class="fi fi-rr-angle-left"></i>'.$lang['statspage'].'</span></div><div class="card" style="margin: 0;"><div class="main-gr-tab">'.$urlswitch.'</div><div class="page-error">'.$page_error.'</div>';
$tpl->load_template('main/main.tpl');
$tpl->set('{block-main}','<div class="mainadmin">'.$result.'</div>');
$tpl->compile('content');
$tpl->clear();
?>