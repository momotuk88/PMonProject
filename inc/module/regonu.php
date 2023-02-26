<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}	
$support = $support ?? null;
$next = $next ?? null;
$id = isset($_GET['id']) ? Clean::int($_GET['id']) : null;
if(!$id){
	$go->redirect('main');	
}
$dataSwitch = $db->Fast('switch','*',['id'=>$id]);
if(!$dataSwitch['id']){
	$go->redirect('main');	
}
if($dataSwitch['device']=='olt'){
	$SQLPon = $db->Multi('switch_pon','*',['oltid'=>$id]);
	$next = true;
}
if(!$next){
	$go->redirect('main');	
}
/// ZTE 300,320 - GPON
if($dataSwitch['oidid']==7){
	$jsloadsnmp = 'ajaxloadsnmpnoregonuzte('.$dataSwitch['id'].');';
	$support = true;
}
/// ZTE 300,320 - GPON
if($support){
$tpl->load_template('olt/regonu.tpl');
$tpl->set('{oltname}',$dataSwitch['place'].' '.$dataSwitch['inf'].' '.$dataSwitch['model']);
$tpl->set('{result}',$tplRes);
$tpl->set('{olt}',$id);
$tpl->set('{jsloadsnmp}','<script>'.$jsloadsnmp .'</script>');
$tpl->set('{listdevice}',$lang['alldevice']);
$tpl->set('{listonunoreg}',$lang['listonunoreg']);
$tpl->compile('content');
$tpl->clear();	
}else{
	$go->redirect('main');		
}

?>