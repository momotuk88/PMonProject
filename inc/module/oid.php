<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$metatags = array('title'=>$lang['pt_oid'],'description'=>$lang['pd_oid'],'page'=>'oid');
$order = array('id'=>'DESC');
$where = $where ?? null;
$sort = $sort ?? null;
$oidid = $oidid ?? null;
$addparam  = $addparam  ?? null;
$oidid = (isset($_GET['oidid']) ? Clean::int($_GET['oidid']) : null);
if($oidid){
	$where['oidid'] = $oidid;
	$addparam = '&oidid='.$oidid;
}
$sort ='<div class="card"><div class="main-stats-tab">';
$sort .='<a href="/?do=oid" class="'.(isset($oidid)?'tab-url ':'tab-active').'"><i class="fi fi-rr-menu-burger"></i>'.$lang['alloid'].'</a>';
$sqloiddevice = $db->Multi('equipment','*',['work'=>'yes']);
if(count($sqloiddevice)){
	foreach($sqloiddevice as $deviceoid){
		$sort .='<a href="/?do=oid&oidid='.$deviceoid['oidid'].'" class="'.($oidid==$deviceoid['oidid']?'tab-active':'tab-url').'"><i class="fi fi-rr-settings"></i>'.$deviceoid['name'].' '.$deviceoid['model'].'</a>';
	}
}
$sort .='<a href="#" class="tab-url" onclick="ajaxoid(\'add\')" style="#2574f4"><i class="fi fi-rr-add"></i>'.$lang['oid_add'].'</a>';
$sort .='</div></div>';
switch($act){
	case'step1':
	
	break;
	default:
		$SQLCount = $db->Multi('oid','*',$where);
		list($pagertop, $pagerbottom, $limit, $offset) = pager(25,count($SQLCount),'/?do=oid'.$addparam);
		$SQLOID = $db->Multi('oid','*',$where,$order,$offset,$limit);
		if(count($SQLOID)){
			foreach($SQLOID as $oid){
				$tpl->load_template('oid/list.tpl');
				$tpl->set('{id}',$oid['id']);
				$tpl->set('{types}',$oid['types']);
				$tpl->set('{oidid}',$oid['oidid']);
				$tpl->set('{model}',$oid['model']);
				$tpl->set('{pon}',$oid['pon']);		
				$tpl->set('{oid}',str_replace('keyonu', '<b>keyonu</b>',str_replace('keyport', '<b>keyport</b>',$oid['oid'])));
				$tpl->set('{format}',$oid['format']);
				$tpl->set('{descr}',($oid['descr']?'<span>'.$lang[$oid['descr']].'</span>':'---'));
				$tpl->compile('oid-result');
				$tpl->clear();			
			}
		}else{
			$tpl->load_template('oid/empty.tpl');
			$tpl->compile('oid-result');
			$tpl->clear();				
		}

}
$tpl->load_template('oid/main.tpl');
$tpl->set('{result}',$tpl->result['oid-result']);
$tpl->compile('block-result');
$tpl->clear();		
$tpl->load_template('oid/page.tpl');
$tpl->set('{sort}',$sort);
$tpl->set('{result}',$tpl->result['block-result']);
$tpl->set('{pager}',$pagertop);
$tpl->compile('content');
$tpl->clear();	
?>