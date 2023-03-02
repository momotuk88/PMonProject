<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$id = isset($_GET['id']) ? Clean::int($_GET['id']) : null;
if(!$id)
	$go->redirect('main');	
$dataonu = $db->Fast('onus','*',['idonu'=>$id]);
$metatags = array('title'=>$dataonu['type'].' '.$dataonu['inface'].' '.$lang['pt_onu'],'description'=>$lang['pd_onu'],'page'=>'onu');
if(!$dataonu['idonu'])
	$go->redirect('main');	
$dataolt = $db->Fast('switch','*',['id'=>$dataonu['olt']]);
if(!$dataolt['id'])
	$go->redirect('main');	
if($config['tag']=='on'){
	if($dataonu['tag']){
		$tpl->load_template('onu/edittag.tpl');
		$tpl->set('{id}',$id);
		$tpl->set('{edit}',$lang['edit']);
		$tpl->set('{marker}',$lang['marker']);
		$tpl->set('{tag}',$dataonu['tag']);
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
	if($dataonu['uid']){
		$tpl->load_template('onu/editbilling.tpl');
		$tpl->set('{id}',$id);
		$tpl->set('{dogovor}',$lang['dogovor']);
		$tpl->set('{edit}',$lang['edit']);
		$tpl->set('{uid}',$dataonu['uid']);
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
	if($dataonu['comments']){
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
	$monitoronu = '<span class="monitoronus"><div class="blob"></div>'.$lang['m'].'</span>';
}
$logonu = ListOnuLog($dataonu['idonu'],$dataolt['id']);
$tpl->load_template('onu/main.tpl');
$tpl->set('{id}',$id);
if(!empty($dataonu['mac']))
	$serialonu = '<span class="n">MAC</span><span class="m">'.$dataonu['mac'].'</span>';
if(!empty($dataonu['sn']))
	$serialonu = '<span class="n">SN</span><span class="m">'.$dataonu['sn'].'</span>';
$checkONU = $db->Fast($PMonTables['mononu'],'*',['idonu'=>$id]);
if(!empty($checkONU['id'])){
	$addmonitor = '<span class="delmonitor" onclick="ajaxcore(\'delmonitor\',\''.$id.'\');">'.$lang['mdel'].'</span>';
}else{
	$addmonitor = '<span class="addmonitor" onclick="ajaxcore(\'addmonitor\',\''.$id.'\');">'.$lang['madd'].'</span>';	
}
$tpl->set('{number_ont}',$serialonu);
$tpl->set('{monitoronu}',$monitoronu);
$tpl->set('{comments}',isset($tpl->result['comments']) ? $tpl->result['comments'] : '');
$tpl->set('{tag}',isset($tpl->result['tag']) ? $tpl->result['tag'] : '');
$tpl->set('{billing}',isset($tpl->result['billing']) ? $tpl->result['billing'] : '');
$tpl->set('{logonu}',$logonu);
$tpl->set('{langcountonu}',$lang['langcountonu']);
$tpl->set('{olt_id}',$dataolt['id']);
$tpl->set('{inface_ont}',mb_strtoupper(trim($dataonu['type']).' '.$dataonu['inface']));
$tpl->set('{olt_place}',$dataolt['place']);
$tpl->set('{olt_updates}',$dataolt['updates']);
$tpl->set('{type_ont}',$dataonu['type']);
$tpl->set('{inface}',$dataonu['inface']);
$tpl->set('{subinfoolt}','');
$tpl->set('{olt}',$lang['olt']);
$dataonuPort = $db->Fast('switch_pon','*',['sfpid'=>$dataonu['portolt'],'oltid'=>$dataonu['olt']]);
$tpl->set('{port_id}',$dataonuPort['id']);
$tpl->set('{countonuport}',$dataonuPort['count']);
$tpl->set('{supportonuport}',$dataonuPort['support']);
$tpl->set('{supportcountonu}',$lang['supportcountonu']);
$tpl->set('{model}',$lang['model']);
$tpl->set('{port}',$lang['port']);
$tpl->set('{uptimeolt}',$lang['uptimeolt']);
$tpl->set('{netip}',($USER['class']>=4?($config['viewipswitch']=='on'?'<div class="olt-data"><span class="name">IP:</span><span class="data">'.$dataolt['netip'].'</span></div>':''):''));
$tpl->set('{olt_model}',trim($dataolt['inf']).' '.$dataolt['model']);
$tpl->set('{olt_port_ont}',mb_strtoupper($dataonu['type'].' '.cl_inface($dataonu['inface'])));
$tpl->set('{olt_uptime}',($dataolt['uptime']?$dataolt['uptime']:'---'));
$tpl->set('{telnet}',$addmonitor);
$tpl->compile('content');
$tpl->clear();
?>