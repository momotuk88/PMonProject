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
	$db->query('UPDATE `'.$PMonTables['onus'].'` SET apiget = apiget+1 WHERE idonu = '.$getONT['idonu']);
	// ZTE C3xx
	if($getOLT['oidid']==7 && $resultONT['status']==1){
		echo'<script type="text/javascript">ajaxzte320('.$id.');</script><div id="ajaxzte320"></div>';
	}	
	// HUAWEI 5608	
	if($getOLT['oidid']==14){
		echo'<script type="text/javascript">ajaxhuawei5608('.$id.');</script><div id="onthuawei5608"></div>';
	}
	if(is_array($resultONT)){
		if(!empty($resultONT['adminstatus'])){
			$adminstatus =  str_replace('down', 'off',str_replace('up', 'on',$resultONT['adminstatus']));
			echo ont_label('Admin Status','<div class="ajax_ont_status_'.$adminstatus.'">'.$resultONT['adminstatus'].'</div>');
		}
		if(!empty($resultONT['operstatus'])){
			$operstatus =  str_replace('down', 'off',str_replace('up', 'on',$resultONT['operstatus']));
			echo ont_label('Operation Status','<div class="ajax_ont_status_'.$operstatus.'">'.$resultONT['operstatus'].'</div>');
		}	
		// C-DATA 16xx
		if($getOLT['oidid']==12){
			echo ont_label($lang['oid_gpon_descr'],'<div id="resname">'.$resultONT['name'].'</div>');
		}		
		if(!empty($resultONT['status'])){
			$status = ($resultONT['status']==1?'<div class="ajax_ont_status_on">'.$lang['online'].'</div>':'<div class="ajax_ont_status_off">'.$lang['offline'].'</div>');
			echo ont_label($lang['status'],$status.$btn_update_info);
		}	
		if(!empty($getONT['added'])){
			echo ont_label($lang['registers'],$getONT['added']);
		}			
		if($resultONT['status']==1 && !empty($resultONT['online'])){
			echo ont_label($lang['online'],aftertime($resultONT['online']));
		}			
		if($resultONT['status']==2){
			echo ont_label($lang['offline'],aftertime($getONT['offline']));
		}	
		if(!empty($resultONT['reason']))
			echo ont_label(($resultONT['status']==1?$lang['rereason']:$lang['reason']),tplreason($lang[$resultONT['reason']],$resultONT['reason']));		
		// ZTE C3xx		
		if(!empty($resultONT['wan']) && $resultONT['status']==1 && $getOLT['oidid']==7){
			echo ont_label($lang['wanport'],'<img src="../style/img/'.(!empty($resultONT['wanportzte']['txt']) ? $resultONT['wanportzte']['img'] : 'eth'.$resultONT['wan']).'.png" class="onueth">');
			if(is_array($resultONT['wanportzte']) && !empty($resultONT['wanportzte']['txt']))
				echo ont_label($lang['typeport'],$resultONT['wanportzte']['txt']);
		}
		// HUAWEI 56xx
		if($getOLT['oidid']==14){
			if(!empty($resultONT['onuerror']))
				echo ont_label($lang['coutporterr'],'<font color=red>'.$resultONT['onuerror'].'</font>');			
			if(!empty($resultONT['linervlan']))
				echo ont_label('inner-vlan',$resultONT['linervlan']);
			if(!empty($resultONT['uservlan'])){
				echo ont_label('user-vlan',$resultONT['uservlan']);
				$Sport = @snmp2_get($getOLT['netip'],$getOLT['snmpro'],'1.3.6.1.4.1.2011.5.14.5.5.1.7.'.$getONT['zte_idport'].'.4.'.$getONT['keyonu'].'.4294967295.4294967295.1.'.$resultONT['uservlan']);
				if($Sport){
					$srvport = clearDataMacRe($Sport)-1;
					echo ont_label('Service-port',$srvport);
				}
			}
			if(!empty($resultONT['countmacport']) && $resultONT['status']==1)
				echo ont_label($lang['countmac'],'<span style="color:#1e9cd9;text-decoration:underline;">'.$resultONT['countmacport'].' (пристроїв)</span>');			
			if(!empty($resultONT['name'])){
				$editpenhuawei = ' <span class="zte_edit_name" onclick="showblockform1();"><img src="../style/img/edit.png"></span>';
				$formpenhuawei = '<div id="form_rename"><textarea name="nameonu" id="nameonu" class="namezteonu">'.$resultONT['name'].'</textarea><input type="submit" class="saverenzte" onclick="snmpsetsave('.$id.',\'savenamehuawei\');"  value="'.$lang['edit'].'"></div>';
				echo ont_label($lang['opis'],'<div id="resname">'.$resultONT['name'].'</div>'.$editpenhuawei.$formpenhuawei);
			}			
			if(!empty($resultONT['bias'])){
				$laser = descr_huawei_laser_bias($resultONT['bias']);
				echo ont_label('Laser Bias Current bar','<img class="bias_img" src="../style/img/'.$laser['img'].'.png">');	
				echo ont_label('Laser Bias Current','<span class="bias_text">'.$resultONT['bias'].' (mA) <div class="bias_'.$laser['img'].'">'.$laser['alarm'].'</div></span>');	
			}				
			if(!empty($resultONT['service']))
				echo ont_label('Service profile ONU',$resultONT['service']);			
			if(!empty($resultONT['linepro']))
				echo ont_label('Line profile ONU',$resultONT['linepro']);			
		}		
		if(!empty($resultONT['mac'])){
			echo ont_label('MAC',$resultONT['mac']);	
		}
		// ZTE C3xx		
		if(!empty($resultONT['mngtvlan']) && $getOLT['oidid']==7){
			$editpenztevlan = ' <span class="zte_edit_vlan" onclick="showblockform2();"><img src="../style/img/edit.png"></span>';
			$formpenztevlan = '<div id="formeditvlan"><input type="text" name="vlan" id="vlan" class="namezteonu" value="'.$resultONT['mngtvlan'].'"><input type="submit" class="saverenzte" onclick="snmpsetsave('.$id.',\'savevlanzte\');" value="'.$lang['edit'].'"></div>';
			echo ont_label('Vlan',$resultONT['mngtvlan'].$editpenztevlan.$formpenztevlan);
		}
		// ZTE C3xx
		if(!empty($resultONT['name']) && $getOLT['oidid']==7){
			$editpenzte = ' <span class="zte_edit_name" onclick="showblockform1();"><img src="../style/img/edit.png"></span>';
			$formpenzte = '<div id="form_rename"><textarea name="nameonu" id="nameonu" class="namezteonu">'.$resultONT['name'].'</textarea><input type="submit" class="saverenzte" onclick="snmpsetsave('.$id.',\'savenamezte\');" value="'.$lang['edit'].'"></div>';
			echo ont_label($lang['opis'],'<div id="resname">'.$resultONT['name'].'</div>'.$editpenzte.$formpenzte);	
		}	
		// ZTE C3xx		
		if(!empty($resultONT['note']) && $getOLT['oidid']==7){
			$editpenzte = '<span class="zte_edit_note" onclick="showblockform3();"><img src="../style/img/edit.png"></span>';
			$formpenzte = '<div id="form_renote"><textarea name="noteonu" id="noteonu" class="namezteonu">'.$resultONT['note'].'</textarea><input type="submit" class="saverenzte" onclick="snmpsetsave('.$id.',\'savedescrzte\');" value="'.$lang['edit'].'"></div>';
			echo ont_label($lang['opis'],'<div id="resnote">'.$resultONT['note'].'</div>'.$editpenzte.$formpenzte);	
		}	
		// BDCOM EPON
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
		// ZTE C3xx
		if(!empty($resultONT['config']))
			echo ont_label($lang['cfgonuinf'],$resultONT['config']);		
		if(!empty($resultONT['dist']))
			echo ont_label($lang['dist'],$resultONT['dist'].' '.$lang['metr']);
		// History RX
		$getHistoryRx = $db->Multi($PMonTables['historyrx'],'*',['device' => $getOLT['id'],'onu' => $id]);
		if(!empty($resultONT['rx']) && $resultONT['status']==1){
			echo ont_label('RX ',signalTerminal($resultONT['rx']).(count($getHistoryRx)?'<a href="/?do=signal&id='.$id.'" class="ont-graph-rx">'.$lang['keygraphsignal'].'</a>':''));
		}else{
			echo ont_label('RX ',signalTerminal($resultONT['rx']).(count($getHistoryRx)?'<a href="/?do=signal&id='.$id.'" class="ont-graph-rx">'.$lang['keygraphsignal'].'</a>':''));
		}
		if(!empty($resultONT['lastrx']))
			echo ont_label($lang['lastrx'],signalTerminal($resultONT['lastrx']));
		if(!empty($resultONT['tx']) && $resultONT['status']==1)
			echo ont_label('TX ',signalTerminal($resultONT['tx']));		
		if(!empty($resultONT['temp']) && $resultONT['status']==1)
			echo ont_label('Temp ',$resultONT['temp'].' °C');
		if(!empty($resultONT['rxolt']) && $resultONT['status']==1)
			echo ont_label('RX OLT ',signalTerminal($resultONT['rxolt']));
		if(!empty($resultONT['model']) || !empty($resultONT['vendor']))
			echo ont_label($lang['model'],$resultONT['vendor'].' '.$resultONT['model']);
	
		// BDCOM EPON - зміна влан
		if($getOLT['oidid']==1){
			if(!empty($resultONT['pvid']))
				echo ont_label($lang['bdcom_vlan'],$resultONT['pvid'].(!empty($getOLT['snmprw'])?'<span id="edit-vlan-'.$getONT['idonu'].'" ></span><span class="ont-btn" id="btn-edit-vlan-'.$getONT['idonu'].'" onclick="bdcomvlanonu_ajax('.$getONT['olt'].','.$getONT['idonu'].',\'bdcomformeditonu\')">'.$lang['edit'].'</span>':''));		
			if(!empty($resultONT['uptime']))
				echo ont_label($lang['uptime'],convertuptime($resultONT['uptime']));		
			if(!empty($resultONT['typereg']))
				echo ont_label($lang['typereg'],$resultONT['typereg']);
			// BDCOM EPON - список МАС за ону
			if($getOLT['oidid']==1 && !empty($getOLT['snmprw']) && !$getOLT['username'] && $USER['class']>=5){
				echo'<span class="snmpreboot" onclick="rebootonu_ajax('.$getONT['olt'].','.$getONT['idonu'].',\'reboot\')">'.$lang['reboot'].' SNMP</span>';
			}		
		}
		// C_DATA
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
?>