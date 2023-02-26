<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$id = isset($_GET['id']) ? Clean::int($_GET['id']) : null;
if($id)
	$datalocation = $db->Fast($PMonTables['location'],'*',['id'=>$id]);
if(!empty($datalocation['id'])){
	$listdevice = getAllDeviceLocation($datalocation['id']);
	if(is_array($listdevice)){
		$devicelist = '<div id="block-location">';
		foreach($listdevice as $dev){
			$devicelist .= devLocation($dev);
		}
		$devicelist .= '</div>';
	}else{
		$devicelist = 'empty';	
	}
	$speedbar='<div id="onu-speedbar"><a class="brmhref" href="/?do=location"><i class="fi fi-rr-building"></i>'.$lang['location'].'</a><span class="brmspan"><i class="fi fi-rr-angle-left"></i>'.$datalocation['name'].'</span></div>';
	$metatags = array('title'=>$lang['locationdetail'],'description'=>$lang['locationdetail'],'page'=>'locationdetail');
	$tpl->load_template('location/detail.tpl');
	$tpl->set('{device}',$devicelist);
	$tpl->set('{id}',$datalocation['id']);
	$tpl->set('{pager}',$pager);
	$tpl->set('{setup}',$lang['setups']);
	$tpl->set('{geo}',$lang['geo']);
	$tpl->set('{delet}',$lang['delet']);
	$tpl->set('{name}',$datalocation['name']);	
	$tpl->set('{img}',(!$datalocation['photo']?'':'<div class="photo"><img src="../file/location/'.$datalocation['photo'].'"></div>'));	
	$tpl->set('{note}',($datalocation['note']?'<div class="note">'.$datalocation['note'].'</div>':''));	
	$tpl->compile('location');
	$tpl->clear();	
}else{
	$metatags = array('title'=>$lang['pt_location'],'description'=>$lang['pt_location'],'page'=>'location');
	$SQLListlocation = $db->Multi($PMonTables['location']);
	if(count($SQLListlocation)){
		foreach($SQLListlocation as $location){
			$tpl->load_template('location/list.tpl');
			$tpl->set('{id}',$location['id']);
			$tpl->set('{name}',$location['name']);
			$tpl->set('{descrdevice}',$lang['countdevice']);
			$countSqlDevice = $db->Multi($PMonTables['switch'],'id',['location'=>$location['id']]); 
			$tpl->set('{count}',count($countSqlDevice));
			if(!empty($location['lan']) && !empty($location['lon']))
				$geo ='<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAHHSURBVFhHzZbLSsNAFIYLI+JD+DK+he/grrpy4VareEnbrfgC7oWKgoIKKuhGXNeF2NiK1EvtBWMm+aeZxJM0mUySfvBDZ+bMOYf08DMlVmtXZqqv1rSoxGF75hZ1WISchjj2l1qnAvIW2nFhRmuHCspTaMWDVc0NKlCXOr1fizNbo8/Rhh9Wb29TwTokQzWFFv6T1Ux1++4XEgTPUZ7GbmozeCGurl+GKOkxv9/BLz9zde8eSofDDDOxT5XPPlHKsk6eBtbj2wgrj4OHH+vYPpOb4ULZaFTMc+3qyyncaA7Ge4Ldu29frCyUnIyKeS6dfjgN3LaG4z3OvTnyxclCuXioDDr/yziNZt9ZC4JxQigVnyTmuXzuzpL4UjfSoFPxXCiTjDjmuXrpzpBYi5niXDx7f2FQKJGcKPNcwZcJ7pdD9mUhvRrUTC0edSOLLhy+k/tCSK1OGvOkhLTpUDHPMCFlelTMkxLS6UHHyxOp9KFinrKQRi9pXp5IoR/VlyeuZ4PKyxNXsyPpTOFatiQxT1zJnrjmifB8iGOeCM2PSeaJsHyJGnSE5E+YeeK4GCjzxFFxBM0T28UizxS2ikeYJ5bTATPMyh9b6sRGRoDKwQAAAABJRU5ErkJggg==" class="top-icon">';
			$tpl->set('{inf}',''.$geo.'');	
			$tpl->set('{url}','/?do=location&id='.$location['id']);
			$photo = ($location['photo']?'/file/location/'.$location['photo']:'/style/img/location.jpg');
			$tpl->set('{photocss}','style="background-image: url('.$photo.'"));"');
			$tpl->compile('location');
			$tpl->clear();				
		}
	}else{
		$tpl->load_template('location/empty.tpl');
		$tpl->set('{result}','');
		$tpl->compile('location');
		$tpl->clear();	
	}			
}
$tpl->load_template('location/main.tpl');
$tpl->set('{add}',(checkAccess(6) && empty($datalocation['id'])?'<div class="navigation mbottom20"><span class="deviceadd" onclick="ajaxcore(\'newlocation\');">'.$lang['add_location'].'</span></div>':''));
$tpl->set('{result}',(empty($datalocation['id'])?'<div id="location">'.$tpl->result['location'].'</div>' : $tpl->result['location']));
$tpl->set('{speedbar}','');
$tpl->compile('content');
$tpl->clear();
?>