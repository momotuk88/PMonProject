<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
if(!checkAccess(6))
	$go->redirect('main');
switch ($act) {
	case 'all':
		$tpl->load_template('add/sfp.tpl');
		$tpl->set('{url}',$config['url']);
		$tpl->set('{sfp}',$lang['sfp']);
		$tpl->compile('all-device');
		$tpl->clear();
	break;
}
if($config['sklad']=='on'){
		
}
$metatags = array('title'=>$lang['pt_add'],'description'=>$lang['pd_add'],'page'=>'add');
$tpl->load_template('add/list.tpl');
$tpl->set('{all}',$tpl->result['all-device']);
$tpl->set('{url}',$config['url']);
$tpl->set('{newdevice}',$lang['newdevice']);
$tpl->set('{olt}',$lang['olt']);
$tpl->set('{switch}',$lang['switch']);
$tpl->set('{switchl2}',$lang['switchl2']);
$tpl->compile('block-add');
$tpl->clear();
$tpl->load_template('add/main.tpl');
$tpl->set('{result}',$tpl->result['block-add']);
$tpl->compile('content');
$tpl->clear();	
?>