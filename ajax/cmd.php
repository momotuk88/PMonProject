<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
$onu = isset($_POST['onu']) ? Clean::int($_POST['onu']): null;
$olt = isset($_POST['olt']) ? Clean::int($_POST['olt']): null;
$act = isset($_POST['act']) ? Clean::text($_POST['act']): null;
switch($act){
	case 1: 	
		$dataOnu = $db->Fast('onus','*',['idonu'=>$onu]);
		$dataSwitch = $db->Fast('switch','*',['id'=>$dataOnu['olt']]);
		if(!empty($dataSwitch['username']) && !empty($dataSwitch['password']) && !empty($dataOnu['type'])){
			$phptelnet = new PHPTelnet();
			$result = $phptelnet->Connect($dataSwitch['netip'],$dataSwitch['username'],$dataSwitch['password']);
			$phptelnet->DoCommand("enable\n", $result);
			$phptelnet->DoCommand("config\n", $result);
			$onu_name = 'epon0/3:1';
			$phptelnet->DoCommand("show running-config interface $onu_name \r\n", $result);
			$returncmd = $result;		
			if($returncmd){
				$out = explode("Current configuration:\r\n!\r\n", $returncmd);
				$out = end($out);
				okno_title('Налаштування інтерфейсу');
				echo'<textarea name="code" rows="5" cols="33" class="telnetcmd">'.ClearTelnet($out).'</textarea>';
				okno_end();	
			}
		}
	break;	
	case 2: 
		// bdcom show vlan
		$phptelnet = new PHPTelnet();
		$result = $phptelnet->Connect($dataSwitch['netip'],$dataSwitch['username'],$dataSwitch['password']);
		$phptelnet->DoCommand("enable\n", $result);
		$phptelnet->DoCommand("config\n", $result);
		$phptelnet->DoCommand("show vlan\n", $result);
		$returncmd = ClearTelnet($result);		
		$out = explode(' -----', $returncmd);
		$out = end($out);
		$arr_out = explode("\n", $out);	
		$arr_out = str_replace("-","",$arr_out);
		if($arr_out){
			okno_title('Список всіх VLAN на комутаторі');
			echo'<pre>';
			foreach ($arr_out as $out_temp){
				if($out_temp){
					echo $out_temp;
				}
			}
			echo'</pre>';
			okno_end();	
		}
	break;
	case 3:
		// bdcom show mac onu
		$dataOnu = $db->Fast('onus','*',['idonu'=>$onu]);
		$dataSwitch = $db->Fast('switch','*',['id'=>$dataOnu['olt']]);
		if(!empty($dataSwitch['username']) && !empty($dataSwitch['password']) && !empty($dataOnu['type'])){			
			$onus = mb_strtolower($dataOnu['type'].''.$dataOnu['inface']);
			$phptelnet = new PHPTelnet();
			$result = $phptelnet->Connect($dataSwitch['netip'],$dataSwitch['username'],$dataSwitch['password']);	
			$phptelnet->DoCommand("enable\n", $result);
			$phptelnet->DoCommand("show mac address-table in﻿terface $onus \r\n", $result);
			if($result){
				okno_title('Список всіх MAC на ONU: '.$onus);
				$out = explode(' -----', $result);
				$out = end($out);
				$arr_out = explode("\n", $out);	
					while (trim(array_pop($arr_out)) == "--More--") {
					fwrite($con, chr(32));
					sleep(2);
					$arr_tmp = explode("\r\n", fread($con, 16536));
					$arr_out = array_merge($arr_out,$arr_tmp);
				}
				$listmac = "";
				foreach ($arr_out as $out_mac){
					$out_mac = str_word_count($out_mac,1,'0123456789.');
					if (!isset($out_mac[1])){
						continue;
					}
					$vlan = $out_mac[0];
					$mac = $out_mac[1];
					$mac = strtoupper($mac);
					$mac = str_split($mac);
					if (!isset($mac[13])){
						continue;
					}
					$mac = mb_strtolower($mac[0].$mac[1].':'.$mac[2].$mac[3].':'.$mac[5].$mac[6].':'.$mac[7].$mac[8].':'.$mac[10].$mac[11].':'.$mac[12].$mac[13]);
					if ($mac == mb_strtolower($dataOnu['mac'])){
						continue;
					}
					if($mac)
						$listmac .= '<span class="bdcom_mac" onclick="copymac(\''.$mac.'\');">'.$mac.' <i class="fi fi-rr-copy-alt"></i></span>';
				}
				echo $listmac;
				okno_end();	
			}			
		}
	break;
	case 4:	
		$dataOnu = $db->Fast('onus','*',['idonu'=>$onu]);
		$dataSwitch = $db->Fast('switch','*',['id'=>$dataOnu['olt']]);
		if(!empty($dataSwitch['username']) && !empty($dataSwitch['password']) && !empty($dataOnu['type'])){			
			$onus = mb_strtolower($dataOnu['type'].''.$dataOnu['inface']);
			$phptelnet = new PHPTelnet();
			$result = $phptelnet->Connect($dataSwitch['netip'],$dataSwitch['username'],$dataSwitch['password']);	
			$phptelnet->DoCommand("enable\n", $result);
			$phptelnet->DoCommand("epon reboot onu interface $onus \r\n", $result);
			if($result){
				okno_title('Перезавантажено ONU: '.$onus);
				echo'ONU <b>'.$onus.'</b> перезавантажено i записо в журнал';
				okno_end();					
			}
		}
	break;
	case 5:
		$dataSwitch = $db->Fast('switch','*',['id'=>$olt]);
		$db->SQLinsert('swcron',['oltid' => $dataSwitch['id'],'status' => 'yes','priority' => 3,'added' => date('Y-m-d H:i:s')]);
		$jobid = $db->getInsertId();
		$db->SQLupdate('switch',['jobid'=>$jobid],['id'=>$dataSwitch['id']]);
		Socketbackground(array('olt'=>$dataSwitch['id'],'jobid'=>$jobid));
	break;
	case 6:
		$dataOnu = $db->Fast('onus','*',['idonu'=>$onu]);
		$dataSwitch = $db->Fast('switch','*',['id'=>$dataOnu['olt']]);
		if(!empty($dataSwitch['username']) && !empty($dataSwitch['password']) && !empty($dataOnu['type'])){			
			$onus = mb_strtolower($dataOnu['type'].''.$dataOnu['inface']);
			$phptelnet = new PHPTelnet();
			$result = $phptelnet->Connect($dataSwitch['netip'],$dataSwitch['username'],$dataSwitch['password']);	
			$phptelnet->DoCommand("enable\n", $result);
			$phptelnet->DoCommand("show running-config interface $onus \r\n", $result);
			$out = explode("Current configuration:\r\n!\r\n", $result);
			$out = ClearTelnet(end($out));
			if($result){
				okno_title('Конфігурація: '.$onus);
				echo "<pre>\n";
				$arr_out = explode("\r\n", $out);
				array_pop($arr_out);
				foreach ($arr_out as $value) {
					echo "$value\n";
				}
				echo "</pre>";
				okno_end();					
			}
		}	
	break;
	case 7:	
		$dataSwitch = $db->Fast('switch','*',['id'=>$olt]);
		if(!empty($dataSwitch['username']) && !empty($dataSwitch['password'])){
			$phptelnet = new PHPTelnet();
			$result = $phptelnet->Connect($dataSwitch['netip'],$dataSwitch['username'],$dataSwitch['password']);	
			$phptelnet->DoCommand("reboot\n", $result);
			$phptelnet->DoCommand("y\n", $result);
			okno_title('Перезавантажено');
			echo "Готово";
			okno_end();				
		}
	break;
	case 8:
		$dataSwitch = $db->Fast('switch','*',['id'=>$olt]);
		if(!empty($dataSwitch['username']) && !empty($dataSwitch['password'])){
			$phptelnet = new PHPTelnet();
			$result = $phptelnet->Connect($dataSwitch['netip'],$dataSwitch['username'],$dataSwitch['password']);	
			$phptelnet->DoCommand("enable\n", $result);
			$phptelnet->DoCommand("write all\n", $result);
			sleep(1);
			$phptelnet->DoCommand("quit\n", $result);			
			okno_title('Конфігурація: '.$ONU);
			echo "Готово";
			okno_end();				
		}
	break;
}
?>
