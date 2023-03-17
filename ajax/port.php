<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$act = isset($_POST['act']) ? Clean::text($_POST['act']): null;
$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
switch($act){
	case 'descr':
		okno_title('Нотатка для порта');
		echo'<form action="/?do=send" method="post" id="formadd">';
		echo'<input name="act" type="hidden" value="saveportdescr"><input name="id" type="hidden" value="'.$id.'">';
		$textresult = '<textarea class="textarea1" rows="7" name="descrport">'.(isset($result) ? $result : "").'</textarea>';
		echo form(['name'=>'Нотатка','descr'=>'','pole'=>$textresult]);
		echo'<span class="js_replace"></span>';
		echo'</form>';
		echo'<div class="polebtn"><button type="submit" form="formadd" value="submit">Додати</button></div>';
		okno_end();
	break;	
	case 'hidden':	
		$blockid = isset($_POST['id']) ? Clean::int($_POST['id']): null;
		$userid = isset($_POST['userid']) ? Clean::int($_POST['userid']): null;
		$type = isset($_POST['type']) ? Clean::text($_POST['type']): null;
		$gettype = ($type=='hide'?'hide':'show');
		if($id && $userid){
			$getUser = $db->Fast('users','port,id',['id'=>$userid]);
			if(!empty($getUser['port'])){
				$setup = unserialize($getUser['port']);
				$setup[$blockid] = $gettype;
				// дублікати видаляємо
			}else{
				$setup = array();
				$setup[$blockid] = $gettype;
			}
			if(is_array($setup) && !empty($getUser['id']))
				$db->SQLupdate('users',['port'=>serialize($setup)],['id'=>$getUser['id']]);
		}
	break;	
	case 'edit':
		$getPort = $db->Fast('switch_port','deviceid,descrport',['id'=>$id]);
		if(!empty($getPort['descrport'])){
			okno_title('Редагувати опис порта');
			echo'<form action="/?do=send" method="post" id="formadd">';
			echo'<input name="act" type="hidden" value="saveportdescr">';
			echo'<input name="id" type="hidden" value="'.$id.'">';
			$textresult = '<textarea class="textarea1" rows="7" name="descrport">'.$getPort['descrport'].'</textarea>';
			echo form(['name'=>'Опис','descr'=>'Опис порта або замітка','pole'=>$textresult]);
			echo'<span class="js_replace"></span>';
			echo'</form>';
			echo'<div class="polebtn"><button type="submit" form="formadd" value="submit">Додати</button></div>';
			okno_end();
		}
	break;	
	case 'setup':
		$getPort = $db->Fast('switch_port','*',['id'=>$id]);
		if(!empty($getPort['id'])){
			okno_title('Налаштування');
			echo'<form action="/?do=send" method="post" id="formadd">';
			echo'<input name="act" type="hidden" value="saveportsetup">';
			echo'<input name="id" type="hidden" value="'.$id.'">';
				$setup1 = '<select class="select" name="monitor" id="monitor">';
				$setup1 .= '<option value="yes" '.($getPort['monitor']=='yes'?'selected':'').'>Включити</option>';
				$setup1 .= '<option value="no" '.($getPort['monitor']=='no'?'selected':'').'>Виключено</option>';
				$setup1 .='</select>';
			echo form(['name'=>'Моніторинг','descr'=>'Статус порта, помилки, відключення','pole'=>$setup1]);
				$setup2 = '<select class="select" name="sms" id="sms">';
				$setup2 .= '<option value="yes" '.($getPort['sms']=='yes'?'selected':'').'>Включити</option>';
				$setup2 .= '<option value="no" '.($getPort['sms']=='no'?'selected':'').'>Виключено</option>';
				$setup2 .='</select>';
			echo form(['name'=>'Сповіщення','descr'=>'Сповіщення в телеграм про всі зміни','pole'=>$setup2]);				
				$setup3 = '<select class="select" name="error" id="error">';
				$setup3 .= '<option value="yes" '.($getPort['error']=='yes'?'selected':'').'>Включити</option>';
				$setup3 .= '<option value="no" '.($getPort['error']=='no'?'selected':'').'>Виключено</option>';
				$setup3 .='</select>';
			echo form(['name'=>'Помилки на порті','descr'=>'Моніторити помилки на порті','pole'=>$setup3]);
			echo'</form>';
			echo'<div class="polebtn"><button type="submit" form="formadd" value="submit">Зберегти</button></div>';
			okno_end();
		}
	break;
}
