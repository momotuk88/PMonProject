<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$metatags = array('title'=>$lang['pl_device'],'description'=>$lang['pd_device'],'page'=>'pondog');
if (!empty($addparam)) {
    if (!empty($pagerlink))
        $addparam = $addparam . $pagerlink;
} else {
    $addparam = $pagerlink;
}
if(!empty($USER['class']) && $USER['class']>=5 && $_GET['act']=='del'){		
	if(isset($_GET['id']))
		$getDataSql['id'] = Clean::int($_GET['id']);
	if(!empty($getDataSql['id'])){
		$db->SQLdelete('swcron',['id' => $getDataSql['id']]);	
		$go->redirect('pondog');	
	}
}
$SQLCount = $db->Multi('swcron','*',$Where);
list($pagertop, $pagerbottom, $limit, $offset) = pager(20,count($SQLCount),'/?do=pondog'.$addparam);
$SQLDevice = $db->Multi('swcron','*',$Where,null,$offset,$limit);
if(count($SQLDevice)){
	foreach($SQLDevice as $Device){
		$tpl->load_template('pondog/list.tpl');
		$tpl->set('{id}',$Device['id']);
		$getDevice = $db->Fast('switch','place,model,inf,netip,typecheck',['id'=>$Device['oltid']]);	
		$tpl->set('{place}',$getDevice['place']);
		$tpl->set('{netip}',$getDevice['netip']);
		$tpl->set('{priority}',priority($Device['priority']));
		$tpl->set('{model}',$getDevice['model']);
		$tpl->set('{interval}',tim_check($getDevice['typecheck']));
		$tpl->set('{inf}',$getDevice['inf']);
		$tpl->set('{added}',$Device['added']);
		$tpl->set('{status}',($Device['status']=='no'?'wait':'works'));
		$tpl->set('{realstatus}',$Device['status']);
		$tpl->compile('pondog');
		$tpl->clear();			
	}
}else{
	$tpl->load_template('pondog/empty.tpl');
	$tpl->set('{result}',$lang['noworks']);
	$tpl->compile('pondog');
	$tpl->clear();	
}
$tpl->load_template('pondog/main.tpl');
$tpl->set('{name}',$lang['mainpondog']);
$tpl->set('{addpondog}',$lang['add']);
$tpl->set('{result}',$tpl->result['pondog']);
$tpl->compile('content');
$tpl->clear();
?>