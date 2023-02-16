<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
$oidid = isset($_POST['oidid']) ? Clean::int($_POST['oidid']): null;
$idonu = isset($_POST['idonu']) ? Clean::int($_POST['idonu']): null;
$act = isset($_POST['act']) ? Clean::text($_POST['act']): null;
switch($act){
	case 'reboot': 
		$SQLswitch = $db->Fast('switch','*',['id'=>$id]);
		$SQLonus = $db->Fast('onus','*',['idonu'=>$idonu]);
		if($USER['class']>=4 && !empty($SQLswitch['id']) && !empty($SQLswitch['snmprw']) && !empty($SQLonus['keyolt'])){ 
			if($SQLswitch['oidid']==1)
				snmp2_set($SQLswitch['netip'], $SQLswitch['snmprw'], "1.3.6.1.4.1.3320.101.10.1.1.29.".$SQLonus['keyolt'], i, "0");			
			if($SQLswitch['oidid']==15)
				snmp2_set($SQLswitch['netip'], $SQLswitch['snmprw'], "1.3.6.1.4.1.17409.2.3.4.1.1.17.".$SQLonus['keyolt'], i, "1");
			LogOnu($SQLonus['idonu'],$SQLswitch['id'],$lang['reboot_onu_users'],$USER['id']);
		}
	break;	
	case 'cdataderegonu': 	
		$SQLswitch = $db->Fast('switch','*',['id'=>$id]);
		$SQLonus = $db->Fast('onus','*',['idonu'=>$idonu]);
		if($SQLswitch['oidid']==15 && $USER['class']>=4 && !empty($SQLswitch['id']) && !empty($SQLswitch['snmprw']) && !empty($SQLonus['keyolt'])){ 
			snmp2_set($SQLswitch['netip'], $SQLswitch['snmprw'], "1.3.6.1.4.1.17409.2.3.4.1.1.17.".$SQLonus['keyolt'], i, "2");
			LogOnu($SQLonus['idonu'],$SQLswitch['id'],'dereg',$USER['id']);
		}	
	break;	
	case 'cdatadeletonu': 	
		$SQLswitch = $db->Fast('switch','*',['id'=>$id]);
		$SQLonus = $db->Fast('onus','*',['idonu'=>$idonu]);
		if($SQLswitch['oidid']==15 && $USER['class']>=4 && !empty($SQLswitch['id']) && !empty($SQLswitch['snmprw']) && !empty($SQLonus['keyolt'])){ 
			snmp2_set($SQLswitch['netip'], $SQLswitch['snmprw'], "1.3.6.1.4.1.17409.2.3.4.1.1.17.".$SQLonus['keyolt'], i, "6");
			LogOnu($SQLonus['idonu'],$SQLswitch['id'],'dereg',$USER['id']);
		}	
	break;	
	case 'bdcommaconu': 	
		$SQLswitch = $db->Fast('switch','*',['id'=>$id]);
		$SQLonus = $db->Fast('onus','*',['idonu'=>$idonu]);
		if($USER['class']>=4 && $SQLswitch['oidid']==1 && !empty($SQLswitch['id']) && !empty($SQLswitch['username']) && !empty($SQLswitch['password'])){ 
			$mac_after_port = telnet_ONU_bdcom_after_port($SQLswitch['netip'],null,$SQLswitch['username'],$SQLswitch['password'],$SQLonus['type'].$SQLonus['inface'],$SQLonus['mac']);
			echo'<div class="telnet_code"><h2>'.$lang['list_mac_port'].'</h2>';
			if($mac_after_port){
				echo $mac_after_port;
			}else{
				echo'';
			}
			echo'</div>';
		}
	break;	
	case 'bdcomformeditonusave': 	
		$SQLswitch = $db->Fast('switch','*',['id'=>$id]);
		$SQLonus = $db->Fast('onus','*',['idonu'=>$idonu]);
		$vlan = isset($_POST['vlan']) ? Clean::int($_POST['vlan']): null;
		if($USER['class']>=5 && $vlan && $SQLswitch['oidid']==1 && !empty($SQLswitch['id']) && !empty($SQLswitch['snmprw'])){ 
			@snmp2_set($SQLswitch['netip'],$SQLswitch['snmprw'], "1.3.6.1.4.1.3320.101.12.1.1.18.".$SQLonus['keyolt'].".1", 'i', "1");
			@snmp2_set($SQLswitch['netip'],$SQLswitch['snmprw'], "1.3.6.1.4.1.3320.101.12.1.1.3.".$SQLonus['keyolt'].".1", 'i', $vlan);
			@snmp2_set($SQLswitch['netip'],$SQLswitch['snmprw'], "1.3.6.1.4.1.3320.20.15.1.1.0", 'i', "1");
			sleep(1);
			$pvid = @snmp2_get($SQLswitch['netip'],$SQLswitch['snmpro'], "1.3.6.1.4.1.3320.101.12.1.1.3.".$SQLonus['keyolt'].".1");
			$VLANs = end(explode('INTEGER: ', $pvid));
			echo' -> '.$VLANs;
			LogOnu($SQLonus['idonu'],$SQLswitch['id'],$lang['edit_vlan'].''.$vlan,$USER['id']);
		}			
	break;	
	case 'savenamehuawei': 
		$name = isset($_POST['name']) ? Clean::text($_POST['name']): null;
		$SQLonus = $db->Fast('onus','*',['idonu'=>$id]);
		$SQLswitch = $db->Fast('switch','*',['id'=>$SQLonus['olt']]);
		if($name && $USER['class']>=5 && $SQLswitch['oidid']==7 && !empty($SQLonus['idonu'])){ 
			@$result = snmp2_set($SQLswitch['netip'],$SQLswitch['snmprw'], "1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.".$SQLonus['zte_idport'].".".$SQLonus['keyonu'],'s',$name);
			echo $name;
		}		
	break;
	case 'savenamezte': 
		$name = isset($_POST['name']) ? Clean::text($_POST['name']): null;
		$SQLonus = $db->Fast('onus','*',['idonu'=>$id]);
		$SQLswitch = $db->Fast('switch','*',['id'=>$SQLonus['olt']]);
		if($name && $USER['class']>=5 && $SQLswitch['oidid']==7 && !empty($SQLonus['idonu'])){ 
			@$result = snmp2_set($SQLswitch['netip'],$SQLswitch['snmprw'], "1.3.6.1.4.1.3902.1012.3.28.1.1.2.".$SQLonus['zte_idport'].".".$SQLonus['keyonu'],'s',$name);
			echo $name;
		}		
	break;	
	case 'savedescrzte': 
		$name = isset($_POST['name']) ? Clean::text($_POST['name']): null;
		$SQLonus = $db->Fast('onus','*',['idonu'=>$id]);
		$SQLswitch = $db->Fast('switch','*',['id'=>$SQLonus['olt']]);
		if($name && $USER['class']>=5 && $SQLswitch['oidid']==7 && !empty($SQLonus['idonu'])){ 
			@$result = snmp2_set($SQLswitch['netip'],$SQLswitch['snmprw'], "1.3.6.1.4.1.3902.1012.3.28.1.1.3.".$SQLonus['zte_idport'].".".$SQLonus['keyonu'],'s',$name);
			echo $name;
		}		
	break;	
	case 'zte3reboot': 	// ztec3xxx reboot
		$SQLonus = $db->Fast('onus','*',['idonu'=>$idonu]);
		$SQLswitch = $db->Fast('switch','*',['id'=>$SQLonus['olt']]);
		if(!empty($SQLswitch['snmprw']) && $USER['class']>=5 && $SQLswitch['oidid']==7 && !empty($SQLonus['idonu'])){ 
			@$result = snmp2_set($SQLswitch['netip'],$SQLswitch['snmprw'], '1.3.6.1.4.1.3902.1012.3.50.11.3.1.1.'.$SQLonus['zte_idport'].'.'.$SQLonus['keyonu'],'i','1');
		}
	break;	
	case 'huaweireboot':		
		$SQLswitch = $db->Fast('switch','*',['id'=>$id]);
		$SQLonus = $db->Fast('onus','*',['idonu'=>$idonu]);
		if($USER['class']>=4 && !empty($SQLswitch['id']) && !empty($SQLswitch['snmprw'])){ 
			@$result = snmp2_set($SQLswitch['netip'],$SQLswitch['snmprw'], '1.3.6.1.4.1.2011.6.128.1.1.4.23.1.20.'.$SQLonus['zte_idport'].'.'.$SQLonus['keyonu'],'i','1');
		}
	break;	
	case 'huaweidelonu':		
		$SQLswitch = $db->Fast('switch','*',['id'=>$id]);
		$SQLonus = $db->Fast('onus','*',['idonu'=>$idonu]);
		if($USER['class']>=4 && !empty($SQLswitch['id']) && !empty($SQLswitch['snmprw'])){ 
			@$result = snmp2_set($SQLswitch['netip'],$SQLswitch['snmprw'], '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.10.'.$SQLonus['zte_idport'].'.'.$SQLonus['keyonu'],'i','6');
			delete_onu($idonu);
		}
	break;	
	case 'zte3delonu':	
		$SQLswitch = $db->Fast('switch','*',['id'=>$id]);
		$SQLonus = $db->Fast('onus','*',['idonu'=>$idonu]);
		if($USER['class']>=4 && !empty($SQLswitch['id']) && !empty($SQLswitch['username']) && !empty($SQLswitch['password'])){ 
			$onu = ZTEllid2PortMatch($SQLonus['zte_idport']);
			$phptelnet = new PHPTelnet();
			$result = $phptelnet->Connect($SQLswitch['netip'],$SQLswitch['username'],$SQLswitch['password']);
			sleep(1);
			$phptelnet->DoCommand("conf t\n", $result);
			$phptelnet->DoCommand("interface gpon-olt_".$onu['shlef']."/".$onu['slot']."/".$onu['port']."\n", $result);
			$phptelnet->DoCommand("no onu ".$SQLonus['keyonu']."\n", $result);
			$phptelnet->DoCommand("yes\n", $result);
			delete_onu($idonu);
			sleep(2);
			$phptelnet->DoCommand("exit\n", $result);
			$phptelnet->DoCommand("exit\n", $result);
			sleep(1);
			$phptelnet->DoCommand("write\n", $result);
			sleep(1);
		}
	break;		
	case 'zte3configonu':	
		$SQLswitch = $db->Fast('switch','*',['id'=>$id]);
		$SQLonus = $db->Fast('onus','*',['idonu'=>$idonu]);
		if($USER['class']>=4 && !empty($SQLswitch['id']) && !empty($SQLswitch['username']) && !empty($SQLswitch['password'])){ 
			$onu = ZTEllid2PortMatch($SQLonus['zte_idport']);
			$phptelnet = new PHPTelnet();
			$result = $phptelnet->Connect($SQLswitch['netip'],$SQLswitch['username'],$SQLswitch['password']);
			sleep(1);
			$command = "show gpon onu detail-info gpon-onu_".$onu['shlef']."/".$onu['slot']."/".$onu['port'].":".$SQLonus['keyonu'];
			$phptelnet->DoCommand($command."\n", $result);
			$result = str_replace('  ',' ',str_replace($command,'',$result));
			$result = str_replace('--More--','',$result);
			$result = str_replace('','',$result);
			$arr_out = explode("\n",$result);	
			foreach($arr_out as $conf){
				if(trim($conf)){
					$onucfg .= ''.trim(preg_replace('/[\s]{2,}/', ' ', $conf)).'<br>';
				}
			}
			echo'<div id="ajaxablock"><pre>'.$onucfg.'</pre></div>';				
		}
	break;	
	case 'zte3mac': 	
		$SQLswitch = $db->Fast('switch','*',['id'=>$id]);
		$SQLonus = $db->Fast('onus','*',['idonu'=>$idonu]);
		if($USER['class']>=4 && !empty($SQLswitch['id']) && !empty($SQLswitch['username']) && !empty($SQLswitch['password'])){ 
			$onu = ZTEllid2PortMatch($SQLonus['zte_idport']);
			$phptelnet = new PHPTelnet();
			$result = $phptelnet->Connect($SQLswitch['netip'],$SQLswitch['username'],$SQLswitch['password']);
			sleep(1);
			$command = "show mac gpon onu gpon-onu_".$onu['shlef']."/".$onu['slot']."/".$onu['port'].":".$SQLonus['keyonu'];
			$phptelnet->DoCommand($command."\n", $result);
			if(preg_match("/Vlan/i",$result)) {
				$out = explode('-----', $result);
				$out = end($out);
				$arr_out = explode("\n", $out);
				foreach ($arr_out as $out_mac){
					if(preg_match("/#/i",$out_mac) || preg_match("/--/i",$out_mac)) {

					}else{
						$listmac .='<div class="block-for-mac">';
						$temponu = explode("  ",$out_mac);
						foreach($temponu as $on){
							$check = trim(str_replace(' ','',$on));
							if($check){
								$listmac .='<div class="block-for-mac-bl">'.$check.'</div>';
							}
						}
						$listmac .='</div>';
					}
				}
			}else{
				$listmac = 'missing mac';
			}
			echo'<div id="ajaxablock"><div class="blockmac">'.$listmac.'</div></div>';				
		}
	break;
	case 'offtvzteport1': 
		$SQLonus = $db->Fast('onus','*',['idonu'=>$id]);
		$SQLswitch = $db->Fast('switch','*',['id'=>$SQLonus['olt']]);
		if(!empty($SQLswitch['snmprw']) && $USER['class']>=5 && $SQLswitch['oidid']==7 && !empty($SQLonus['idonu'])){ 
			$result = @snmp2_set($SQLswitch['netip'],$SQLswitch['snmprw'], ".1.3.6.1.4.1.3902.1012.3.50.19.1.1.1.".$SQLonus['zte_idport'].".".$SQLonus['keyonu'].".1",'1',2);
		}		
	break;	
	case 'onzteport': 
		$port = isset($_POST['port']) ? Clean::int($_POST['port']): null;
		$SQLonus = $db->Fast('onus','*',['idonu'=>$idonu]);
		$SQLswitch = $db->Fast('switch','*',['id'=>$SQLonus['olt']]);
		if(!empty($SQLswitch['snmprw']) && $USER['class']>=5 && $SQLswitch['oidid']==7 && !empty($SQLonus['idonu'])){ 
			$onu = ZTEllid2PortMatch($SQLonus['zte_idport']);
			$phptelnet = new PHPTelnet();
			$result = $phptelnet->Connect($SQLswitch['netip'],$SQLswitch['username'],$SQLswitch['password']);
			sleep(1);
			$phptelnet->DoCommand("\n", $result);
			$phptelnet->DoCommand("conf t\n", $result);
			$phptelnet->DoCommand("pon-onu\n", $result);
			$phptelnet->DoCommand("pon-onu-mng gpon-onu_1/".$onu['slot']."/".$onu['port'].":".$SQLonus['keyonu']."\n", $result);
			$phptelnet->DoCommand("interface eth eth_0/".$port." state unlock\n", $result);
			$phptelnet->DoCommand("exit\n", $result);
			sleep(1);		
		}		
	break;	
	case 'offzteport': 
		$port = isset($_POST['port']) ? Clean::int($_POST['port']): null;
		$SQLonus = $db->Fast('onus','*',['idonu'=>$idonu]);
		$SQLswitch = $db->Fast('switch','*',['id'=>$SQLonus['olt']]);
		if(!empty($SQLswitch['snmprw']) && $USER['class']>=5 && $SQLswitch['oidid']==7 && !empty($SQLonus['idonu'])){ 
			$onu = ZTEllid2PortMatch($SQLonus['zte_idport']);
			$phptelnet = new PHPTelnet();
			$result = $phptelnet->Connect($SQLswitch['netip'],$SQLswitch['username'],$SQLswitch['password']);
			sleep(1);
			$phptelnet->DoCommand("\n", $result);
			$phptelnet->DoCommand("conf t\n", $result);
			$phptelnet->DoCommand("pon-onu\n", $result);
			$phptelnet->DoCommand("pon-onu-mng gpon-onu_1/".$onu['slot']."/".$onu['port'].":".$SQLonus['keyonu']."\n", $result);
			$phptelnet->DoCommand("interface eth eth_0/".$port." state lock\n", $result);
			$phptelnet->DoCommand("exit\n", $result);
			sleep(1);		
		}		
	break;	
	case 'bdcomformeditonu': 	
		$SQLswitch = $db->Fast('switch','*',['id'=>$id]);
		$SQLonus = $db->Fast('onus','*',['idonu'=>$idonu]);
		if($SQLswitch['oidid']==1 && !empty($SQLswitch['id']) && !empty($SQLswitch['snmprw'])){ 
			$pvid = @snmp2_get($SQLswitch['netip'],$SQLswitch['snmpro'], "1.3.6.1.4.1.3320.101.12.1.1.3.".$SQLonus['keyolt'].".1");
			if($pvid){
				$VLAN = end(explode('INTEGER: ', $pvid));
				echo'<span class="editvlan"><input id="vlan" type="text" value="'.$VLAN.'"><span class="ont-btn" style="margin:0;" onclick="bdcomvlanonu_ajax('.$SQLswitch['id'].','.$SQLonus['idonu'].',\'bdcomformeditonusave\')">'.$lang['save'].'</span></span>';
			}
		}
	break;	
	case 'hideonu': 
		if(!empty($USER['id'])){
			if($USER['hideonu']=='yes'){
				$hideonu = 'no';
			}else{
				$hideonu = 'yes';
			}
			$db->SQLupdate('users',['hideonu'=>$hideonu],['id'=>$USER['id']]);
		}
	break;	
	case 'rebootall': 
		/// перезавантажуємо всі ону на BDCOM EPON
		$SQLswitch = $db->Fast('switch','*',['id'=>$id]);
		if($oidid==1 && !empty($SQLswitch['id']) && !empty($SQLswitch['snmprw'])){ 
			snmp_set_quick_print(1);
			$result_array = @snmp2_real_walk($SQLswitch['netip'],$SQLswitch['snmpro'],'1.3.6.1.4.1.3320.101.10.1.1.26', 100000, 5);
			if($result_array){
				$total = 0;
				foreach ($result_array as $keys => $arr_value) {
					preg_match('/.1.1.26.([\d]+)$/',$keys,$ont);
					if($ont[1] && $arr_value==3){
						usleep(random_int(1,10));
						snmp2_set($SQLswitch['netip'], $SQLswitch['snmprw'], "1.3.6.1.4.1.3320.101.10.1.1.29.".$ont[1], i, "0");
						$total++;
					}
				}
			}
		}
		if($total){
			okno_title('Reboot ALL');
			echo form(['name'=>'Всього перезавантажено','descr'=>'','pole'=>$total.' ONU']);
			okno_end();
		}
	break;
}