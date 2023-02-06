<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$metatags = array('title'=>$lang['pt_oid'],'description'=>$lang['pd_oid'],'page'=>'oid');
$Order = array('id'=>'DESC');
switch($act){
	case'step1':
	
	break;
	default:
		$SQLCount = $db->Multi('oid','*',$Where);
		list($pagertop, $pagerbottom, $limit, $offset) = pager(25,count($SQLCount),'/?do=oid'.$addparam);
		$SQLOID = $db->Multi('oid','*',$Where,$Order,$offset,$limit);
		if(count($SQLOID)){
			foreach($SQLOID as $oid){
				$tpl->load_template('oid/list.tpl');
				$tpl->set('{id}',$oid['id']);
				$tpl->set('{types}',$oid['types']);
				$tpl->set('{oidid}',$oid['oidid']);
				$tpl->set('{model}',$oid['model']);
				$tpl->set('{pon}',$oid['pon']);		
					$oids = str_replace('keyonu', '<b>keyonu</b>',$oid['oid']);
					$oids = str_replace('keyport', '<b>keyport</b>',$oids);
				$tpl->set('{oid}',$oids);
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
$tpl->set('{lang_add_oid}',$lang['oid_add']);
$tpl->set('{result}',$tpl->result['block-result']);
$tpl->set('{pager}',$pagertop);
$tpl->compile('content');
$tpl->clear();	
?>