<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
$act = isset($_POST['act']) ? Clean::text($_POST['act']): null;
switch($act){
	case 'noregonuzte': 	
		$dataSwitch = $db->Fast('switch','*',['id'=>$id]);
		if(!empty($dataSwitch['snmpro']) && !empty($dataSwitch['snmprw'])){
			$cmd_show_vlan_summary_zte = 'show vlan summary';
			$phptelnet = new PHPTelnet();
			$result = $phptelnet->Connect($dataSwitch['netip'],$dataSwitch['username'],$dataSwitch['password']);
			$phptelnet->DoCommand($cmd_show_vlan_summary_zte."\r", $result);
			$vlan = getResultVlan($result);
			if($vlan)
				$vlanlist = getAllVlanFromula($vlan);
			if(is_array($vlanlist)){
				$selectvlan = '<select class="input_select" name="vlan" id="vlan">';
				foreach($vlanlist as $key => $vlans){
					$selectvlan .= '<option value="'.$key.'">Vlan '.$vlans['vlan'].'</option>';
				}
				$selectvlan .='</select>';
			}		
			@$resNoRegOnu = snmp2_real_walk($dataSwitch['netip'],$dataSwitch['snmpro'],'1.3.6.1.4.1.3902.1012.3.13.3.1.2');	
			if($resNoRegOnu){
				echo'<div class="head_noreg"><div class="sn">SN/Серійний</div><div class="key">Інтерфейс</div><div class="port">Vlan</div><div class="knopka"></div></div>';
				foreach($resNoRegOnu as $key => $sn){
					preg_match('/1012.3.13.3.1.2.(\d+).(\d+)/',$key,$m);
					if(isset($m[1]) && isset($m[2])){
						$onusn = ZTEToSerial($sn);
						echo'<form id="regonu-'.$m[1].$m[2].'"><input type="hidden" id="type" name="type" value="gpon"><input type="hidden" id="sn" name="sn" value="'.$onusn.'"><input type="hidden" id="keyport" name="keyport" value="'.$m[1].'"><input type="hidden" id="keyonu" name="keyonu" value="'.$m[2].'"><input type="hidden" id="zteolt" name="zteolt" value="'.$dataSwitch['id'].'">';
						echo'<div class="head_noreg_list"><div class="sn">'.$onusn.'</div>';
						echo'<div class="key">'.ZTEllid2Port($m[1],$m[2]).'</div>';
						echo'<div class="port">'.$selectvlan.'</div>';
						echo'<div class="knopka"><span class="regonu" onclick="nextregonuzte('.$m[1].$m[2].')">Реєстрація</span></div></div>';
						echo'</form><div id="nextreg-'.$m[1].$m[2].'"></div>';
					}
				}
			}
		}
	break;	
	case 'nextregonuzte': 		
		$type = isset($_POST['data']['type']) ? Clean::text($_POST['data']['type']) : null;
		$zteolt = isset($_POST['data']['zteolt']) ? Clean::int($_POST['data']['zteolt']) : null;
		$dataSwitch = $db->Fast('switch','*',['id'=>$zteolt]);
		if(!empty($dataSwitch['username']) && !empty($dataSwitch['password'])){		
			if($type=='gpon'){
				$vlan = isset($_POST['data']['vlan']) ? Clean::text($_POST['data']['vlan']) : null;
				$sn = isset($_POST['data']['sn']) ? Clean::text($_POST['data']['sn']) : null;
				$keyport = isset($_POST['data']['keyport']) ? Clean::int($_POST['data']['keyport']) : null;
				$keyonu = isset($_POST['data']['keyonu']) ? Clean::int($_POST['data']['keyonu']) : null;
				$zteslot = ZTEllid2PortMatch($keyport);
				$tcountprofile = ZTEgetTcontProfileTable($dataSwitch['netip'],$dataSwitch['snmpro']);
				echo'<div class="formregonu"><div class="pole">';
				echo'<div><b>SN/Серійний</b><input class="forminput" name="sn" value="'.$sn.'" style="width:200px;"></div><div><b>Vlan</b><input class="forminput" name="vlan" value="'.$vlan.'" style="width:100px;"></div></div><div class="pole">';
				$tcountprofile = ZTEgetProfile($dataSwitch['netip'],$dataSwitch['snmpro']);
				if(is_array($tcountprofile)){
					$selectprofile = '<select class="inputonuselect" name="profile" id="profile">';
					foreach($tcountprofile as $key => $profile){
						$selectprofile .= '<option value="'.$key.'"> '.$profile['profile'].'</option>';
					}
					$selectprofile .='</select>';
				}				
				echo'<div><b>Профіль</b>'.$selectprofile.'</div><div><b>Switch port mode</b><select name="swmode" class="inputonuselect"><option value="access">access</option><option value="hybrid">hybrid</option><option value="transparent">transparent</option><option value="trunk" selected>trunk</option></select></div>';
				echo'</div><div class="pole">';
				echo'<div><b>GPON</b></div>';
				echo'<div><b>_</b><input class="forminput" name="slotnm" value="'.$zteslot['shlef'].'"></div>';
				echo'<div><b>/</b><input class="forminput" name="cardnm" value="'.$zteslot['slot'].'"></div>';
				echo'<div><b>/</b><input class="forminput" name="portnm" value="'.$zteslot['port'].'"></div>';
				echo'<div><b>:</b><input class="forminput" name="onunm" value="'.$keyonu.'"></div>';				
				echo'</div><div class="pole"><button type="submit" class="btnregonu">Реєстрація</button></div>';
				echo'</div>';
			}			
			if($type=='epon'){
				
			}
		}
	break;	
	case 'regonuzte': 	
		$type = isset($_POST['data']['type']) ? Clean::text($_POST['data']['type']) : null;
		$sn = isset($_POST['data']['sn']) ? Clean::text($_POST['data']['sn']) : null;
		$keyport = isset($_POST['data']['keyport']) ? Clean::int($_POST['data']['keyport']) : null;
		$keyonu = isset($_POST['data']['keyonu']) ? Clean::int($_POST['data']['keyonu']) : null;
		$zteolt = isset($_POST['data']['zteolt']) ? Clean::int($_POST['data']['zteolt']) : null;
		$vlan = isset($_POST['data']['vlan']) ? Clean::int($_POST['data']['vlan']) : null;
		$dataSwitch = $db->Fast('switch','*',['id'=>$zteolt]);
		if(!empty($dataSwitch['username']) && !empty($dataSwitch['password']) && $vlan){		
			if($type=='gpon'){		
				$onu = ZTEllid2PortMatch($keyport);
				$phptelnet = new PHPTelnet();
				$result = $phptelnet->Connect($dataSwitch['netip'],$dataSwitch['username'],$dataSwitch['password']);
				$phptelnet->DoCommand("conf t\n", $result);
				$phptelnet->DoCommand("interface gpon-olt_1/".$onu['slot']."/".$onu['port']."\n", $result);
				$phptelnet->DoCommand("onu ".$keyonu." type ONU_1G sn ".$sn."\n", $result);
				$phptelnet->DoCommand("onu ".$keyonu." profile line 1000mb remote standart\n", $result);
				$phptelnet->DoCommand("exit\n", $result);
				sleep(1);
				$phptelnet->DoCommand("interface gpon-onu_1/".$onu['slot']."/".$onu['port'].":".$keyonu."\n", $result);
				sleep(1);
				$phptelnet->DoCommand("switchport mode trunk vport 1\n", $result);
				sleep(1);
				$phptelnet->DoCommand("switchport vlan ".$vlan." tag vport 1\n", $result);
				sleep(1);
				$phptelnet->DoCommand("pon-onu-mng gpon-onu_1/".$onu['slot']."/".$onu['port'].":".$keyonu."\n", $result);
				$phptelnet->DoCommand("vlan port eth_0/1 mode tag vlan ".$vlan."\n", $result);
				sleep(1);
				$phptelnet->DoCommand("exit\n", $result);
				$phptelnet->DoCommand("write\n", $result);
				sleep(2);
				$phptelnet->DoCommand("exit\n", $result);
			}
		}
	break;	
}
