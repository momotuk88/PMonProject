<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$addparam = $addparam ?? null;
$id = isset($_GET['id']) ? Clean::int($_GET['id']) : null;
if(!$id){
	$go->redirect('main');	
}
$dataSwitch = $db->Fast($PMonTables['switch'],'*',['id'=>$id]);
if(!$dataSwitch['id']){
	$go->redirect('main');	
}
$where = array('deviceid'=>$dataSwitch['id']);
$order = array('added'=>'DESC');
switch($act){
	case'clear':
	
	break;
	default:
		$SQLCount = $db->Multi($PMonTables['swlog'],'*',$where);
		list($pagertop, $pagerbottom, $limit, $offset) = pager(25,count($SQLCount),'/?do=switchlog&id='.$id.''.$addparam);
		$SQLLog = $db->Multi($PMonTables['swlog'],'*',$where,$order,$offset,$limit);
		if(count($SQLLog)){
			foreach($SQLLog as $log){
				$tpl->load_template('log/list.tpl');
				$tpl->set('{id}',$log['id']);
				$tpl->set('{added}',$log['added']);
				$tpl->set('{text}',$log['message']);
				$tpl->set('{types}',$log['types']);
				$tpl->compile('log-result');
				$tpl->clear();			
			}
		}else{
			$tpl->load_template('log/empty.tpl');
			$tpl->compile('log-result');
			$tpl->clear();				
		}

}
$metatags = array('title'=>$lang['log'].' '.$dataSwitch['place'],'description'=>$dataSwitch['place'].' '.$dataSwitch['inf'].' '.$dataSwitch['model'],'page'=>'switchlog');
$tpl->load_template('log/page.tpl');
$tpl->set('{result}',$tpl->result['log-result']);
$tpl->set('{url}','/?do=detail&act='.$dataSwitch['device'].'&id='.$dataSwitch['id']);
$tpl->set('{pager}',$pagertop);
$tpl->set('{logdevice}',$dataSwitch['place'].' '.$dataSwitch['inf'].' '.$dataSwitch['model']);
$tpl->set('{logname}',$lang['log']);
$tpl->compile('content');
$tpl->clear();	
?>