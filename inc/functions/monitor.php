<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
function checkerONU($data,$olt){
	global $db, $config, $PMonTables, $lang;
	foreach($data as $ios => $eachsigs) {
		$super = $db->Simple('SELECT idonu, added FROM `'.$PMonTables['onus'].'` WHERE olt = '.$eachsigs['id'].'
		AND type = "'.($eachsigs['pon']=='gpon'?'gpon':'epon').'"
		AND keyonu = "'.$eachsigs['keyonu'].'" '.(!empty($eachsigs['keyport'])?'AND zte_idport = "'.$eachsigs['keyport'].'"':'').'
		ORDER BY added ASC LIMIT 1');	
		if(!empty($super['idonu'])){
			$db->SQLupdate($PMonTables['onus'],['cron' => 1],['idonu' => $super['idonu']]);
		}
	}
	$getdeletonu = $db->Multi($PMonTables['onus'],'*',['olt' => $olt,'cron' => 2]);
	if(count($getdeletonu)){
		foreach($getdeletonu as $io => $eachsig) {
			if(!empty($eachsig['idonu'])){
				$db->query('DELETE FROM onus WHERE idonu = '.$eachsig['idonu']);
				$db->query('DELETE FROM onus_comm WHERE idonu = '.$eachsig['idonu']);
				$db->query('DELETE FROM onus_log WHERE onuid = '.$eachsig['idonu']);
				$db->query('DELETE FROM unitponboxont WHERE onuid = '.$eachsig['idonu']);
				$db->query('DELETE FROM historysignal WHERE onu = '.$eachsig['idonu']);
				$db->query('DELETE FROM monitoronu WHERE idonu = '.$eachsig['idonu']);
				$db->SQLinsert($PMonTables['swlog'],['deviceid' =>$eachsig['olt'],'types' =>'deletonu','message' =>$lang['deletonu'].$eachsig['type'].$eachsig['inface'],'added' =>date('Y-m-d H:i:s')]);
			}
		}
	}
}
function reasonGponHuawei($data){
	switch ($data) {
		case '1':						
			return 'err6'; // LOS
		break;				
		case '2':						
			return 'err36'; // LOSi(Loss of signal for ONUi) or LOBi (Loss of burst for ONUi)
		break;				
		case '3':						
			return 'err37'; // LOFI(Loss of frame of ONUi)
		break;			
		case '4':						
			return 'err38'; // SFI(Signal fail of ONUi)
		break;	
		case '5':						
			return 'err39'; //  LOAI(Loss of acknowledge with ONUi)
		break;	
		case '6':						
			return 'err40'; // LOAMI(Loss of PLOAM for ONUi)
		break;	
		case '7':						
			return 'err41'; // deactive ONT fails
		break;	
		case '8':						
			return 'err42'; // deactive ONT success
		break;	
		case '9':						
			return 'err43'; // reset ONT
		break;	
		case '10':						
			return 'err44'; // re-register ONT
		break;	
		case '11':						
			return 'err45'; // pop up fail 
		break;	
		case '13':						
			return 'err1';							
		break;				
		case '15':						
			return 'err46'; // LOKI(Loss of key synch with ONUi) 
		break;				
		case '18':						
			return 'err47'; // deactived ONT due to the ring 
		break;				
		case '30':						
			return 'err48'; // shut down ONT optical module
		break;				
		case '31':						
			return 'err49'; // reset ONT by ONT command
		break;				
		case '32':						
			return 'err50'; // reset ONT by ONT reset button
		break;				
		case '33':						
			return 'err51'; // reset ONT by ONT software
		break;				
		case '34':						
			return 'err52'; // deactived ONT due to broadcast attack
		break;				
		case '35':						
			return 'err53'; // operator check fail
		break;				
		case '37':						
			return 'err54'; // a rogue ONT detected by itself
		break;	
		case '-1':						
			return 'err6';	
		break;			
		case '255':						
			return 'err1';	
		break;
		default:	
			return 'err20';						
	}
}
function portstatusHuawei($value){
	if(preg_match('/1/i',$value) || preg_match('/4/i',$value)){
		return 'up';	
	}else{
		return 'down';	
	}
}
function MacHuawei($type) {
		if (preg_match("/Hex/i", $type)) {
			$re_z_z = explode('Hex-STRING: ', $type);
			$re_z = end($re_z_z);
			$re_z = str_replace('"', '',$re_z);
			$re_z = trim($re_z);
			$onu = preg_replace("/\s+/","",mb_strtolower($re_z));
		}elseif(preg_match("/STRING/i", $type)) {
			$re_ze_mac = explode('STRING: ', $type);
			$re_mac = end($re_ze_mac);
			$re_mac = str_replace('"', '',$re_mac);
			$re_mac = trim($re_mac);
			$onu = bin2hex($re_mac);
		}
		$onu = preg_replace('/(.{2})/','\1:',$onu,5);
		return $onu;
}
function cdataGpon($type) {
		if (preg_match("/Hex/i", $type)) {
			$re_z_z = explode('Hex-STRING: ', $type);
			$re_z = end($re_z_z);
			$onu = str_replace('"', '',$re_z);
		}elseif(preg_match("/STRING/i", $type)) {
			$re_ze_mac = explode('STRING: ', $type);
			$re_mac = end($re_ze_mac);
			$onu = str_replace('"', '',$re_mac);
		}
		return $onu;
}
function SnHuawei($type) {
		if (preg_match("/Hex/i", $type)) {
			$re_z_z = explode('Hex-STRING: ', $type);
			$re_z = end($re_z_z);
			$re_z = str_replace('"', '',$re_z);
			$re_z = trim($re_z);
			$onu = preg_replace("/\s+/","",mb_strtolower($re_z));
		}elseif(preg_match("/STRING/i", $type)) {
			$re_ze_mac = explode('STRING: ', $type);
			$re_mac = end($re_ze_mac);
			$re_mac = str_replace('"', '',$re_mac);
			$re_mac = trim($re_mac);
			$onu = bin2hex($re_mac);
		}
		return $onu;
}
function explodeRowsTwo($data) {
	$result = explode("\n\n", $data);
	return $result;
}
function explodeRows($data) {
	$result = explode("\n\n", $data);
	return $result;
}
function PMonStats(){
	global $config, $db, $PMonTables;	
	$SQLbadonu = $db->SimpleWhile("SELECT idonu FROM onus WHERE rx BETWEEN '-".$config['badsignalstart']."' AND '-".$config['badsignalend']."'");
	$getOnline = $db->Multi($PMonTables['onus'],'idonu',['status'=>1]);
	$getAll = $db->Multi($PMonTables['onus']);
	$getOffline = (int)count($getAll) - count($getOnline);
	$db->SQLinsert($PMonTables['pmonstats'],['datetime'=>date('Y-m-d H:i:s'),'countonu'=>count($getAll),'badsignal'=>count($SQLbadonu),'online'=>count($getOnline),'offline'=>$getOffline]);
}
function SignalMonitor($newstatus,$newrx,$oldrx,$idonu){
		global $config, $db, $PMonTables, $time;
		$insertSignal = false;
		$old = 0;
		if(!empty($oldrx)) 
			$old = signal_onu_minus($oldrx);
		if(!empty($newrx)) 
			$new = signal_onu_minus($newrx);
		if($old < $new){				
			$up_dbm = $new - $old;
			if($config['criticsignal']<=$up_dbm && $newstatus==1 && $up_dbm){
				$update['rxstatus'] = 'up'; // $langrx = '{сигнал збільшився на '.$up_dbm.'}';
				$update['lastrx'] = $oldrx;
				$update['changerx'] = $time;
				$insertSignal = true;
			}
		}elseif($old > $new){
			$down_dbm = $old - $new;
			if($config['criticsignal']<$down_dbm && $newstatus==1 && $down_dbm ){
				$update['rxstatus'] = 'down'; // $langrx = '{сигнал зменшився на '.$down_dbm.'}';
				$update['lastrx'] = $oldrx;
				$update['changerx'] = $time;
				$insertSignal = true;
			}
		}elseif($old === $new){
			$update['rxstatus'] = 'none';	
		}else{	
			$update['rxstatus'] = 'none';		
		}		
		if(isset($update)){
			$db->SQLupdate($PMonTables['onus'],$update,['idonu' => $idonu]);
		}
		return $insertSignal;
	}
function formatOID($oid,$keyonu,$keyport){
	$dataSNMP = str_replace('keyonu',$keyonu,$oid);
	$dataSNMP = str_replace('keyport',$keyport,$dataSNMP);
	$dataSNMP = trim($dataSNMP);
	return $dataSNMP;
}
function clInteger($dataSNMP){
	$dataSNMP = str_replace('INTEGER:', '',$dataSNMP);
	$dataSNMP = str_replace('"', '',$dataSNMP);
	$dataSNMP = str_replace(' ', '',$dataSNMP);
	$dataSNMP = trim($dataSNMP);
	if(preg_match('/up/i',$dataSNMP) || preg_match('/down/i',$dataSNMP)){
		$dataSNMP = $dataSNMP;
	}else{
		if($dataSNMP==1){
			$dataSNMP='up';
		}elseif($dataSNMP==2){
			$dataSNMP='down';
		}else{
			$dataSNMP='down';	
		}
	}
	return $dataSNMP;
}
function telegram_port($type) {
	global $config;
	if($config['telegram']=='on' && $type){
		$content = array('chat_id' => $config['telegramchatid'],'text' => $type,'parse_mode'=>'HTML','disable_notification'=>false);
		file_get_contents('https://api.telegram.org/bot'.$config['telegramtoken'].'/sendmessage?'.http_build_query($content));	
	}
}
function savePortMonitor($place,$name,$descr,$status,$laststatus,$portid,$deviceid,$sms){
	global $db, $lang;
	$time = date('Y-m-d H:i:s');
	$send = false;
	$status = clInteger($status);
	if($laststatus=='down' && $status=='up'){
		$SQLupd['timeup'] = $time;
		$SQLupd['operstatus'] = 'up';
		$send = true;
		$text = '✅<b>RECOVERY:</b> '.$place.' Порт: <b>'.$name.'</b>'.($descr?'-['.$descr.']':'').' - UP';
	}elseif($laststatus=='up' && $status=='down'){
		$SQLupd['timedown'] = $time;
		$SQLupd['operstatus'] = 'down';
		$send = true;
		$text = '⛔️<b>PROBLEM:</b> '.$place.' Порт: <b>'.$name.'</b>'.($descr?'-['.$descr.']':'').' - DOWN';
	}elseif($laststatus=='down' && $status=='down'){

	}elseif($laststatus=='none' && $status=='up'){
		$SQLupd['timeup'] = $time;
		$SQLupd['operstatus'] = 'up';
	}elseif($laststatus=='none' && $status=='down'){
		$SQLupd['timedown'] = $time;
		$SQLupd['operstatus'] = 'down';
	}else{
		
	}
	$SQLupd['updates'] = $time;
	$db->SQLupdate('switch_port',$SQLupd,['id'=>$portid]);
	if($send){
		$dataInsert['status'] = $SQLupd['operstatus'];
		$dataInsert['deviceid'] = $deviceid;
		$dataInsert['portid'] = $portid;
		$dataInsert['added'] = $time;
		if($deviceid && $portid && !empty($SQLupd['operstatus']) && $sms=='yes'){
			$db->SQLinsert('swlogport',$dataInsert);
			telegram_port($text);
		}
	}
}
function saveErrPort($deviceid,$llid,$result,$nameport,$place){
	global $db, $lang;
	$result['in'] = str_replace('4294967295', 0,$result['in']);
	$dataLastPortError = $db->Simple("SELECT * FROM `switch_port_err` WHERE `llid` = '".(int)$llid."' AND `deviceid` = '".(int)$deviceid."' ORDER BY `added` DESC LIMIT 1");	
	if(!empty($result['in'])){
		if(!empty($dataLastPortError['inerror']) && $dataLastPortError['inerror']<$result['in']){
			$SQLInsert['status_inerror'] = 'up';
		}else{
			$SQLInsert['status_inerror'] = 'no';	
		}	
	}	
	if(!empty($SQLInsert['status_inerror']) && $SQLInsert['status_inerror']=='up' && !empty($dataLastPortError['id']))
		$SQLInsert['newin'] = $result['in']-$dataLastPortError['inerror'];
	$SQLInsert['deviceid'] = $deviceid;
	$SQLInsert['llid'] = $llid;	
	$SQLInsert['inerror'] = ($result['in']?$result['in']:0);
	$SQLInsert['added'] = date('Y-m-d H:i:s');	
	if(!empty($SQLInsert['status_inerror']))
		$db->SQLinsert('switch_port_err',$SQLInsert);
}
function getFormatSNMP($dataSNMP,$format){
	switch($format){
		case 'string':		
			$dataSNMP = str_replace('STRING:', '',$dataSNMP);
			$dataSNMP = str_replace('"', '',$dataSNMP);
			$dataSNMP = str_replace(' ', '',$dataSNMP);
			$dataSNMP = trim($dataSNMP);
		break;			
		case 'hex-string':		
			$dataSNMP = str_replace('Hex-STRING:', '',$dataSNMP);
			$dataSNMP = str_replace('STRING:', '',$dataSNMP);
			$dataSNMP = str_replace('"', '',$dataSNMP);
			$dataSNMP = str_replace(' ', '',$dataSNMP);
			$dataSNMP = trim($dataSNMP);
		break;			
		case 'integer':	
			$dataSNMP = str_replace('INTEGER:', '',$dataSNMP);
			$dataSNMP = str_replace('"', '',$dataSNMP);
			$dataSNMP = str_replace(' ', '',$dataSNMP);
			$dataSNMP = trim($dataSNMP);
		break;			
	}	
	if(!$dataSNMP) 
		$dataSNMP = false;		
	return $dataSNMP;	
}
function getNameBdcomport($result){
	$result = getFormatSNMP($result,'string'); 
	$result = str_replace('N0', 'N 0/',$result); 
	$result = str_replace('t0', 't 0/',$result); 
	return $result;	
}
function getNameHuaweiport($result){
	$result = getFormatSNMP($result,'string'); 
	$result = str_replace('N0', 'N 0',$result); 
	$result = str_replace('t0', 't 0',$result); 
	return $result;	
}
function getNameZteport($result){
	$result = getFormatSNMP($result,'string'); 
	$result = str_replace('gpon_', 'GPON ',$result); 
	$result = str_replace('i_1', 'i 1',$result); 
	return $result;	
}
function getNameCdataport($result){
	$result = str_replace('e', 'e ',$result); 
	$result = str_replace('pon', 'gpon ',$result); 
	$result = str_replace('i_1', 'i 1',$result); 
	return mb_strtoupper($result);	
}
function getNameDlink1106($result){
	$result = getFormatSNMP($result,'string'); 
	return $result;	
}
function getNamesDlink1106($result){
	$nametag = 'Ethernet 0/';
	$nametagsfp = 'SFP 0/';
	return ($result==6?$nametagsfp:$nametag).$result;
}
function getTypePortDlink1106($result){
	$nametag = 'eth1000';
	$nametagsfp = 'sfp';
	return ($result==6?$nametagsfp:$nametag);
}
function signal_onu_minus($var) {
	$var = str_replace('-','',$var);
	return (int)$var;
}
function getTypePort($value){
	if(preg_match('/xgei/i',$value)){
		return 'xgei'; // xgei 
	}elseif(preg_match('/gei/i',$value)){
		return 'gei'; // gei 
	}elseif(preg_match('/gpon/i',$value)){
		return 'gpon';
	}elseif(preg_match('/epon/i',$value)){
		return 'epon';
	}elseif(preg_match('/rxolt/i',$value)){
		return '';	
	}elseif(preg_match('/TGigaEthernet/i',$value)){
		return 'sfp'; // gei (интерфейс 1000M Ethernet)	
	}elseif(preg_match('/GigaEthernet/i',$value)){
		return 'sfp'; // gei (интерфейс 1000M Ethernet)	
	}elseif(preg_match('/FastEthernet/i',$value)){
		return 'eth100'; // gei (интерфейс 1000M Ethernet)	
	}elseif(preg_match('/Mng1/i',$value)){
		return 'mng1'; // gei (интерфейс 1000M Ethernet)	
	}
}
function getTypePortHuawei($value){
	$value = strtolower($value);
	if(preg_match('/xgei/i',$value)){
		return 'xgei'; 
	}elseif(preg_match('/xge/i',$value)){
		return 'xge'; 
	}elseif(preg_match('/gei/i',$value)){
		return 'gei'; 
	}elseif(preg_match('/ge/i',$value)){
		return 'ge'; 
	}elseif(preg_match('/gpon/i',$value)){
		return 'gpon';
	}elseif(preg_match('/epon/i',$value)){
		return 'epon';
	}elseif(preg_match('/rxolt/i',$value)){
		return '';	
	}elseif(preg_match('/ethernet/i',$value)){
		return 'sfp'; // gei (интерфейс 1000M Ethernet)	
	}elseif(preg_match('/GigaEthernet/i',$value)){
		return 'sfp'; // gei (интерфейс 1000M Ethernet)	
	}elseif(preg_match('/FastEthernet/i',$value)){
		return 'eth100'; // gei (интерфейс 1000M Ethernet)	
	}elseif(preg_match('/Mng1/i',$value)){
		return 'mng1'; // gei (интерфейс 1000M Ethernet)	
	}
}
function clearDataMacRe($value){
	$value = str_replace('Hex-STRING:', '',$value);
	$value = str_replace('STRING:', '',$value);
	$value = str_replace('INTEGER:', '',$value);
	$value = str_replace('"', '',$value);
	$value = str_replace(' ', '',$value);
	$value = trim($value);	
	return $value;
}
function clearData1108($value){
	$value = str_replace('STRING:', '',$value);
	$value = str_replace('"', '',$value);
	$value = str_replace('EPON System, GE-', 'GigaEthernet 0/',$value);
	$value = str_replace('EPON System, PON-', 'epon 0/',$value);
	$value = trim($value);	
	return $value;
}
function ClearDataMac($value) {
	$value = clearDataMacRe($value);
	if (strlen($value)===17) $value = str_replace(' ','',$value);
	$value = trim($value," \"");
    $value = trim($value,'"');
    $value = stripslashes($value);
	if (strlen($value)< 10) $value = strtoupper(bin2hex($value));
	return preg_replace('/(.{2})/','\1:',mb_strtolower($value),5);
}
function fileCacheArray($DataArray,$Filename='test.cache',$Folder='export/switchcache/'){
	$filename = $Folder.$Filename;
	if($DataArray){
		$Savedata = serialize($DataArray);
		file_put_contents($filename, $Savedata);
	}
	$Result = file_get_contents($filename);
	return unserialize($Result);
}
function post_system_health($jobid){
	global $config;
	if(!empty($config['url']) && $jobid){
		$ch = curl_init($config['url'].'/port.php');
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,['jobid'=>$jobid]);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);	
		curl_setopt($ch,CURLOPT_HEADER,true); 
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($ch,CURLOPT_TIMEOUT,1); 
		$json = curl_exec($ch);
		curl_close($ch);
	}
}
function post_system($jobid,$olt){
	global $config;
	if(!empty($config['url']) && $olt && $jobid){
		$ch = curl_init($config['url'].'/system.php');
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,['olt'=>$olt,'jobid'=>$jobid]);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);	
		curl_setopt($ch,CURLOPT_HEADER,true); 
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($ch,CURLOPT_TIMEOUT,1); 
		$json = curl_exec($ch);
		curl_close($ch);
	}
}
function get_curl_api($post_array, $decode = false, $time = 3){
	global $config;
	$ch = curl_init($config['monitorapi']);
	curl_setopt($ch,CURLOPT_POST, true);
	curl_setopt($ch,CURLOPT_POSTFIELDS, $post_array);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);	
	curl_setopt($ch,CURLOPT_HEADER,false); 
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 3); // таймаут для соединения с хостом
	curl_setopt($ch,CURLOPT_TIMEOUT, 10); // таймаут для сессии
	$json = curl_exec($ch);
	curl_close($ch);
	if($decode){
		$res_api = json_decode($json,true);	
	}else{
		$res_api = $json;	
	}
	return $res_api;
}
?>