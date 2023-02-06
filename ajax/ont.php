<?php
define('AJAX',true);
define('ONT',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
if($_POST['id']){
$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
$getONT = $db->Fast('onus','*',['idonu' => $id]);
if(!empty($getONT['idonu'])){
$getOLT = $db->Fast('switch','*',['id' => $getONT['olt']]);
if(!empty($getOLT['netip']) && !empty($getOLT['class'])){
$btn_update_info = '<span class="ajax_update_btn" onclick="Realontdata('.$id.')">'.$lang['update'].'</span>';
$OnuClass = new Ont($getOLT['id'],$getOLT['class']);
$support = $OnuClass->Support();
if($support){
	$resultONT = $OnuClass->getApi($getONT);
	if(is_array($resultONT)){
		// ZTE c320
		if($getOLT['oidid']==7 && $resultONT['status']==1){
			echo'<script type="text/javascript">ajaxzte320('.$id.');</script>';
			echo'<div id="ajaxzte320"></div>';
		}
		if(!empty($resultONT['status'])){
			$status = ($resultONT['status']==1?'<div class="ajax_ont_status_on">'.$lang['online'].'</div>':'<div class="ajax_ont_status_off">'.$lang['offline'].'</div>');
			echo ont_label('Статус',$status.$btn_update_info);
		}			
		if(!empty($resultONT['wan']) && $resultONT['status']==1 && $getOLT['oidid']!=7){
			echo ont_label($lang['wanport'],'<img src="../style/img/'.(!empty($resultONT['wanportzte']['txt']) ? $resultONT['wanportzte']['img'] : 'eth'.$resultONT['wan']).'.png" class="onueth">');
			if(is_array($resultONT['wanportzte']) && !empty($resultONT['wanportzte']['txt']))
				echo ont_label('Тип порта',$resultONT['wanportzte']['txt']);
		}		
		if(!empty($resultONT['mac']))
			echo ont_label('MAC',$resultONT['mac']);		
		if(!empty($resultONT['mngtvlan']) && $getOLT['oidid']==7){
			$editpenztevlan = ' <span class="zte_edit_vlan" onclick="showblockform2();"><img src="../style/img/edit.png"></span>';
			$formpenztevlan = '<div id="formeditvlan"><input type="text" name="vlan" id="vlan" class="namezteonu" value="'.$resultONT['mngtvlan'].'"><input type="submit" class="saverenzte" onclick="savezteonuname('.$id.');" value="Редагувати"></div>';
			echo ont_label('Vlan',$resultONT['mngtvlan'].$editpenztevlan.$formpenztevlan);
		}
		if(!empty($resultONT['name']) && $getOLT['oidid']==7){
			$editpenzte = ' <span class="zte_edit_name" onclick="showblockform1();"><img src="../style/img/edit.png"></span>';
			$formpenzte = '<div id="form_rename"><textarea name="nameonu" id="nameonu" class="namezteonu">'.$resultONT['name'].'</textarea><input type="submit" class="saverenzte" onclick="savezteonuname('.$id.');" value="Редагувати"></div>';
			echo ont_label('Опис','<div id="resname">'.$resultONT['name'].'</div>'.$editpenzte.$formpenzte);	
		}			
		if(!empty($resultONT['note']) && $getOLT['oidid']==7){
			$editpenzte = '<span class="zte_edit_note" onclick="showblockform3();"><img src="../style/img/edit.png"></span>';
			$formpenzte = '<div id="form_renote"><textarea name="noteonu" id="noteonu" class="namezteonu">'.$resultONT['note'].'</textarea><input type="submit" class="saverenzte" onclick="savezteonudescr('.$id.');" value="Редагувати"></div>';
			echo ont_label('Нотатки','<div id="resnote">'.$resultONT['note'].'</div>'.$editpenzte.$formpenzte);	
		}	
		if($getOLT['oidid']==3){
			if($resultONT['vlanmode'])
				echo ont_label('VlanMode',$resultONT['vlanmode']);			
			if($resultONT['bias'])
				echo ont_label('Bias',$resultONT['bias']);			
			if($resultONT['volt'])
				echo ont_label($lang['volt'],$resultONT['volt'].' Вольт');			
			if($resultONT['offline']){
				preg_match('/(\d+)\/(\d+)\/(\d+):(\d+):(\d+)/',$resultONT['offline'],$timeZTE);
				$clockoff = $timeZTE[1].'-'.$timeZTE[2].'-'.mb_substr($timeZTE[3],0,2).' '.mb_substr($timeZTE[3],2,4).':'.$timeZTE[4].':'.$timeZTE[5];
				echo ont_label($lang['lastoffline'],$clockoff);	
				if($resultONT['status']==2)
					echo ont_label($lang['onuoffline'], aftertime($clockoff));	
			}				
			if($resultONT['device'])
				echo ont_label($lang['models'],$resultONT['device']);			
		}	
		if(!empty($resultONT['config']))
			echo ont_label('Профіль конфігурації ONU',$resultONT['config']);		
		if(!empty($resultONT['dist']))
			echo ont_label($lang['dist'],$resultONT['dist'].' '.$lang['metr']);
		$getHistoryRx = $db->Multi($PMonTables['historyrx'],'*',['device' => $getOLT['id'],'onu' => $id]);
		if(!empty($resultONT['rx']) && $resultONT['status']==1){
			echo ont_label('RX ',signalTerminal($resultONT['rx']).(count($getHistoryRx)?'<a href="/?do=signal&id='.$id.'" class="ont-graph-rx">'.$lang['keygraphsignal'].'</a>':''));
		}else{
			echo ont_label('RX ',signalTerminal($resultONT['rx']).(count($getHistoryRx)?'<a href="/?do=signal&id='.$id.'" class="ont-graph-rx">'.$lang['keygraphsignal'].'</a>':''));
		}
		if(!empty($resultONT['lastrx']))
			echo ont_label($lang['lastrx'],signalTerminal($resultONT['lastrx']));
		if($resultONT['tx'] && $resultONT['status']==1)
			echo ont_label('TX ',signalTerminal($resultONT['tx']));		
		if($resultONT['temp'] && $resultONT['status']==1)
			echo ont_label('Temp ',$resultONT['temp'].' °C');
		if($resultONT['rxolt'] && $resultONT['status']==1)
			echo ont_label('RX OLT ',signalTerminal($resultONT['rxolt']));
		if($resultONT['model'] || $resultONT['vendor'])
			echo ont_label($lang['model'],$resultONT['vendor'].' '.$resultONT['model']);
		// BDCOM EPON - зміна влан
		if(!empty($resultONT['pvid']) && $getOLT['oidid']==1)
			echo ont_label($lang['bdcom_vlan'],$resultONT['pvid'].(!empty($getOLT['snmprw'])?'<span id="edit-vlan-'.$getONT['idonu'].'" ></span><span class="ont-btn" id="btn-edit-vlan-'.$getONT['idonu'].'" onclick="bdcomvlanonu_ajax('.$getONT['olt'].','.$getONT['idonu'].',\'bdcomformeditonu\')">змінити</span>':''));		
		if(!empty($resultONT['reason']))
			echo ont_label(($resultONT['status']==1?$lang['rereason']:$lang['reason']),tplreason($lang[$resultONT['reason']],$resultONT['reason']));		
		if(!empty($resultONT['uptime']))
			echo ont_label($lang['uptime'],convertuptime($resultONT['uptime']));		
		if(!empty($resultONT['typereg']))
			echo ont_label($lang['typereg'],$resultONT['typereg']);
		// BDCOM EPON - список МАС за ону
		if($getOLT['oidid']==1 && !empty($getOLT['snmprw']) && !$getOLT['username'] && $USER['class']>=5){
			echo'<span class="snmpreboot" onclick="rebootonu_ajax('.$getONT['olt'].','.$getONT['idonu'].',\'reboot\')">'.$lang['reboot'].' SNMP</span>';
		}		
		if($getOLT['oidid']==15 && !empty($getOLT['snmprw']) && $USER['class']>=5){
			echo'<span class="snmpreboot" onclick="rebootonu_ajax('.$getONT['olt'].','.$getONT['idonu'].',\'reboot\')">'.$lang['reboot'].'</span>';
			echo'<span class="dereg" onclick="rebootonu_ajax('.$getONT['olt'].','.$getONT['idonu'].',\'cdataderegonu\')">'.$lang['dereg'].'</span>';
			echo'<span class="delonu" onclick="rebootonu_ajax('.$getONT['olt'].','.$getONT['idonu'].',\'cdatadeletonu\')">'.$lang['delet'].'</span>';
		}
	}else{
		echo $lang['err_api_ont'];
	}
}
}
}
}

