<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
if($_POST['id']){
	$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
	if($id){
		$switch = $db->Fast('switch','*',['id'=>$id]);
		/// ZTE C320
		if(!empty($switch['id'])){
			if($switch['oidid']==7){
				snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
				@$resTypeSpeedFan = snmp2_real_walk($switch['netip'],$switch['snmpro'],'1.3.6.1.4.1.3902.1015.2.1.3.10.10.10.1.7');
				@$resTypeSlot = snmp2_real_walk($switch['netip'],$switch['snmpro'],'1.3.6.1.4.1.3902.1015.2.1.1.3.1.4.1.1');
				$typeid = 1;
				if($resTypeSlot){
					foreach($resTypeSlot as $key => $type){
						preg_match('/4.1.1.(\d+)/',$key,$m);
						$nameSlot = strtolower(str_replace('STRING:', '', str_replace(' ', '', str_replace('"', '', trim($type)))));
						$rSlot[$typeid]['name']= $nameSlot;	
						$rSlot[$typeid]['id']= $m[1];	
						$typeid ++;
					}
				}
				if(is_array($rSlot)){
					echo'<div class="olt_zte">';
					foreach($rSlot as $idslot => $valueslot){
						if($valueslot['name']=='smxa'){
							echo'<span class="slot"><b>SMXA/3</b> (GE/FE, 10GE/GE)</span>';
						}elseif($valueslot['name']=='gtgh'){
							echo'<span class="slot"><b>GTGH</b> GPON 16</span>';
						}elseif($valueslot['name']=='pram'){
							echo'<span class="slot"><b>PRAM</b> 220V — 48VDC</span>';
						}
					}
					if($resTypeSpeedFan){
						echo'<div class="blockstats1">';
						foreach($resTypeSpeedFan as $keyFan => $SpeedFan){
							echo'<span class="slotfan"><img class="rot" src="../style/img/extractor.png"><b>FAN </b> '.strtolower(str_replace('INTEGER:', '', str_replace(' ', '', str_replace('"', '', trim($SpeedFan))))).'</span>';
						}
						echo'</div>';						
					}
					echo'</div>';
				}
			}
		}
		/// BDCOM, CDATA
		if(!empty($switch['id'])){
			if($switch['oidid']==12 || $switch['oidid']==5 || $switch['oidid']==2 || $switch['oidid']==13 || $switch['oidid']==15 || $switch['oidid']==1  || $switch['oidid']==14 ){
				$res_snmp = get_curl_api(array('do' => 'device','id' => $id), true, 10);
				if(is_array($res_snmp)){
					if(is_array($res_snmp['result'])){
						foreach($res_snmp['result'] as $type => $value) {
							if($type=='cpu'){
								$inf .= '<div class="uptime_block"><img src="../style/img/cpu.png"><span class="data">'.$value.'%</span></div>';
							}elseif($type=='temp'){
								$inf .= '<div class="uptime_block"><img src="../style/img/temperature-control.png"><span class="data">'.$value.'°C</span></div>';
							}elseif($type=='firmware'){
								$inf .= '<div class="uptime"><img src="../style/img/temperature-control.png"><span class="data">'.$value.'</span></div>';
							}elseif($type=='name'){
								$inf .= '<div class="uptime"><img src="../style/img/temperature-control.png"><span class="name">'.$lang['name'].'</span><span class="data">'.$value.'</span></div>';
							}elseif($type=='uptime'){
								$inf .= '<div class="uptime"><img src="../style/img/uptime.png"><span class="name">'.$lang['uptime'].'</span><span class="data">'.$value.'</span></div>';
							}
						}
					}
					echo'<div id="cssfon">'.$inf.'</div>';
				}
			}
		}
		// not registered ZTE
		if($switch['oidid']==7 && $resNoRegOnu){
			echo'<a class="urlnoregonu" href="/?do=regonu&id='.$switch['id'].'"><div class="not_registered"><span>Список не зареєстрованих onu</span></div></a>';
		}		
		// HUAWEI
		if($switch['oidid']==14){
			@$temp1 = snmp2_get($switch['netip'],$switch['snmpro'],'1.3.6.1.4.1.2011.6.2.1.3.1.1.1.0');
			if($temp1){
				$volt = strtolower(str_replace('INTEGER:', '', str_replace(' ', '', str_replace('"', '', trim($temp1)))))/1000;
			}
			@$resTypeSlot = snmp2_real_walk($switch['netip'],$switch['snmpro'],'1.3.6.1.4.1.2011.2.6.7.1.1.2.1.7.0');
			$typeid = 1;
			if($resTypeSlot){
				foreach($resTypeSlot as $key => $type){
					preg_match('/1.2.1.7.0.(\d+)/',$key,$m);
					$nameSlot = strtolower(str_replace('STRING:', '', str_replace(' ', '', str_replace('"', '', trim($type)))));
					$rSlot[$typeid]['name']= $nameSlot;	
					$rSlot[$typeid]['id']= $m[1];	
					$typeid ++;
				}
			}
			if(is_array($rSlot)){
				echo'<div class="olt_zte">';
				foreach($rSlot as $idslot => $valueslot){
					if($valueslot['name']=='h803epfd'){
						echo'<span class="slotplat"><img src="../style/img/network.png"><b>H803EPFD</b>EPON 16 портів</span>';
					}elseif($valueslot['name']=='h806gpbd'){
						echo'<span class="slotplat"><img src="../style/img/network.png"><b>H806GPBD</b>GPON 8 портів</span>';
					}elseif($valueslot['name']=='h801x2cs'){
						echo'<span class="slotplat"><img src="../style/img/ethernetslot.png"><b>H801X2CS</b>2/10GE</span>';
					}elseif($valueslot['name']=='h801mcud'){
						echo'<span class="slotplat"><img src="../style/img/ethernetslot.png"><b>H801MCUD</b>4x100/1000Mbps</span>';
					}elseif($valueslot['name']=='h801mpwd'){
						echo'<span class="slotplat"><img src="../style/img/danger.png"><b>H801MPWD</b> 400W</span>';
					}elseif($valueslot['name']=='h809epbd'){
						echo'<span class="slotplat"><img src="../style/img/network.png"><b>H809EPBD</b>EPON 8 портів</span>';
					}elseif($valueslot['name']=='h802scun'){
						echo'<span class="slotplat"><img src="../style/img/ethernetslot.png"><b>H802SCUN</b>4x100/1000Mbps</span>';
					}elseif($valueslot['name']=='h801gicf'){
						echo'<span class="slotplat"><img src="../style/img/ethernetslot.png"><b>H801GICF</b>2x100/1000Mbps</span>';
					}
				}
				if($volt)
					echo'<span class="slotplat"><img src="../style/img/hwvolt.png"><b>Батарея</b>'.$volt.' Вольт</span>';
				echo'</div>';
			}
		}
	}
}

