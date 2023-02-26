<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$metatags = array('title'=>$lang['add_new_users'],'description'=>$lang['add_new_users'],'page'=>'users');
$selectallusers = $db->Multi('users');
if(count($selectallusers)){
	foreach($selectallusers as $user){
		$tpl->load_template('users/list.tpl');
		$tpl->set('{id}',$user['id']);
		$tpl->set('{username}',$user['username']);
		$tpl->set('{name}',($user['name']?'<b>'.$user['name'].'</b>':''));
		$tpl->set('{added}',$user['added']);
		$tpl->set('{page}',$user['url']);
		$tpl->set('{last}',$user['lastactivity']);
		$tpl->set('{ip}',($user['ip']?'<div class="userip">'.$user['ip'].'</div>':''));
		$tpl->set('{onlyip}',($user['onlyip']=='on'?'<div class="onlyip">прив`язка до ір <b>'.$user['setip'].'</b></div>':''));
		$tpl->set('{class}',getClassUser($user['class']));
		$tpl->set('{usermoder}',(checkAccess(6)?'<a href="#" onclick="ajaxcore(\'edituser\','.$user['id'].')">Редагувати</a>'.($USER['id']!==$user['id']?'<a href="#" onclick="ajaxcore(\'deletuser\','.$user['id'].')">Видалити</a>':''):''));
		$tpl->compile('list-users');
		$tpl->clear();			
	}
}else{
		
}
$tpl->load_template('users/main.tpl');
$tpl->set('{add}',(checkAccess(6)?'<div class="navigation mbottom20"><span class="deviceadd" onclick="ajaxcore(\'newuser\');">'.$lang['add_new_users'].'</span></div>':''));
$tpl->set('{name}',$lang['users']);
$tpl->set('{result}',$tpl->result['list-users']);
$tpl->compile('content');
$tpl->clear();
?>