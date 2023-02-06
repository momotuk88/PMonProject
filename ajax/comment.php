<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
$act = isset($_POST['act']) ? Clean::str($_POST['act']): null;
if($act=='edittag'){
	$dataONT = $db->Fast('onus','*',['idonu'=>$id]);
	$action = '<input name="act" type="hidden" value="savetag"><input name="id" type="hidden" value="'.$id.'">';
	$title = $lang['editmark'].': '.$dataONT['type'].' '.$dataONT['inface'];
	$content = form(['name'=>$lang['marker'],'descr'=>'','pole'=>'<input required name="tag" class="input1" type="text" value="'.$dataONT['tag'].'">']);
}elseif($act=='edituid'){
	$dataONT = $db->Fast('onus','*',['idonu'=>$id]);
	if(!empty($dataONT['uid'])){
		$action = '<input name="act" type="hidden" value="saveuid"><input name="id" type="hidden" value="'.$id.'">';
		$title = $lang['edituid'].': '.$dataONT['type'].' '.$dataONT['inface'];
		$content = form(['name'=>'UID','descr'=>'','pole'=>'<input required name="uid" class="input1" type="text" value="'.$dataONT['uid'].'">']);
	}
}else{
		
}
if($content){
	okno_title($title);
	echo'<form action="/?do=send" method="post" id="formadd">';
	echo $action;
	echo $content;
	echo'</form>';
	echo'<div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['save'].'</button></div>';
	okno_end();
}

