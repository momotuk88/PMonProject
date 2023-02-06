<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$id = isset($_GET['id']) ? Clean::int($_GET['id']) : null;
$dataGroup = $db->Fast($PMonTables['gr'],'*',['id'=>$id]);
if(!empty($dataGroup['id'])){
	$metatags = array('title'=>$lang['title_group'],'description'=>$lang['descr_group'],'page'=>'group');
	$tpl->load_template('group/detail.tpl');
	$tpl->set('{id}',$dataGroup['id']);
	$tpl->set('{name}',$dataGroup['name']);
	$SQLDevice = $db->Multi('switch','*',['groups'=>$dataGroup['id']]);
	if(count($SQLDevice)){
		$devicelist .='<div class="group-list-detail">';
		foreach($SQLDevice as $Device){
			$devicelist .='<div class="grlist"><a href="/"><h2>'.$Device['place'].'</h2></a><span class="netip">'.$Device['netip'].'</span><span class="model">'.$Device['inf'].' '.$Device['model'].'</span><span class="btn-del-gr"><span><img onclick="ajaxcore(\'delgroupdev\','.$Device['id'].');" src="../style/img/cross-mark.png"></span></span></div>';
		}
		$devicelist .='</div>';
	}
	$tpl->set('{device}',$devicelist);
	$tpl->compile('group');
	$tpl->clear();	
	$speedbar = '<div id="onu-speedbar"><a class="brmhref" href="/?do=group"><i class="fi fi-rr-folder"></i>Групи</a><span class="brmspan"><i class="fi fi-rr-angle-left"></i>'.$dataGroup['name'].'</span></div>';
}else{
	$metatags = array('title'=>$lang['title_group'],'description'=>$lang['descr_group'],'page'=>'group');
	if(count($SQLListlocation)){
				$addgroup = '<div class="navigation mbottom20"><span class="deviceadd" onclick="ajaxcore(\'addgroup\');">'.$lang['addgroup'].'</span></div>';
		foreach($SQLListlocation as $group){
			$tpl->load_template('group/list.tpl');
			$tpl->set('{id}',$group['id']);
			$tpl->set('{name}',$group['name']);
			$SQLcountGroups = $db->Multi($PMonTables['switch'],'*',['groups'=>$group['id']]);
			$tpl->set('{count}',(count($SQLcountGroups)?'<span>'.count($SQLcountGroups).'</span>':''));
			$tpl->set('{url}','/?do=group&id='.$group['id']);
			$tpl->compile('group');
			$tpl->clear();				
		}
	}else{
		$addgroup = '<div class="navigation mbottom20"><span class="deviceadd" onclick="ajaxcore(\'addgroup\');">'.$lang['addgroup'].'</span></div>';
		$tpl->load_template('group/empty.tpl');
		$tpl->set('{result}','');
		$tpl->compile('group');
		$tpl->clear();	
	}	
	$speedbar = '<div id="onu-speedbar"><a class="brmhref" href="/?do=group"><i class="fi fi-rr-folder"></i>Групи</a><span class="brmspan"><i class="fi fi-rr-angle-left"></i>Список груп</span></div>';
}
$tpl->load_template('group/main.tpl');
$tpl->set('{result}',$tpl->result['group']);
$tpl->set('{speedbar}',$speedbar);
$tpl->set('{addgroup}',$addgroup);
$tpl->compile('content');
$tpl->clear();