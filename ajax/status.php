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
				@$resNoRegOnu = snmp2_real_walk($switch['netip'],$switch['snmpro'],'1.3.6.1.4.1.3902.1012.3.13.3.1.2');
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
			if($switch['oidid']==5 || $switch['oidid']==2 || $switch['oidid']==13 || $switch['oidid']==15 || $switch['oidid']==1 ){
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
	}
}

