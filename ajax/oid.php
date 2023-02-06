<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
$act = isset($_POST['act']) ? Clean::text($_POST['act']): null;
switch($act){
	case 'edit': 
		$SQLOID = $db->Fast('oid','*',['id'=>$id]);
		if(!empty($SQLOID['id'])){
			okno_title($lang['oid_edit']);
			echo'<form action="/?do=send" method="post" id="formadd"><input name="act" type="hidden" value="saveoid">';
			echo form(['name'=>'oidid','descr'=>'oidid','pole'=>'<input required name="oidid" class="input1" type="text" value="'.$SQLOID['oidid'].'">']);
			echo form(['name'=>'model','descr'=>'model','pole'=>'<input types name="model" class="input1" type="text" value="'.$SQLOID['model'].'">']);
			echo form(['name'=>'pon','descr'=>'pon','pole'=>'<input types name="pon" class="input1" type="text" value="'.$SQLOID['pon'].'">']);
			echo form(['name'=>'oid','descr'=>'oid','pole'=>'<input types name="oid" class="input1" type="text" value="'.$SQLOID['oid'].'">']);
			$oid_format ='<select class="select" name="format" id="format">';
			$oid_format .='<option value="empty">Автоматичний</option>';
			$oid_format .='<option value="integer" '.($SQLOID['format']=='integer'?'selected':'').'>INTEGER</option>';
			$oid_format .='<option value="string" '.($SQLOID['format']=='string'?'selected':'').'>STRING</option>';
			$oid_format .='<option value="hex" '.($SQLOID['format']=='hex'?'selected':'').'>HEX-STRING</option>';
			$oid_format .='</select>';
			echo form(['name'=>'format','descr'=>'format','pole'=>$oid_format]);
			echo form(['name'=>'descr','descr'=>'descr','pole'=>'<input types name="descr" class="input1" type="text" value="'.$SQLOID['descr'].'">'.($SQLOID['descr']?''.$lang[$SQLOID['descr']].'':'').'']);
			echo'</form><div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['oid_save'].'</button><button type="button" onclick="ajaxoid(\'del\','.$id.');" style="background:#f45438;">'.$lang['oid_del'].'</button></div>';
			okno_end();
		}
	break;		
	case 'add': 	
		$getOIDList = $db->Multi('equipment');
		if(count($getOIDList)){
			foreach($getOIDList as $getOID => $valueOID){
				if(!empty($valueOID['oidid'])){
					$arrayOID[$valueOID['oidid']]['oidid'] = $valueOID['oidid'];
					$arrayOID[$valueOID['oidid']]['device'] = $valueOID['device'];
					$arrayOID[$valueOID['oidid']]['name'] = $valueOID['name'];
					$arrayOID[$valueOID['oidid']]['class'] = $valueOID['phpclass'];
				}
			}
			$oid_data ='<select class="select" name="oidid" id="oidid">';
			foreach($arrayOID as $ArrOID){
				$oid_data .='<option value="'.$ArrOID['oidid'].'">'.$ArrOID['name'].' ['.$ArrOID['class'].']</option>';				
			}
			$oid_data .='</select>';
		}
		okno_title($lang['oid_add']);
		echo'<form action="/?do=send" method="post" id="formadd"><input name="act" type="hidden" value="newoid">';
		echo form(['name'=>'Тип комутатора','descr'=>' Категорія OID','pole'=>$oid_data]);
		echo form(['name'=>'Тип','descr'=>'epon, gpon - технологія<br> device - пристрій<br> port - порт','pole'=>'<input types name="pon" class="input1" type="text">']);
		echo form(['name'=>'Технологія','descr'=>'onu - ону<br> health - параметри<br> global - глобальні<br> monitor - мніторинг','pole'=>'<input types name="inf" class="input1" type="text">']);
		echo form(['name'=>'OID','descr'=>'.keyonu - ід ону, .keyport - ід порта','pole'=>'<input types name="oid" class="input1" type="text">']);
		echo form(['name'=>'Задача OID-у','descr'=>'rx- сигнал, dist - довжина волокна','pole'=>'<input types name="types" class="input1" type="text">']);
		$oid_format ='<select class="select" name="format" id="format">';
		$oid_format .='<option value="auto">Автоматичний</option>';
		$oid_format .='<option value="integer">INTEGER</option>';
		$oid_format .='<option value="string">STRING</option>';
		$oid_format .='<option value="hex">HEX-STRING</option>';
		$oid_format .='</select>';
		echo form(['name'=>'Тип фільтрування','descr'=>'Автоматичний режин в більшості випадків','pole'=>$oid_format]);
		echo form(['name'=>'Опис запита','descr'=>'Опис запита що отримуємо','pole'=>'<input types name="descr" class="input1" type="text" value="'.$SQLOID['descr'].'">']);
		echo'</form><div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['save'].'</button></div>';
		okno_end();
	break;		
}

