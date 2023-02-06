<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$id = isset($_GET['id']) ? Clean::int($_GET['id']) : null;
if(!$id)
	$go->redirect('main');	
$dataONT = $db->Fast('onus','*',['idonu'=>$id]);
$metatags = array('title'=>$dataONT['type'].' '.$dataONT['inface'].' '.$lang['pt_onu'],'description'=>$lang['pd_onu'],'page'=>'onu');
if(!$dataONT['idonu'])
	$go->redirect('main');	
$dataOLT = $db->Fast('switch','*',['id'=>$dataONT['olt']]);
if(!$dataOLT['id'])
	$go->redirect('main');	
if($config['tag']=='on'){
	if($dataONT['tag']){
		$tpl->load_template('onu/edittag.tpl');
		$tpl->set('{id}',$id);
		$tpl->set('{edit}',$lang['edit']);
		$tpl->set('{marker}',$lang['marker']);
		$tpl->set('{tag}',$dataONT['tag']);
		$tpl->compile('tag');
		$tpl->clear();	
	}else{
		$tpl->load_template('onu/addtag.tpl');
		$tpl->set('{id}',$id);
		$tpl->set('{save}',$lang['save']);
		$tpl->set('{marker}',$lang['marker']);
		$tpl->compile('tag');
		$tpl->clear();	
	}
}
if($config['billing']=='on'){
	if($dataONT['uid']){
		$tpl->load_template('onu/editbilling.tpl');
		$tpl->set('{id}',$id);
		$tpl->set('{dogovor}',$lang['dogovor']);
		$tpl->set('{edit}',$lang['edit']);
		$tpl->set('{uid}',$dataONT['uid']);
		$tpl->compile('billing');
		$tpl->clear();	
	}else{
		$tpl->load_template('onu/addbilling.tpl');
		$tpl->set('{id}',$id);
		$tpl->set('{dogovor}',$lang['dogovor']);
		$tpl->set('{save}',$lang['save']);
		$tpl->compile('billing');
		$tpl->clear();
	}
}
if($config['comment']=='on'){
	if($dataONT['comments']){
		$tpl->load_template('onu/editcomm.tpl');
		$tpl->set('{id}',$id);
		$tpl->set('{save}',$lang['save']);
		$tpl->compile('comments');
		$tpl->clear();
	}else{
		$tpl->load_template('onu/addcomm.tpl');
		$tpl->set('{id}',$id);
		$tpl->set('{commentar}',$lang['commentar']);
		$tpl->set('{save}',$lang['save']);
		$tpl->compile('comments');
		$tpl->clear();
	}
}
$monitoronu = '';
$dataMonONT = $db->Fast('monitoronu','*',['idonu'=>$id]);
if(!empty($dataMonONT['id'])){
	$monitoronu = '<span class="monitoronus"><div class="blob"></div>Моніториться</span>';
}
$logonu = ListOnuLog($dataONT['idonu'],$dataOLT['id']);
$tpl->load_template('onu/main.tpl');
$tpl->set('{id}',$id);
if(!empty($dataONT['mac']))
	$mac_ont = '<span class="n">MAC</span><span class="m">'.$dataONT['mac'].'</span>';
if(!empty($dataONT['sn']))
	$sn_ont = '<span class="n">SN</span><span class="m">'.$dataONT['sn'].'</span>';
$checkONU = $db->Fast($PMonTables['mononu'],'*',['idonu'=>$id]);
if(!empty($checkONU['id'])){
	$addmonitor = '<span class="delmonitor">Видалити моніторинг</span>';
}else{
	$addmonitor = '<span class="addmonitor" onclick="ajaxcore(\'addmonitor\',\''.$id.'\');">Додати в моніторинг</span>';	
}
$tpl->set('{number_ont}',$mac_ont.$sn_ont);
$tpl->set('{monitoronu}',$monitoronu);
$tpl->set('{comments}',$tpl->result['comments']);
$tpl->set('{tag}',$tpl->result['tag']);
$tpl->set('{billing}',$tpl->result['billing']);
$tpl->set('{logonu}',$logonu);
$tpl->set('{langcountonu}',$lang['langcountonu']);
$tpl->set('{olt_id}',$dataOLT['id']);
$tpl->set('{inface_ont}',mb_strtoupper(trim($dataONT['type']).' '.$dataONT['inface']));
$tpl->set('{olt_place}',$dataOLT['place']);
$tpl->set('{olt_updates}',$dataOLT['updates']);
$tpl->set('{type_ont}',$dataONT['type']);
$tpl->set('{inface}',$dataONT['inface']);
$tpl->set('{subinfoolt}',$subinfoolt);
$tpl->set('{olt}',$lang['olt']);
$dataONTPort = $db->Fast('switch_pon','*',['sfpid'=>$dataONT['portolt'],'oltid'=>$dataONT['olt']]);
$tpl->set('{port_id}',$dataONTPort['id']);
$tpl->set('{countonuport}',$dataONTPort['count']);
$tpl->set('{supportonuport}',$dataONTPort['support']);
$tpl->set('{supportcountonu}',$lang['supportcountonu']);
$tpl->set('{model}',$lang['model']);
$tpl->set('{port}',$lang['port']);
$tpl->set('{uptimeolt}',$lang['uptimeolt']);
$tpl->set('{olt_ip}',($USER['class']>=4?$dataOLT['netip']:'255.255.255.255'));
$tpl->set('{olt_model}',trim($dataOLT['inf']).' '.$dataOLT['model']);
$tpl->set('{olt_port_ont}',mb_strtoupper($dataONT['type'].' '.cl_inface($dataONT['inface'])));
$tpl->set('{olt_uptime}',($dataOLT['uptime']?$dataOLT['uptime']:'---'));
$tpl->set('{telnet}',$addmonitor);
$tpl->compile('content');
$tpl->clear();
?>