<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
if($USER){
	$m_device = '<li class="nav-item "><a class="nav-link dropdown-toggle" href="#">';
	$m_device .= '<div class="i"><i class="fi fi-rr-screen"></i></div><span class="m">'.$lang['btn_menu_device'].'</span></a>';
	$m_device .= '<ul class="nav-dropdown-items dropdown">';
	$m_device .= '<li class="nav-item"><a class="nav-link" href="/?do=device"><div class="i"><i class="fi fi-rr-database"></i></div><span class="m">'.$lang['btn_menu_list'].'</span>'.(count($checkLicenseSwitch)?'<span class="btn_mon_switch">'.count($checkLicenseSwitch).'</span>':'').'</a></li>';				
	$m_device .= '<li class="nav-item"><a class="nav-link" href="/?do=porterror"><div class="i"><i class="fi fi-rr-stats"></i></div><span class="m">Помилки на портах</span></a></li>';				
	$m_device .= '<li class="nav-item"><a class="nav-link" href="/?do=pondog"><div class="i"><i class="fi fi-rr-refresh"></i></div><span class="m">'.$lang['btn_menu_cron'].'</span></a>';
	$m_device .= '</li>';
	$m_device .= '</ul></li>';
}
if($config['sklad']=='on' && checkAccess(4)){
	$m_sklad = '<li class="nav-title">ТМЦ</li>'; 
	$m_sklad .= '<li class="nav-item nav-dropdown "><a class="nav-link dropdown-toggle" href="#">'; 
	$m_sklad .= '<div class="i"><i class="fi fi-rr-copy"></i></div> <span class="m">Склад</span></a>'; 
	$m_sklad .= '<ul class="nav-dropdown-items dropdown">'; 
	$m_sklad .= '<li class="nav-item"><a class="nav-link" href="/?do=add&act=all"><div class="i"><i class="fi fi-rr-apps-add"></i></div><span>Поставити на прихід</span></a></li>'; 
	$m_sklad .= '<li class="nav-item"><a class="nav-link" href="/?do=sklad"><div class="i"><i class="fi fi-rr-apps"></i></div><span>Обладання</span></a></li>'; 
	$m_sklad .= '</ul>'; 
	$m_sklad .= '</li>'; 
}
if($USER){
	$m_error = '<li class="nav-item"><a class="nav-link" href="/?do=search"><div class="i"><i class="fi fi-rr-search-alt"></i></div>';
	$m_error .= '<span class="m">'.$lang['btn_menu_search_ont'].'</span></a></li>';	
	if(count($SQLCountMonitor)){	
		$m_error .= '<li class="nav-item"><a class="nav-link" href="/?do=mononu"><div class="i"><i class="fi fi-rr-thumbtack"></i></div>';
		$m_error .= '<span class="m">Моніторинг ONU</span><span class="btn_mon_onus">'.count($SQLCountMonitor).'</span></a></li>'; 
	}	
	$m_error .= '<li class="nav-item"><a class="nav-link" href="/?do=statusport"><div class="i"><i class="fi fi-rr-time-twenty-four"></i></div>';
	$m_error .= '<span class="m">Моніторинг портів</span></a></li>'; 
}
	$m_monitor = '';
	$m_pon ='';
if($config['pon']=='on'){
	$m_pon .='<li class="nav-title">Мережа</li>';
	$m_pon .='<li class="nav-item nav-dropdown "><a class="nav-link dropdown-toggle" href="#"><div class="i"><i class="fi fi-rr-grid"></i></div><span class="m">'.$lang['location'].'</span></a>';
	$m_pon .='<ul class="nav-dropdown-items dropdown">';
	$m_pon .='<li class="nav-item"><a class="nav-link" href="/?do=location"><div class="i"><i class="fi fi-rr-building"></i></div><span>'.$lang['listlocation'].'</span></a></li>';
	$m_pon .='<li class="nav-item"><a class="nav-link" href="/?do=group"><div class="i"><i class="fi fi-rr-folder"></i></div><span>'.$lang['group'].'</span>'.(count($SQLListlocation)?'<span class="btn_mon_group">'.count($SQLListlocation).'</span>':'').'</a></li>';
	$m_pon .='</ul></li>';
}
if(checkAccess(6)){
	$m_config ='<li class="nav-title">Config</li>';
	$m_config .='<li class="nav-item nav-dropdown "><a class="nav-link dropdown-toggle" href="#">';
	$m_config .='<div class="i"><i class="fi fi-rr-grid"></i></div><span class="m">'.$lang['btn_menu_sys'].'</span></a>';
	$m_config .='<ul class="nav-dropdown-items dropdown">';
	$m_config .='<li class="nav-item"><a class="nav-link" href="/?do=config"><div class="i"><i class="fi fi-rr-apps"></i></div><span>'.$lang['btn_menu_conf'].'</span></a></li>';
	$m_config .='<li class="nav-item"><a class="nav-link" href="/?do=billing"><div class="i"><i class="fi fi-rr-e-learning"></i></div><span>API Billing</span></a></li>';
	$m_config .='<!--<li class="nav-item"><a class="nav-link" href="/?do=apikey"><div class="i"><i class="fi fi-rr-e-learning"></i></div><span>API</span></a></li>-->';
	$m_config .='<li class="nav-item"><a class="nav-link" href="/?do=oid"><div class="i"><i class="fi fi-rr-key"></i></div><span>'.$lang['btn_menu_oid_m'].'</span></a></li>';
	$m_config .='<li class="nav-item"><a class="nav-link" href="/?do=users"><div class="i"><i class="fi fi-rr-map-marker"></i></div><span>'.$lang['btn_menu_user'].'</span></a></li>';
	$m_config .='</ul></li>';
}
if($USER){
	$m_exit = '<li class="nav-item"><a class="nav-link" href="/?do=exit"><div class="i"><i class="fi fi-rr-unlock"></i></div>';
	$m_exit .= '<span class="m">'.$lang['btn_menu_exit'].'</span></a></li>'; 
}
$tpl->load_template('menu.tpl');
$tpl->set('{menu-list-monitor}',$m_monitor);
$tpl->set('{menu-list-error}',$m_error);
$tpl->set('{menu-list-sklad}',$m_sklad);
$tpl->set('{menu-list-pon}',$m_pon);
$tpl->set('{menu-list-config}',$m_config);
$tpl->set('{menu-list-exit}',$m_exit);
$tpl->set('{menu-list-device}',$m_device);
$tpl->compile('menu');
$tpl->clear();
?>
