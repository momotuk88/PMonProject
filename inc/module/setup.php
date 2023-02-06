<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
if(!checkAccess(6))
	$go->redirect('main');
$metatags['title'] = $lang['setup'];
$metatags['description'] = $lang['setup'];
$id = isset($_GET['id'])?Clean::int($_GET['id']):null;
if(!$id)
	$go->redirect('main');	
$dataSwitch = $db->Fast('switch','*',['id'=>$id]);
if(!$dataSwitch['id'])
	$go->redirect('main');	
$setup = '<form action="/?do=send" method="post" id="formadd">';
$setup .= '<input name="act" type="hidden" value="savesetup">';
$setup .= '<input name="id" type="hidden" value="'.$dataSwitch['id'].'">';
$group = getListGroup();
if(is_array($group)){
	foreach($group as $gr){
		$listgroup .= '<option value="'.$gr['id'].'" '.($dataSwitch['groups']==$gr['id']?' selected':'').'>'.$gr['name'].'</option>';
	}
	$setup .= formpage(['img'=>'folders.png','name'=>$lang['group'],'descr'=>'Групування пристроїв','pole'=>'<select class="select" name="group" id="group"><option value="0"></option>'.$listgroup.'</select>']);
}
$location = getListLocations();
if(is_array($location)){
	foreach($location as $loc){
		$listlocation .= '<option value="'.$loc['id'].'" '.($dataSwitch['location']==$loc['id']?' selected':'').'>'.$loc['name'].'</option>';
	}
	$setup .= formpage(['img'=>'m6.png','name'=>$lang['location'],'descr'=>'Розташування пристрою','pole'=>'<select class="select" name="location" id="location"><option value="0"></option>'.$listlocation.'</select>']);
}
$setup .= formpage(['img'=>'addconnect.png','name'=>$lang['inputnamedevice'],'descr'=>'Коротка назва комутатора','pole'=>'<input types name="place" class="input1" type="text" value="'.$dataSwitch['place'].'">']);
$monitor ='<select class="select" name="monitor" id="format">';
$monitor .='<option value="no"></option>';
$monitor .='<option value="yes" '.($dataSwitch['monitor']=='yes'?'selected':'').'>'.($dataSwitch['monitor']=='yes'?'Включено':'Включити').'</option>';
$monitor .='<option value="no" '.($dataSwitch['monitor']=='no'?'selected':'').'>'.($dataSwitch['monitor']=='no'?'Виключено':'Виключити').'</option>';
$monitor .='</select>';
$setup .= formpage(['img'=>'no-internet.png','name'=>'Моніторинг','descr'=>'Моніторинг комутатора системою','pole'=>$monitor]);
$connect ='<select class="select" name="connect" id="format">';
$connect .='<option value="no"></option>';
$connect .='<option value="yes" '.($dataSwitch['connect']=='yes'?'selected':'').'>'.($dataSwitch['connect']=='yes'?'Включено':'Включити').'</option>';
$connect .='<option value="no" '.($dataSwitch['connect']=='no'?'selected':'').'>'.($dataSwitch['connect']=='no'?'Виключено':'Виключити').'</option>';
$connect .='</select>';
$setup .= formpage(['img'=>'servers.png','name'=>'Комутація','descr'=>'Комутація портів','pole'=>$connect]);
$gallery ='<select class="select" name="gallery" id="format">';
$gallery .='<option value="no"></option>';
$gallery .='<option value="yes" '.($dataSwitch['gallery']=='yes'?'selected':'').'>'.($dataSwitch['gallery']=='yes'?'Включено':'Включити').'</option>';
$gallery .='<option value="no" '.($dataSwitch['gallery']=='no'?'selected':'').'>'.($dataSwitch['gallery']=='no'?'Виключено':'Виключити').'</option>';
$gallery .='</select>';
$setup .= formpage(['img'=>'photo-gallery.png','name'=>'Фото','descr'=>'Фото галерея','pole'=>$gallery]);
$typecheck ='<select class="select" name="typecheck" id="format">';
$typecheck .='<option value="no"></option>';
$typecheck .='<option value="15min" '.($dataSwitch['typecheck']=='15min'?'selected':'').'>15 хв</option>';
$typecheck .='<option value="30min" '.($dataSwitch['typecheck']=='30min'?'selected':'').'>30 хв</option>';
$typecheck .='<option value="1h" '.($dataSwitch['typecheck']=='1h'?'selected':'').'>1 година</option>';
$typecheck .='<option value="2h" '.($dataSwitch['typecheck']=='2h'?'selected':'').'>2 години</option>';
$typecheck .='<option value="3h" '.($dataSwitch['typecheck']=='3h'?'selected':'').'>3 години</option>';
$typecheck .='<option value="4h" '.($dataSwitch['typecheck']=='4h'?'selected':'').'>4 години</option>';
$typecheck .='</select>';
$setup .= formpage(['img'=>'m2.png','name'=>'Інтервал CRON','descr'=>'Час перевірки комутатора','pole'=>$typecheck]);
// ip
$setup .= formpage(['img'=>'img1.png','name'=>$lang['ip'],'descr'=>$lang['ipdescr'],'pole'=>'<input types name="netip" class="input1" type="text" value="'.$dataSwitch['netip'].'">']);
// mac
$setup .= formpage(['img'=>'img1.png','name'=>$lang['mac'],'descr'=>$lang['ipdescr'],'pole'=>'<input types name="mac" class="input1" type="text" value="'.$dataSwitch['mac'].'">']);
// sn 
$setup .= formpage(['img'=>'m5.png','name'=>$lang['sn'],'descr'=>$lang['supporttmc'],'pole'=>'<input types name="sn" class="input1" type="text" value="'.$dataSwitch['sn'].'">']);
$setup .= '<div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['save'].'</button></form>';
$tpl->load_template('setup/main.tpl');
$tpl->set('{langsetup}',$lang['setup']);
$tpl->set('{id}',$id);
$tpl->set('{place}',$dataSwitch['place']);
$tpl->set('{langlist}',$lang['alldevice']);
$tpl->set('{result}',$setup);
$tpl->compile('content');
$tpl->clear();
?>