<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
function ZTEAllVlan($netip,$snmpro) {
if($netip && $snmpro){@$getAllVlanList = snmp2_real_walk($netip,$snmpro,'1.3.6.1.4.1.3902.3.102.1.1.1.1');if($getAllVlanList){
	foreach($getAllVlanList as $key => $vlan){
		preg_match('/3902.3.102.1.1.1.1.(\d+)/',$key,$mvlan);		
		$dataVlan[$mvlan[1]]['vlan'] = $mvlan[1];
		$namevlan = snmp2_get($netip,$snmpro,'.1.3.6.1.4.1.3902.3.102.1.1.1.6.'.$mvlan[1]);
		$dataVlan[$mvlan[1]]['name'] = str_replace ('STRING:','',str_replace (' ','',str_replace ('"','',$namevlan)));		
		}}}return (isset($dataVlan) ? $dataVlan : null);
}
function ZTEllid2Port($llid,$on) {
$lx=sprintf("%08x",$llid);
	switch ($lx[0]) {
	case '1':
		$sh=hexdec($lx[1])+1;
		$sl=hexdec($lx[2].$lx[3]);
		$ol=hexdec($lx[4].$lx[5]);
	break;
	case '2':
		$sh=hexdec($lx[3]);
		$sl=hexdec($lx[4].$lx[5]);
		$ol=hexdec($lx[6].$lx[7]);
		if ($cl>16) {
			$cl-=16; $sl++;
		}
			$ol1=$ol;
		break;
	case '3':
		$sh=hexdec($lx[1])+1;
		$sl=hexdec($lx[2].$lx[3]);
		$ol=($sl&0x07)+1;
		$sl=$sl>>3;
		$on = hexdec($lx[4].$lx[5]);
	break;
	case '6':
		$sh=hexdec($lx[1])+1;
		$sl=hexdec($lx[2].$lx[3]);
		$ol=0;
	break;
	}
	return "{$sh}/{$sl}/{$ol}:{$on}";
}
function getResultVlan($result){
	$out = explode("Details are following:",$result);
	$out = end($out);
	$arr_out = explode("\r\n",$out);
	array_pop($arr_out);
	foreach ($arr_out as $out_mac){
		$out_mac = str_word_count($out_mac,1,'0123456789,-');
		if(is_null($out_mac[1])){
			if(!empty($out_mac[0])){
				$data = $out_mac[0];
			}
		}
		
	}
	return isset($data) ? $data : null;
}
function getAllVlanFromula($temp){
	$out = explode(',', $temp);
	if($out){
		foreach($out as $key => $tem){
			if (preg_match('/-/i',$tem)){
				#preg_match('/(\d+)-(\d+)/',$tem,$mat);
				#for ($i = $mat[1]; $i <= $mat[2]; $i++) {
				#	$vlan[$i]['vlan'] = $i;
				#}
			}else{
				#$vlan[$tem]['vlan'] = $tem;
			}
			$vlan[$tem]['vlan'] = $tem;
		}
	}
	return isset($vlan) ? $vlan : null;
}
function ZTEllid2PortMatch($llid) {
$lx=sprintf("%08x",$llid);
	switch ($lx[0]) {
	case '1':
		$sh=hexdec($lx[1])+1;
		$sl=hexdec($lx[2].$lx[3]);
		$ol=hexdec($lx[4].$lx[5]);
	break;
	case '2':
		$sh=hexdec($lx[3]);
		$sl=hexdec($lx[4].$lx[5]);
		$ol=hexdec($lx[6].$lx[7]);
		if ($cl>16) {
			$cl-=16; $sl++;
		}
			$ol1=$ol;
		break;
	case '3':
		$sh=hexdec($lx[1])+1;
		$sl=hexdec($lx[2].$lx[3]);
		$ol=($sl&0x07)+1;
		$sl=$sl>>3;
		$on = hexdec($lx[4].$lx[5]);
	break;
	case '6':
		$sh=hexdec($lx[1])+1;
		$sl=hexdec($lx[2].$lx[3]);
		$ol=0;
	break;
	}
	$data['shlef'] = $sh;
	$data['slot'] = $sl;
	$data['port'] = $ol;
	return $data;
}
function ZTEgetTcontProfileTable($netip,$snmpro){
	@$result = snmp2_real_walk($netip,$snmpro,"1.3.6.1.4.1.3902.1012.3.26.1.1.2");
	$i=1;
	if($result){
		foreach ($result as $key => $value) {
			preg_match('/1012.3.26.1.1.2.(\d+)/',$key,$m);
			$data[$i]['idtariff'] = $m[1];
			$value = preg_replace('/STRING:/','',$value);
			$value = preg_replace('/"/','',$value);
			$data[$i]['tariff'] = $value;
			$i++;
		}
	}
	return isset($data) ? $data : null;
}
function ZTEgetProfile($netip,$snmpro){
	@$result = snmp2_real_walk($netip,$snmpro,"1.3.6.1.4.1.3902.1012.3.28.1.1.1");
	$i=1;
	if($result){
		foreach ($result as $key => $value) {
			preg_match('/1012.3.28.1.1.1.(\d+)/',$key,$m);
			$data[$i]['profileid'] = $m[1];
			$value = preg_replace('/STRING:/','',$value);
			$value = preg_replace('/"/','',$value);
			$data[$i]['profile'] = $value;
			$i++;
		}
	}
	return isset($data) ? $data : null;
}
function ZTEToSerial($tempsn) {
		if (strpos($tempsn,'Hex-STRING') !== false){
			$tempsn = preg_replace('~^.*?( = )~i','',$tempsn);
			$tempsn = preg_replace('/Hex-STRING/','',$tempsn);
			$tmpv = explode(" ",$tempsn);
			$val1 = hexdec($tmpv[1]);
			$val2 = hexdec($tmpv[2]);
			$val3 = hexdec($tmpv[3]);
			$val4 = hexdec($tmpv[4]);
			$val5 = $tmpv[5];
			$val6 = $tmpv[6];
			$val7 = $tmpv[7];
			$val8 = $tmpv[8];
			return chr($val1).chr($val2).chr($val3).chr($val4).$val5.$val6.$val7.$val8;
		}else{
			$tempsn = preg_replace('~^.*?( = )~i','',$tempsn);
			$onu_snc1 = preg_replace ('/STRING:/','',$tempsn);
			$tmpv = explode(" ","$onu_snc1");
			$tmpe = str_split($tmpv[1]);
			return $tmpe[1].$tmpe[2].$tmpe[3].$tmpe[4].strtoupper(dechex(ord($tmpe[5]))).strtoupper(dechex(ord($tmpe[6]))).strtoupper(dechex(ord($tmpe[7]))).strtoupper(dechex(ord($tmpe[8])));
		}
	}
function typeOnuzteVideoPort($typetv){
	$typetv = preg_replace('~^.*?( = )~i','',$typetv);
	$typetv = preg_replace('/INTEGER: 1/','1',$typetv);
	$typetv = preg_replace('/INTEGER: 2/','2',$typetv);
	$typetv = preg_replace('/INTEGER: 65535/','65535',$typetv);
	$type = preg_replace('/No Such Instance currently exists at this OID/',0,$typetv);
	switch($type){
		case 1: 
			$res['img']='tv';	
			$res['st']='up';
		break;   
		case 2: 
			$res['img']='tv';	
			$res['st']='disable';
		break;			
		case 65535: 
			$res['img']='tv';	
			$res['st']='down';
		break;			
	}
	return $res;
}
function typeOnuztePort($snmptype){
		$snmptype = preg_replace('~^.*?( = )~i','',$snmptype);
		$snmptype = preg_replace ('/INTEGER: 5/','5',$snmptype);
		$snmptype = preg_replace ('/INTEGER: 6$/','6',$snmptype);
		$snmptype = preg_replace ('/INTEGER: 65535/','65535',$snmptype);
		$snmptype = preg_replace ('/No Such Instance currently exists at this OID/','0',$snmptype);
		$type = preg_replace ('/INTEGER: 1/','1',$snmptype);
		switch($type){
			case 6: 
				$result1['img']='zte6.png';	
				$result1['txt']='1 Gbps';
				$result1['st']='enable';
				$result1['status']='up';	
			break; 			
			case 3: 
				$result1['img']='zte3.png';	
				$result1['txt']='10 Mbps';
				$result1['st']='enable';
				$result1['status']='up';	
			break;   
			case 5: 
				$result1['img']='zte5.png';	
				$result1['txt']='100 Mbps';	
				$result1['st']='enable';
				$result1['status']='up';	
			break;
			case 1: 
				$result1['img']='zte0.png';	
				$result1['txt']='Offline';
				$result1['st']='disable';	
				$result1['status']='disable';					
			break;
			case 0: 
				$result1['img']='zte0.png';	
				$result1['txt']='Down';	
				$result1['st']='disable';
				$result1['status']='down';	
			break;
		}
		return $result1;
	}
function ClearTelnet($result) {
	$result = str_replace("Switch_config#","",$result);
	$result = str_replace("show vlan","",$result);
	$result = str_replace("Switch#","",$result);
	return $result;
}
function cl1($cmd) {
	$cmd = str_replace('VLAN','',$cmd);
	$cmd = str_replace('Status','',$cmd);
	$cmd = str_replace('Name','',$cmd);
	$cmd = str_replace('Ports','',$cmd);
	$cmd = str_replace('-','',$cmd);
	$cmd = preg_replace("/\s+/", " ", $cmd);
	$cmd = trim($cmd);
	return  $cmd;
}
function telegram_sms($type) {
	global $config;
	if($config['telegram']=='on' && $type){
		$content = array('chat_id' => $config['telegramchatid'],'text' => $type,'parse_mode'=>'HTML','disable_notification'=>false);
		file_get_contents('https://api.telegram.org/bot'.$config['telegramtoken'].'/sendmessage?'.http_build_query($content));	
	}
}
function ListSwitchMonitor(){
	global $config, $db, $PMonTables;
	$data = array();
	$SQLListDevice = $db->Multi('switch','*',['monitor'=>'yes']);
	if(count($SQLListDevice)){
		foreach($SQLListDevice as $value){
			$data[$value['id']]['id'] = $value['id'];
			$data[$value['id']]['place'] = $value['place'];
			$data[$value['id']]['netip'] = $value['netip'];
			$data[$value['id']]['location'] = $value['location'];
			$data[$value['id']]['updates'] = $value['updates'];
			$data[$value['id']]['model'] = $value['inf'].' '.$value['model'];
		}
	}
	return $data;
}
function ListOnuLog($id,$olt){
	global $config, $db, $lang, $PMonTables;
	if($id){
		$SQL = $db->Multi('onus_log','*',['onuid' => $id, 'olt' => $olt]);
		if(count($SQL)){
			$styleTPL ='<div class="ont-block"><h1>'.$lang['log_onu'].' <a class="closelog" href="/?do=onu&id='.$id.'">'.$lang['close'].'</a></h1><div class="ont-content">';
			foreach($SQL as $onu => $data){
				$styleTPL .='<div id="log-'.$data['id'].'" class="log-onu">';
				$styleTPL .='<div class="d">'.$data['added'].'</div>';
				$styleTPL .='<div class="t">'.$data['descr'].'</div>';
				$dataUser = $db->Fast('users','username,id',['id'=>$data['user']]);
				$styleTPL .='<div class="u">'.$dataUser['username'].'</div>';
				$styleTPL .='</div>';
			}
			$styleTPL .='</div></div>';
			return $styleTPL;
		}
	}
}
function LogOnu($idonu,$idolt,$descr,$user){
	global $db, $time;
	$SQLdata['onuid'] = $idonu;
	$SQLdata['olt'] = $idolt;
	$SQLdata['added'] = $time;
	$SQLdata['descr'] = $descr;
	if($user)
		$SQLdata['user'] = $user;
	$db->SQLinsert('onus_log',$SQLdata);
}
function highlight_word($title,$searched_word) {
    return str_ireplace($searched_word,'<font color=red>'.$searched_word.'</font>',$title); // replace content
}
function getTypeConnectFiber($getconnect,$getconnectid){
	global $config, $db, $lang, $PMonTables;	
	if($getconnect==1){
		
	}elseif($getconnect==2){
		return  $db->Fast($PMonTables['myfta'],'*',['id' => $getconnectid]);
	}elseif($getconnect==3){
		return $db->Fast($PMonTables['unitponbox'],'*',['id' => $getconnectid]);
	}else{
		return false;
	}	
}
function listonumdunoCSS($id){
	global $config, $db, $lang, $PMonTables;
	$SQLListONU = $db->SimpleWhile('SELECT unitponboxont.onuid as onu_id_box, unitponboxont.status, onus.idonu as onu_id, onus.inface as inface, onus.type as pontype, onus.mac as onumac, onus.sn as sn, onus.status as onustatus, onus.dist as onudist, onus.rx as onusignal FROM `unitponboxont`,`onus` WHERE unitponboxont.ponboxid = '.$id.' AND onus.idonu = unitponboxont.onuid ORDER BY onus.status ASC,onus.rx ASC ');
	if(count($SQLListONU)){
		foreach($SQLListONU as $onu => $value){
			$data[$onu] = $value;
		}
	}
	return $data;
}
function listonumdu($id){
	global $config, $db, $lang, $PMonTables;
	$sql = 'SELECT unitponboxont.onuid as onu_id_box, unitponboxont.status, onus.idonu as onu_id, onus.inface as inface, onus.type as pontype, onus.mac as onumac, onus.sn as sn, onus.status as onustatus, onus.dist as onudist, onus.rx as onusignal FROM `unitponboxont`,`onus` WHERE unitponboxont.ponboxid = '.$id.' AND onus.idonu = unitponboxont.onuid ORDER BY onus.status ASC,onus.rx ASC ';
	$SQLListONU = $db->SimpleWhile($sql);
	if(count($SQLListONU)){
		foreach($SQLListONU as $onu => $value){
			$signal = str_replace('0.00', '',$value['onusignal']);
			$data .='<a class=map_url_pon '.($value['onustatus']==2?'style=\"color:red;\"':'').'>';
			$data .='<div class=map_onu>';
			$data .='<div class=status_map_onu>'.($value['onustatus']==1?'<span class=online_map></span>':'<span class=offline_map></span>').'</div>';
			$data .='<div class=inface_map_onu><span>'.$value['pontype'].' '.$value['inface'].'</span></div>';
			if(!empty($value['onudist']))
				$data .='<div class=dist_map_onu><span>'.$value['onudist'].'м</span></div>';
			if($signal)
				$data .='<div class=rx_map_onu>'.styleRxMap($value['onusignal']).'</div>';
			$data .='</div>';
			$data .='</a>';
		}
	}
	return $data;
}
function styleRxMap($rx){
	$signal = $rx;
	if(!$signal)
		$signal = false;
	if($signal=='-70')
		$signal = false;
	$signala = str_replace('-', '',$signal);
	$signala = (int)$signala;
	if($signala>1 AND $signala<=12){
		$result='<span class=rx0>'.$rx.'</span>';
	}elseif($signala>=13 AND $signala<=17){
		$result='<span class=rx1>'.$rx.'</span>';
	}elseif($signala>=18 AND $signala<=24){
		$result='<span class=rx2>'.$rx.'</span>';
	}elseif($signala>=25 AND $signala<=29){
		$result='<span class=rx4>'.$rx.'</span>';
	}elseif($signala>=30 AND $signala<=70){
		$result='<span class=rx5>'.$rx.'</span>';
	}
	return $result;
}
function mapfibers($id){
global $config, $db, $lang, $PMonTables;
$SQLListFiber = $db->Multi($PMonTables['fibermap']);
$data = array();
if(count($SQLListFiber)){
foreach($SQLListFiber as $fiber){
$sqlFiber = $db->Fast($PMonTables['fiberlist'],'*',['id'=>$fiber['fiberid']]);
$fiber1 .='var fiber'.$fiber['fiberid'].' = '.$fiber['geo'].'
';
$fiber2 .="var polyline = L.polyline(fiber".$fiber['fiberid'].",{color:'".(!empty($fiber['color'])?$fiber['color']:'#fff')."',clickable: 'true'}).bindTooltip('<b>Тип оптики: </b>".$lang[$sqlFiber['typesfiber']]."<br>".(!empty($sqlFiber['metr']) ? "<b>Довжина кабелю: </b>".$sqlFiber['metr']."м<br>":"")."<b>Встановлено: </b>".$sqlFiber['added']."').addTo(map);
";				
}}
$data['var'] = $fiber1;
$data['fiber'] = $fiber2;
return $data;
}
function mapponbox($id){
global $config, $db, $lang, $PMonTables;
$SQLListPonbox = $db->Multi($PMonTables['unitponbox'],'*',['unitid'=>$id]);
$marker ='';
if(count($SQLListPonbox)){
foreach($SQLListPonbox as $ponbox){
if(!empty($ponbox['lan']) && !empty($ponbox['lon'])){
$komplekt = komplektmdu($ponbox['id']);
$listFiber = listfibermdu($ponbox['id']);
$marker .='L.marker(['.$ponbox['lan'].','.$ponbox['lon'].'],{icon:'.imgmdu($ponbox).'}).bindPopup("<div id=\"det-pon\"><a class=\"m-n-ponbox\" href=\"/?do=pon&act=view&id='.$ponbox['id'].'\">'.$ponbox['name'].'<span class=\"triangle-right\"></span><\/a>'.(!empty($ponbox['count'])?'<b>ONT:</b> '.$ponbox['count'].' ':'').(!empty($ponbox['online']) ? '<font color=\"#5ed420\"><b>Онлайн:<\/b> '.$ponbox['online'].'<\/font> ':'').(!empty($ponbox['offline']) ? '<font color=\"red\"><b>Оффлайн:</b> '.$ponbox['offline'].'<\/font> ':'').($komplekt?$komplekt:'').($listFiber?$listFiber:'').'</div>").addTo(map); 
';
}}}
return $marker; 	
}
function mapmyfta($locationid) {
global $config, $db, $lang, $PMonTables;
$SQLListMyfta = $db->Multi($PMonTables['myfta'],'*',['locationid'=>$locationid]);
$marker_myfta ='';
if(count($SQLListMyfta)){
foreach($SQLListMyfta as $myfta){
if(!empty($myfta['lan']) && !empty($myfta['lon'])){
$marker_myfta .='L.marker(['.$myfta['lan'].','.$myfta['lon'].'],{icon:myftamap}).bindPopup("Муфта: '.$myfta['name'].'").addTo(map);
';
}}}
return $marker_myfta;
}
function imgmdu($data) {
	if($data['status']==2){
		$img = 'mduoff'.$data['count'];
	}else{
		$img = 'mduon'.$data['count'];	
	}
	return $img;
}
function listfibermdu($ponboxid) {
	global $config, $db, $lang, $PMonTables;	
		$SQLListFiberin = $db->Multi($PMonTables['fiberlist'],'*',['getconnectid'=>$ponboxid]);
		$list = '';
		if(count($SQLListFiberin)){
			foreach($SQLListFiberin as $fiber1){
				if($fiber1['nextconnect']==3)
					$SQLbox1 = $db->Fast($PMonTables['unitponbox'],'id,name',['id'=>$fiber1['nextconnectid'],'treeid'=>$fiber1['treeid']]);
				if($fiber1['nextconnect']==2)
					$SQLbox1 = $db->Fast($PMonTables['myfta'],'*',['id'=>$fiber1['nextconnectid']]);
				$list .= "<span class='fiberlistmaps'><a href='/'><img class='ml' src='../style/img/map-cable.png'>".$lang[$fiber1['typesfiber']].", ".($fiber1['metr'] ? $fiber1['metr']."м, ":'')."".$SQLbox1['name']."</a></span>";
			}
		}		
		$SQLListFiberOut = $db->Multi($PMonTables['fiberlist'],'*',['nextconnectid'=>$ponboxid]);
		if(count($SQLListFiberOut)){
			foreach($SQLListFiberOut as $fiber2){
				if($fiber2['nextconnect']==2)
					$SQLbox2 = $db->Fast($PMonTables['myfta'],'*',['id'=>$fiber2['getconnectid']]);
				if($fiber2['nextconnect']==3)
					$SQLbox2 = $db->Fast($PMonTables['unitponbox'],'id,name',['id'=>$fiber2['getconnectid'],'treeid'=>$fiber2['treeid']]);
				$list .= "<span class='fiberlistmaps'><a href='/'><img class='ml' src='../style/img/map-cable.png'>".$lang[$fiber2['typesfiber']].", ".($fiber2['metr'] ? $fiber2['metr']."м, ":'')."".$SQLbox2['name']."</a></span>";
			}
		}
	return $list;
}
function komplektmdu($ponboxid) {
	global $config, $db, $lang, $PMonTables;	
	$komplet = '';
	$getData = $db->Multi($PMonTables['unitbasket'],'*',['ponboxid'=>$ponboxid]);
	if(count($getData)){
		foreach($getData as $spliter){
			$komplet .="<span class='fiberlistmap'><img class='ml' src='../style/img/map-split.png'>".$lang['spliter'.$spliter['spliter']]."</span>";
		}
	}
	return $komplet;
}
function getStatusPonbox($ponboxid) {
	global $config, $db, $lang, $PMonTables;	
	$arrayponbox = '';
	$getALLont = $db->Multi($PMonTables['ponboxonu'],'*',['ponboxid'=>$ponboxid]);
	if(count($getALLont)){
		$arrayponbox = array();
		foreach($getALLont as $id => $data){
			$arrayponbox[$ponboxid][($data['status']==2?'offline':'online')][$data['onuid']] = $data['onuid'];
		}
		if(!empty($arrayponbox[$ponboxid]['offline']) && is_array($arrayponbox) && count($getALLont)==count($arrayponbox[$ponboxid]['offline'])){
			$arrayponbox['statusmdu'] = 'criticmdu.png';
		}else{
			$arrayponbox['statusmdu'] = '';
		}
		if(!empty($arrayponbox[$ponboxid]['offline']))
			$getDataSql['offline'] = count($arrayponbox[$ponboxid]['offline']);
		if(!empty($arrayponbox[$ponboxid]['online']))
			$getDataSql['online'] = count($arrayponbox[$ponboxid]['online']);
		if(is_array($getDataSql)){
			$getDataSql['count'] = ($getALLont ? count($getALLont) : 0);
			if($getDataSql['count']>=1 AND $getDataSql['count']==$getDataSql['offline']){
				$getDataSql['status'] = 2;
			}else{
				$getDataSql['status'] = 1;	
			}
			$db->SQLupdate($PMonTables['unitponbox'],$getDataSql,['id'=>$ponboxid]);
		}
	}
	return $arrayponbox;
}
function viewListPonBox($ont,$ponboxid,$listOntPonbox) {
	global $config, $db, $lang, $PMonTables;
	$TplarraONT .= '<div class="block-ont"';
	if(!empty($listOntPonbox[$ponboxid][$ont['idonu']]['onuid'])){
		$TplarraONT .= 'style="border: 1px solid #4a7bb059;background:#bfd0de12;"';	
		if(!empty($ont['ponboxid']) && $ont['ponboxid']==$ponboxid)
			$TplarraONT .= 'onclick="ajaxponboxonu(\'delonu\','.$ponboxid.','.$ont['idonu'].')"';
	}else{
		if(!$ont['ponboxid'])
			$TplarraONT .= 'onclick="ajaxponboxonu(\'addonu\','.$ponboxid.','.$ont['idonu'].')"';			
	}			
	$TplarraONT .= '>';
	$TplarraONT .= '<div class="l">';
	if(!empty($ont['ponboxid']) && $ont['ponboxid']==$ponboxid){
		$TplarraONT .= '<img src="../style/img/pon/addonu.png">';
	}elseif(!empty($ont['ponboxid']) && $ont['ponboxid']!==$ponboxid){
		$TplarraONT .= '<img src="../style/img/pon/lockonu.png">';			
	}else{
		$TplarraONT .= '<img src="../style/img/pon/onu.png">';	
	}
	$TplarraONT .= '</div>		
		<div class="b-add">
		<div class="lis"><b>'.$ont['inface'].'</b>'.(!empty($ont['ponboxname']) && $ont['ponboxid']!=$ponboxid ? '<div class="mdu-curent">'.$ont['ponboxname'].'</div>':'');
		$TplarraONT .= '</div>';
		if($ont['status']==2){
		if(!empty($ont['sn']) || !empty($ont['mac']))
			$TplarraONT .= '<div class="lis"><span class="srnum">'.$ont['sn'].$ont['mac'].'</span></div>';
		}else{
			$TplarraONT .= '<div class="lis">'.$ont['rx'].' '.$ont['dist'].'</div>';	
		}
		$TplarraONT .= '</div></div>';
	return $TplarraONT;
}
function viewONUListPonBox($arrayONT,$ponboxid,$moder) {
	global $config, $db, $lang, $PMonTables;
	foreach($arrayONT[$ponboxid] as $id => $onu){
		if(!empty($onu['onuid'])){
			$dataONT = $db->Fast($PMonTables['onus'],'*',['idonu'=>$onu['onuid']]);
			if(!empty($dataONT['idonu'])){
					$style .='<div class="ont-ponbox-one"><div class="im">';
					$style .='<img class="img-onu" src="../style/img/pon/onu.png"></div><div class="nm">';
					$style .='<span>';						
					$style .='<div class="inface" '.($dataONT['status']==2?'style="color:red;"':'').'>';
					if($dataONT['status']==2){
						$style .='<div class="statusoffline"></div>';
					}else{
						$style .='<div class="statusonline"></div>';
					}
					$style .=''.$dataONT['type'].' '.$dataONT['inface'].'</div>';
					if(!empty($dataONT['mac']) || !empty($dataONT['sn']))
						$style .='<div class="sernum" '.($dataONT['status']==2?'style="color:red;"':'').'>'.$dataONT['mac'].$dataONT['sn'].'</div>';					
					$style .='<div class="subinfo">';
						$style .='<span class=distponbox>'.($dataONT['dist']?$dataONT['dist'].' м':'').'</span>';
						$style .='<span class=signalponbox>'.($dataONT['rx']?styleRxMap($dataONT['rx']):'').'</span>';
					$style .='</div>';				
					$style .='</span>';

	    $style .='</div>
				</div>';
			}
		}
	}
	return $style;
}
function loadListOnt($deviceid,$portid) {
	global $config, $db, $lang, $PMonTables;
	$arraONT = '';
	$dataPort = $db->Fast($PMonTables['switchport'],'*',['deviceid'=>$deviceid,'id'=>$portid]);
	$dataONT = $db->Multi($PMonTables['onus'],'*',['olt'=>$deviceid,'portolt'=>$dataPort['llid']]);
	if(count($dataONT)){
		$arraONT = array();
		foreach($dataONT as $id => $onu){
			$arraONT[$id]['idonu'] = $onu['idonu']; 
			$arraONT[$id]['status'] = $onu['status'];
			$arraONT[$id]['dist'] = $onu['dist'];
			$arraONT[$id]['rx'] = $onu['rx'];
			if(!empty($onu['sn']))			
				$arraONT[$id]['sn'] = $onu['sn']; 
			if(!empty($onu['mac']))	
				$arraONT[$id]['mac'] = $onu['mac']; 
			$arraONT[$id]['type'] = $onu['type']; 
			$arraONT[$id]['inface'] = $onu['type'].' '.$onu['inface']; 
			$dataPonBoxNon = $db->Fast($PMonTables['ponboxonu'],'id,ponboxid,added',['onuid'=>$onu['idonu']]);
			if(!empty($dataPonBoxNon['id'])){
				$arraONT[$id]['id'] = $dataPonBoxNon['id'];
				$arraONT[$id]['ponboxid'] = $dataPonBoxNon['ponboxid'];
				if(!empty($dataPonBoxNon['ponboxid'])){
					$dataPonBoxNan = $db->Fast($PMonTables['unitponbox'],'id,name',['id'=>$dataPonBoxNon['ponboxid']]);
				$arraONT[$id]['ponboxname'] = $dataPonBoxNan['name'];
				}
				$arraONT[$id]['ponboxadded'] = $dataPonBoxNon['added'];
			}
		}
	}	
	return $arraONT;
}
function ListOntPonbox($ponboxid) {
	global $config, $db, $lang, $PMonTables;
	$arrayponbox = '';
	$getALLont = $db->Multi($PMonTables['ponboxonu'],'*',['ponboxid'=>$ponboxid]);
	if(count($getALLont)){
		$arrayponbox = array();
		foreach($getALLont as $id => $data){
			$arrayponbox[$ponboxid][$data['onuid']]['onuid'] = $data['onuid'];
			$arrayponbox[$ponboxid][$data['onuid']]['status'] = $data['status'];
			if(!empty($data['added']))
				$arrayponbox[$ponboxid][$data['onuid']]['added'] = $data['added'];
			if(!empty($data['updates']))
				$arrayponbox[$ponboxid][$data['onuid']]['updates'] = $data['updates'];			
			if(!empty($data['ponboxid']))
				$arrayponbox[$ponboxid][$data['onuid']]['ponboxid'] = $data['ponboxid'];
		}
	}	
	return $arrayponbox;
}
function infdisplay($text) {
	return '<span class="inf_display"><i class="fi fi-rr-comment-info"></i>'.($text?$text:'empty_err').'</span>';
}
function pmonlog($data = array()) {
	global $db, $lang, $config, $PMonTables, $time; 
	switch($data['types']) {
		case 'users':		
			$SQLinsert['userid'] = $data['userid'];
			$SQLinsert['message'] = $data['message'];
			$SQLinsert['added'] = $time;
			$SQLinsert['progress'] = 'user';
		break;		
		case 'config':		
			$SQLinsert['userid'] = $data['userid'];
			$SQLinsert['message'] = $data['message'];
			$SQLinsert['added'] = $time;
			$SQLinsert['progress'] = 'config';
		break;		
		case 'switch':		

		break;	
	}
	if(is_array($SQLinsert))
		$db->SQLinsert($PMonTables['log'],$SQLinsert);
}
function aftertime($start) {
	global $lang;
	$onu_time = false;
	if(preg_match("/0000/i",$start)){
		$onu_time = false;	
	}else{
		$startTime = date_create($start);
		$endTime   = date_create();
		$diff = date_diff($endTime, $startTime);
		if($diff->format('%m'))	
			$onu_time.= $diff->format('%m').' міс ';
		if($diff->format('%d'))
			$onu_time.= $diff->format('%d').' дн ';
		if($diff->format('%h'))
			$onu_time.= $diff->format('%h').' год ';
		if($diff->format('%i'))
			$onu_time.= $diff->format('%i').' хв ';
		if($diff->format('%s') && !$diff->format('%i'))
			$onu_time.= $diff->format('%s').' сек ';

	}
	return $onu_time;
}
if (!function_exists("htmlspecialchars_uni")) {
	function htmlspecialchars_uni($message) {
		$message = preg_replace("#&(?!\#[0-9]+;)#si", "&amp;", $message); // Fix & but allow unicode
		$message = str_replace("<","&lt;",$message);
		$message = str_replace(">","&gt;",$message);
		$message = str_replace("\"","&quot;",$message);
		$message = str_replace("  ", "&nbsp;&nbsp;", $message);
		return $message;
	}
    function html_uni($str) {
        return htmlspecialchars_uni($str);
    }
}
function infstatus($status,$count=null){
	if($status=='yes' && $count){
		return '<div class="online_green speed1"></div>';
	}elseif($status=='yes' && !$count){
		return '<div class="online_green"></div>';		
	}elseif($status=='no'){
		return '<div class="online_red"></div>';		
	}else{
		return '<div class="online_red"></div>';		
	}
}
function getClassUser($class){
	global $lang;
	switch ($class) {
		case 7:		
			return $lang['class7'];	
		break;		
		case 6:		
			return $lang['class6'];	
		break;	
		case 5:				
			return $lang['class5'];	
		break;	
		case 4:		
			return $lang['class4'];	
		break;	
		case 3:				
			return $lang['class3'];	
		break;	
		case 2:				
			return $lang['class2'];	
		break;	
		case 1:	
			return $lang['class1'];	
		break;	
	}	
}
function devLocation($object){
	if(!empty($object['type'])){
		$tplStyle ='<div class="object">';
		switch($object['type']){
			case 'olt':
				$tplStyle .='<div class="img"><img src="../style/device/'.$object['img'].'"></div>';
				$tplStyle .='<a href="/?do=detail&act='.$object['type'].'&id='.$object['id'].'">'.$object['name'].'</a>';
				$tplStyle .='<h3>'.$object['model'].'</h3>';
			break;		
			case 'unit':
				$tplStyle .='<div class="img"><img src="../style/img/box.png"></div>';
				$tplStyle .='<a href="/">'.$object['name'].'</a>';
				$tplStyle .='<h3>'.$object['model'].'</h3>';
			break;
		}
		$tplStyle .='</div>';
	}
	return $tplStyle;
}
function getAllDeviceLocation($location){
	global $db,$PMonTables;
	$dataSwitch = $db->Multi($PMonTables['switch'],'*',['location'=>$location]);
	if(count($dataSwitch)){
		$arraSwitch = array();
		foreach($dataSwitch as $id => $value){
			$arraySwitch[$id]['id'] = $value['id']; 
			$arraySwitch[$id]['name'] = $value['place']; 
			$arraySwitch[$id]['type'] = $value['device'];
			$arraySwitch[$id]['img'] = $value['img'];
			$arraySwitch[$id]['model'] = $value['inf'].''.$value['model'];
		}
	}
	$dataUn = $db->Multi($PMonTables['unit'],'*',['location'=>$location]);
	if(count($dataUn)){
		$arrayUn = array();
		foreach($dataUn as $id => $value){
			$arrayUn[$id]['id'] = $value['id']; 
			$arrayUn[$id]['name'] = $value['name']; 
			$arrayUn[$id]['type'] = 'unit';
			$arrayUn[$id]['img'] = 'box.png';
			$arrayUn[$id]['model'] = 'Юніти';
		}
	}
	if(is_array($arraySwitch) && is_array($arrayUn)){
		$arrayData = array_merge($arraySwitch, $arrayUn);
	}elseif(is_array($arraySwitch) && !is_array($arrayUn)){
		$arrayData = $arraySwitch;
	}else{
		$arrayData = $arrayUn;
	}
	return $arrayData;
}
function checkAccess($class){
	global $USER;
	if(!$USER['class']){
		return false;
	}else
	if(!empty($USER['class']) && $USER['class']>=$class){
		return true;
	}else{ 
		return false;
	}
}
function tim_check($time){
	$timecheck = '1 hour';	
	if($time=='1h'){
		$timecheck = '1 година';
	}elseif($time=='30min'){
		$timecheck = '30 хвилин';
	}elseif($time=='2h'){
		$timecheck = '2 години';
	}elseif($time=='3h'){
		$timecheck = '3 години';
	}
	return $timecheck;
}
function priority($priority){
	switch($priority){
		case 1:
			return 'Початкова перевірка';
		break;		
		case 2:
			return 'Звичайна перевірка';
		break;		
		case 3:
			return 'Максимальна перевірка';
		break;
	}
}
function getListSFParray(){
	global $db;
	$dataArraySfp = $db->Multi('sfp');
	if(count($dataArraySfp)){
		$arraSfp = array();
		foreach($dataArraySfp as $id => $value){
			$arraSfp[$id]['id'] = $value['id'];
			$arraSfp[$id]['types'] = $value['types'];
			$arraSfp[$id]['wavelength'] = $value['wavelength'];
			$arraSfp[$id]['connector'] = $value['connector'];
			$arraSfp[$id]['dist'] = $value['dist'];
			$arraSfp[$id]['speed'] = $value['speed'];
		}
	}else{
		$arraSfp = null;
	}	
	return $arraSfp;	
}
function convertuptime($times) {

	return $times;
}
function secTodate($inputSeconds) {
    $secondsInAMinute = 60;
    $secondsInAnHour = 60 * $secondsInAMinute;
    $secondsInADay = 24 * $secondsInAnHour;
    $days = floor($inputSeconds / $secondsInADay);
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);
    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);
    $sections = ['дн' => (int)$days,'г' => (int)$hours,'хв' => (int)$minutes,'с' => (int)$seconds,];
    foreach ($sections as $name => $value){
        if ($value > 0){
            $timeParts[] = $value. ''.$name.($value == 1 ? '' : '');
        }
    }
    return implode(', ', $timeParts);
}
function tplreason($title,$css){
	return'<span class="css_'.$css.'">'.$title.'</span>';
}
function geterrorPonTpl($llid,$deviceid){
	global $db;
	$dataLastPortError = $db->Simple("SELECT * FROM `switch_port_err` WHERE `llid` = '".(int)$llid."' AND `deviceid` = '".(int)$deviceid."' ORDER BY `added` DESC LIMIT 1");
	if(!empty($dataLastPortError['inerror'])){
		$tpl .= '<div class="porterrblock">';
		$tpl .= '<div class="info"><h2>IfInErrors</h2><span>'.$dataLastPortError['added'].'</span></div>';				
		if($dataLastPortError['status_inerror']=='up'){
			$tpl .= '<div class="icoup"><span><i class="fi fi-rr-angle-small-up"></i></span></div>';
		}elseif($dataLastPortError['status_inerror']=='down'){
			$tpl .= '<div class="icoup"><span><i class="fi fi-rr-angle-small-up"></i></span></div>';
		}else{
			$tpl .= '<div class="iconone"><span><i class="fi fi-br-minus"></i></span></div>';	
		}
		$tpl .= '<div class="errcount">'.$dataLastPortError['inerror'].($dataLastPortError['newin'] && $dataLastPortError['newin']!==$dataLastPortError['inerror']?'<b>+'.$dataLastPortError['newin'].'</b>':'').'</div>';
		$tpl .= '</div>';
	}
	return $tpl;
}
function getlistPonTpl($data){
	global $db;
	$SQLport = $db->Multi('switch_pon','*',['oltid'=>$data['deviceid']],['sort'=>'asc']);
	if(count($SQLport)){
		foreach($SQLport as $Port){
			$css_load_bar = loadbarpon($Port['support'],$Port['count']);
			$tpl .= '<li '.($data['ponid']==$Port['id']?'class="active"':'').'>'.($data['ponid']==$Port['id'] && $Port['count']>10?'':'').'';//<span class="connect_bd"></span>
			$tpl .= '<div class="elem nam"><a href="/?do=terminal&id='.$data['deviceid'].'&port='.$Port['id'].'">'.$Port['pon'].'';
			$SQLgetPort = $db->Fast('switch_port','*',['deviceid'=>$Port['oltid'],'llid'=>$Port['sfpid']]);
			if(!empty($Port['support'])){
				$tpl .= '<span class="subinf">';
				if(!empty($Port['online']))
					$tpl .= '<span class="pon_online">'.$Port['online'].'</span>';
				if(!empty($Port['offline']))
					$tpl .= '<span class="pon_offline">'.$Port['offline'].'</span>';
				$tpl .= '<span class="pon_real">'.$Port['count'].'</span>';
				$tpl .= '<span class="pon_support">'.$Port['support'].'</span>';
				$tpl .= '</span>';
				if(!empty($SQLgetPort['descrport']))
					$tpl .= '<span class="element1">'.$SQLgetPort['descrport'].'</span>';
			}
			$tpl .= '</a><div class="loadli"><div class="bdload '.$css_load_bar['css'].'" style="width:'.$css_load_bar['width'].'%;"></div></div></div></li>';
		}
	}
	return $tpl;
}
function cl_inface($inface){
	$inface = str_replace('"','',$inface);
	$inface = trim($inface);
	preg_match('/(.*?):/',$inface,$inf);
	return str_replace(':','',$inf[0]);
}
function ont_label($name,$data){
	return '<div class="ont-label"><div class="name-label">'.$name.'</div><div class="data-label">'.$data.'</div></div>';
}
function loadbarpon($portonu,$portcountonu){
	$data = array();
	$width = (100/$portonu)*$portcountonu;
	$count = ceil($width);
	if($count>=1 AND $count<=49){
		$styleport='load0';	
	}elseif($count>=50 AND $count<=60){
		$styleport='load1';	
	}elseif($count>=61 AND $count<=70){
		$styleport='load2';	
	}elseif($count>=71 AND $count<=80){
		$styleport='load3';	
	}elseif($count>=81 AND $count<=90){
		$styleport='load4';	
	}elseif($count>=91 AND $count<=99){
		$styleport='load5';	
	}else{
		$styleport='full';
	}
	$data['css'] = $styleport;
	$data['width'] = $width;
	return $data;
}
function nameport_pon2($string) {
	$string = mb_strtolower($string);
	$result = preg_replace("/[^a-zа-я\s]/iu","",$string);
	#$result = preg_replace("/[^a-zа-я1-9\s]/iu","",$string);
	return str_replace(" ","",$result);
}
function nameport_pon($value) {
	$value = mb_strtolower($value);
	if(preg_match('/xgei/i',$value)){
		return 'sfp'; // xgei (интерфейс 10G Ethernet).
	}elseif(preg_match('/gei/i',$value)){
		return 'eth1000'; // gei (интерфейс 1000M Ethernet)	
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
function idport_switch($string) {
	preg_match('/0\/(\d+)/',$string,$match);
	return $match[1];
}
function getAllPortDevice($SQLPortSFP){
	$ListPort = array();
	foreach($SQLPortSFP as $PortSfp){
		$nameport = nameport_pon($PortSfp['nameport']);
		#$idport = idport_switch($PortSfp['nameport']);
		$idport = $PortSfp['llid'];
		$ListPort[$nameport][$idport]['name'] = $PortSfp['nameport'];
		$ListPort[$nameport][$idport]['llid'] = $PortSfp['llid'];
		$ListPort[$nameport][$idport]['deviceid'] = $PortSfp['deviceid'];
		$ListPort[$nameport][$idport]['descr'] = $PortSfp['descrport'];
		$ListPort[$nameport][$idport]['id'] = $PortSfp['id'];
		$ListPort[$nameport][$idport]['operstatus'] = $PortSfp['operstatus'];
		$ListPort[$nameport][$idport]['typeport'] = $PortSfp['typeport'];
		$ListPort[$nameport][$idport]['updates'] = $PortSfp['updates'];
		$ListPort[$nameport][$idport]['sms'] = $PortSfp['sms'];
		$ListPort[$nameport][$idport]['error'] = $PortSfp['error'];
		$ListPort[$nameport][$idport]['monitor'] = $PortSfp['monitor'];
		$ListPort[$nameport][$idport]['idport'] = $idport;
		$ListPort[$nameport][$idport]['id'] = $PortSfp['id'];
	}
	return $ListPort;
}
function goPost($url,$post_array = array(), $decode = false, $time = 10){
	if($url){
		$ch = curl_init($url);
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $post_array);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER,false); 
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,$time);
		$json = curl_exec($ch);
		curl_close($ch);
		if($decode){
			$res_api = json_decode($json,true);	
		}else{
			$res_api = $json;	
		}
		return $res_api;
	}
}
function tplErrorPort($deviceid,$llid){
	global $db;
	$style ='';
	$dataLastPortError = $db->Simple("SELECT * FROM `switch_port_err` WHERE `llid` = '".(int)$llid."' AND `deviceid` = '".(int)$deviceid."' ORDER BY `added` DESC LIMIT 1");	
	if(!empty($dataLastPortError['inerror']))
		$style .='<span class="block_error"><span class="title">IfInErrors:</span><span class="counterr">'.$dataLastPortError['inerror'].($dataLastPortError['newin'] && $dataLastPortError['newin']!==$dataLastPortError['inerror']?'<b>+'.$dataLastPortError['newin'].'</b>':'').'</span></span>';	
	if(!empty($dataLastPortError['outerror']))
		$style .='<span class="block_error"><span class="title">out:</span><span class="counterr">'.$dataLastPortError['outerror'].'</span></span>';
	if(!empty($dataLastPortError['id']))
		return $style;
}
function blockonline($count){
	global $lang;
	$style = '';
	if($count)
		$style = '<div class="listcheck"><div class="onu-online"><h2>'.$lang['blockonline'].'</h2><div class="onu-bar"><div class="onu-load" style="width:'.$count.'%;"></div></div></div></div>';
	return $style;		
}
function blockoffline($count){
	global $lang;
	$style = '';
	if($count)
		$style = '<div class="listcheck"><div class="onu-offline"><h2>'.$lang['blockoffline'].'</h2><div class="onu-bar"><div class="onu-load" style="width:'.$count.'%;"></div></div></div></div>';
	return $style;		
}
function checkSearch($data){
	switch($data){
		case 'big':
			return 'big';
		break;			
		case 'small':
			return 'small';
		break;		
		case 'all':
			return 'all';
		break;		
		case 'gpon':
			return 'gpon';
		break;		
		case 'epon':
			return 'epon';
		break;		
		case 'on':
			return 'on';
		break;		
		default:
			return false;
	}
}
function statusChecker($status){
	switch($status){
		case 'up':
			return '<span class="style_up">up</span>';
		break;		
		case 'down':
			return '<span class="style_down">down</span>';
		break;		
		case 'none':
			return '<span class="style_none">none</span>';
		break;
	}
}
function info($title,$descr){
	$style .= '<div class="information">';
	$style .= '<h2>'.$title.'</h2>';
	$style .= '<b>'.$descr.'</b>';
	$style .= '</div>';
	return $style;
}
function getPortConnectSFP($deviceid){
	global $db, $lang;
	$SQLSfp = $db->Multi('connect_port','*',['curd'=>$deviceid]);
	if(count($SQLSfp)){
		$result ='';
		$array = array();
		foreach($SQLSfp as $sfp){
			$array[$sfp['id']]['id'] = $sfp['id'];
			$array[$sfp['id']]['types'] = $sfp['types'];
			$array[$sfp['id']]['current']['device_id'] =  $deviceid;
			$array[$sfp['id']]['current']['device_id'] =  $sfp['curp'];
			$array[$sfp['id']]['connect']['device_id'] =  $sfp['connd'];
			$array[$sfp['id']]['connect']['port_id'] =  $sfp['connp'];
			// CONNECT DEVICE: place, port
			$Devcurrent = $db->Simple('SELECT switch.place, switch.id, switch_port.nameport FROM switch, switch_port WHERE switch.id = switch_port.deviceid AND switch_port.id = '.$sfp['curp']);
			if(!empty($Devcurrent['id'])){
				$array[$sfp['id']]['current']['place'] = $Devcurrent['place'];
				$array[$sfp['id']]['current']['port'] = $Devcurrent['nameport'];
			}
			// SFP CURRENT
			$SFPcurrent = $db->Fast('sfp','*',['id'=>$sfp['cursfp']]);
			if(!empty($SFPcurrent['id'])){
				$array[$sfp['id']]['current']['sfp']['id'] = $SFPcurrent['id']; 
				$array[$sfp['id']]['current']['sfp']['wav'] = $SFPcurrent['wavelength']; 
				$array[$sfp['id']]['current']['sfp']['dist'] = $SFPcurrent['dist']; 
			}
			// CONNECT DEVICE: place, port
			$Devconnect = $db->Simple('SELECT switch.place, switch.id, switch_port.nameport FROM switch, switch_port WHERE switch.id = switch_port.deviceid AND switch_port.id = '.$sfp['connp']);
			if(!empty($Devconnect['id'])){
				$array[$sfp['id']]['connect']['place'] = $Devconnect['place'];
				$array[$sfp['id']]['connect']['port'] = $Devconnect['nameport'];
			}
			// SFP CONNECT
			$SFPconnect = $db->Fast('sfp','*',['id'=>$sfp['connsfp']]);
			if(!empty($SFPconnect['id'])){
				$array[$sfp['id']]['connect']['sfp']['id'] = $SFPconnect['id']; 
				$array[$sfp['id']]['connect']['sfp']['wav'] = $SFPconnect['wavelength']; 
				$array[$sfp['id']]['connect']['sfp']['dist'] = $SFPconnect['dist']; 
			}
		}
	}
	if(is_array($array)){
		foreach($array as $community){
			$result .='<div class="connect-port">';
			$result .='<div class="connect-cur"><h2>'.$community['current']['place'].'</h2><span>'.$community['current']['port'].'</span></div>';	
			if(is_array($community['current']['sfp'])){
				$result .='<div class="connect-cur-sfp"><span class="css_sfp"><span class="cursfpkm">'.$community['current']['sfp']['dist'].'km<span></span></span><span class="sfp'.$community['current']['sfp']['wav'].'">'.$community['current']['sfp']['wav'].'</span></span></div>';	
			}
			$result .='<div class="connect-ico"><span><img onclick="ajaxconnect(\'edit\','.$community['id'].');" src="/style/img/'.($community['types']?$community['types']:'sc').'.png"></span></div>';	
			if(is_array($community['connect']['sfp'])){
				$result .='<div class="connect-conn-sfp"><span class="css_sfp"><span class="sfp'.$community['connect']['sfp']['wav'].'">'.$community['connect']['sfp']['wav'].'</span><span class="commsfpkm">'.$community['connect']['sfp']['dist'].'km<span></span></span></span></div>';	
			}
			$result .='<div class="connect-conn"><h2>'.$community['connect']['place'].'</h2><span>'.$community['connect']['port'].'</span></div>';			
			$result .='<a class="connect-url" href="/?do=detail&act=olt&id='.$community['connect']['device_id'].'"><img src="/style/img/hub.png"></a>';	
			$result .='</div>';	
		}
	}else{
			$result .='<div class="empty_connect"><i class="fi fi-rr-comment-info"></i>'.$lang['empty'].'</div>';	
	}
	return $result;
}
function getPortConnect($data,$id){
	global $db;
	$SQLSwitch = $db->Fast('switch','place,netip,id',['id'=>$data[$id]['connectdevice']]);
	if(!empty($SQLSwitch['id'])){
		$style .='<div class="connectswitch">';
		$style .='<img class="sw-icon" src="../style/img/servers.png">';
		$style .='<div class="namedev"><a class="connectnext" href="/?do=detail&act=olt&id='.$data[$id]['connectdevice'].'&page=connect">';
			$style .='<div class="dev">'.$SQLSwitch['place'].'</div>';
			$style .='<div class="port">'.$data[$id]['nameport'].'</div>';
		$style .='</a></div>';
		$style .='</div>';
	}
	return $style;
}
function getConnection($id){
	global $db;
	$array_conn = null;
	$SQLconnect = $db->Multi('connect_port','*',['curd'=>$id]);
	if(count($SQLconnect)){
		$array_conn = array();
		foreach($SQLconnect as $connect){
			$SQLconnectDevice = $db->Fast('switch_port','*',['id'=>$connect['connp']]);
			if(!empty($SQLconnectDevice['id'])){
				$array_conn[$connect['curp']]['id'] = $SQLconnectDevice['id'];
				$array_conn[$connect['curp']]['nameport'] = $SQLconnectDevice['nameport'];
				$array_conn[$connect['curp']]['descrport'] = $SQLconnectDevice['descrport'];
				$array_conn[$connect['curp']]['operstatus'] = $SQLconnectDevice['operstatus'];
				$array_conn[$connect['curp']]['connectdevice'] = $connect['connd'];
				$array_conn[$connect['curp']]['connectport'] = $connect['connp'];
				$array_conn[$connect['curp']]['added'] = $connect['added'];
			}
		}
	}
	return $array_conn;					
}
function saveBaseip($ip,$id){
	global $db, $time;
	$db->SQLinsert('baseip',['ip'=>$ip,'deviceid'=>$id,'added'=>$time]);
}
function getListLocation(){
	global $db;
	$SQLgetListLocation = $db->Multi('location');
	if(count($SQLgetListLocation)){
		$location = array();
		foreach($SQLgetListLocation as $loc){
			$location[$loc['id']]['name'] = $loc['name'];
			if(!empty($loc['lan']))
				$location[$loc['id']]['geo']['lan'] = $loc['lan'];
			if(!empty($loc['lon']))
				$location[$loc['id']]['geo']['lon'] = $loc['lon'];
		}
		return $location;
	}else{
		return false;
	}
}
function getListDevice($type = false){
	global $db;
	if($type) $Where = ['device'=>$type,'work'=>'yes'];
	$SQLgetList = $db->Multi('equipment','*',$Where);
	if(count($SQLgetList)){
		return $SQLgetList;
	}else{
		return false;
	}
}
function getListGroup(){
	global $db, $PMonTables;
	$list = $db->Multi($PMonTables['gr']);
	if(count($list)){
		return $list;
	}else{
		return false;
	}
}
function getListLocations(){
	global $db, $PMonTables;
	$list = $db->Multi($PMonTables['location']);
	if(count($list)){
		return $list;
	}else{
		return false;
	}
}
function form($data = array()){
	$style = '<div class="pole1">';
	$style .= '<div class="form1">'.$data['name'].'';
	if(!empty($data['descr']))
		$style .= '<b>'.$data['descr'].'</b>';
	$style .= '</div>';
	$style .= '<div class="form2">'.$data['pole'].'</div>';
	$style .= '</div>';	
	return $style;
}
function formpage($data = array()){
	$style = '<div class="pole1">';
	if(!empty($data['img']))
		$style .= '<div class="img"><img src="../style/img/'.$data['img'].'"></div>';
	$style .= '<div class="form1">'.$data['name'].'';
	if(!empty($data['descr']))
		$style .= '<b>'.$data['descr'].'</b>';
	$style .= '</div>';
	$style .= '<div class="form2">'.$data['pole'].'</div>';
	$style .= '</div>';	
	return $style;
}
function okno_title($title){
	echo'<div class="overlay"><div id="okno"><div id="oknoheader" class="title"><img class="logo-pop" src="../style/pop.png">'.$title.'<span class="close" onclick="oknoclose()"><img style="height: 13px;" src="/style/img/multiply.png"></span></div><div class="result">';
}
function okno_end(){
	echo'</div></div></div><script>dragElement(document.getElementById("okno"));</script>';
}
function getPonPortOLt($olt,$llid){
	global $db;
	$SQLDetailPort = $db->Fast('switch_pon','*',['oltid'=>$olt,'sfpid'=>$llid]);
	return $SQLDetailPort;
}
function switchLog($deviceid,$types,$message){
	global $db, $PMonTables, $lang;
		$dataInsert['message'] = $message;
		$dataInsert['deviceid'] = $deviceid;
		$dataInsert['types'] = $types;
		$dataInsert['added'] = date('Y-m-d H:i:s');
		if($deviceid && $message && $types){
			$db->SQLinsert($PMonTables['swlog'],$dataInsert);		
		}
}
function idblock($name){
	$str = mb_strtolower(trim($name));
	switch($str){
		case 'fastethernet':
			$id = 1;
		break;		
		case 'epon':
			$id = 2;
		break;		
		case 'gpon':
			$id = 3;
		break;		
		case 'tgigaethernet':
			$id = 4;
		break;		
		case 'gigaethernet':
			$id = 5;
		break;		
		case 'xge':
			$id = 6;
		break;		
		case 'ge':
			$id = 7;
		break;
	}
	return $id;
}
function statsPonPort($data,$support){
	if(!empty($data['support'])){
		$style ='<div class="curent-onu">';
		if(!empty($data['online']))
			$style .='<div><span class="counton">'.$data['online'].'</span><span class="countname">онлайн</span></div>';
		if(!empty($data['offline']))	
			$style .='<div><span class="countoff">'.$data['offline'].'</span><span class="countname">оффлайн</span></div>';
		if(!empty($data['count']))	
			$style .='<div><span class="count">'.$data['count'].'</span><span class="countname">підкл.</span></div>';
		$style .='<div><span class="support">'.$data['support'].'</span><span class="supportname">'.$support.'</span><div>';
		$style .='</div>';
	}
	return $style;
}
function statusInfo($status){
	return '<span class="infos"><img src="../style/img/settings.png">'.$status.'</span>';
}
function grpahPon($count,$support){
	$width = (100/$support)*$count;
	if($width>=1 AND $width<=49){
		$color='pon1';	
	}elseif($width>=50 AND $width<=60){
		$color='pon2';	
	}elseif($width>=61 AND $width<=70){
		$color='pon3';	
	}elseif($width>=71 AND $width<=80){
		$color='pon4';	
	}elseif($width>=81 AND $width<=90){
		$color='pon5';	
	}elseif($width>=91 AND $width<=99){
		$color='pon6';	
	}else{
		$color='pon6';
	}
	$html ='<div class="percent_box '.$color.'">';
	$html .='<span class="percent"><span class="count">'.$count.'</span><span class="support">'.$support.'</span></span>';
	$html .='<div class="scale_box"><span class="scale" style="width:'.ceil($width).'%;"></span></div>';
	$html .='</div>';
	return $html;
}
function pager($rpp, $count, $href, $opts = array()) {
	$pages = ceil($count / $rpp);
	$pagedefault = 0;
	if (!empty($opts['lastpagedefault']))
		$pagedefault = floor(($count - 1) / $rpp);
		if ($pagedefault < 0)
			$pagedefault = 0;
	else {
		$pagedefault = 0;
	}
	if (isset($_GET["page"])) {
		$page = (int)$_GET["page"];
		if ($page < 0)
			$page = $pagedefault;
	}
	else
		$page = $pagedefault;	   

	$mp = $pages - 1;
	$as = '<i class="fi fi-rr-angle-left"></i>';
	if ($page >= 1) {
		$pager .= "<td style=\"border:none\">";
		$pager .= "</td>";
	}
	$as = '<i class="fi fi-rr-angle-right"></i>';
	if ($page < $mp && $mp >= 0) {
		$pager2 .= "<td style=\"border:none\">";
		$pager2 .= "<a class=\"navi\" href=\"{$href}&page=" . ($page + 1) . "\" style=\"text-decoration: none;\">$as</a>";
		$pager2 .= "</td>$bregs";
	}else	 $pager2 .= $bregs;

	if ($count) {
		$pagerarr = array();
		$dotted = 0;
		$dotspace = 3;
		$dotend = $pages - $dotspace;
		$curdotend = $page - $dotspace;
		$curdotstart = $page + $dotspace;
		for ($i = 0; $i < $pages; $i++) {
			if (($i >= $dotspace && $i <= $curdotend) || ($i >= $curdotstart && $i < $dotend)) {
				if (!$dotted)
				   $pagerarr[] = "<td style=\"border:none\" ><span class=\"clear\">...</span></td>";
				$dotted = 1;
				continue;
			}
			$dotted = 0;
			$start = $i * $rpp + 1;
			$end = $start + $rpp - 1;
			if ($end > $count)
				$end = $count;

			 $text = $i+1;
			if ($i != $page){
				if(!$i){
					$new_url=$href;
				}else{
					$new_url=$href.'&page='.$i;
				}
				$pagerarr[] = "<td style=\"border:none\"><a class=\"navi\" title=\"$start&nbsp;-&nbsp;$end\" href=\"{$new_url}\" style=\"text-decoration: none;\">$text</a></td>";
			}else{
				$pagerarr[] = "<td style=\"border:none\"><span>$text</span></td>";
			}
		}
		$pagerstr = join("", $pagerarr);
		$pagertop = "<table class=\"navs navigation\"><tr>$pager $pagerstr $pager2</tr></table>\n";
	
	}else {
		$pagertop = $pager;
		$pagerbottom = $pagertop;
	}
	$start = $page * $rpp;
	return array($pagertop, $pagerbottom, $start, $rpp);
}
function statusTermianl($status){
	if($status==1){
		$data['css'] = 'up';
		$data['img'] = '<img src="../style/img/online.png">';
	}else{
		$data['css'] = 'down';
		$data['img'] = '<img src="../style/img/offline.png">';
	}
	return $data;
}
function signalTerminal($signal, $type=true){
	$db = null;
	if($signal=='Offline')
		return ' ';
	if($signal=='0')
		return ' ';
	if($signal=='-70')
		return ' ';
	if($type==true)
		$db = ' dBm';
	$signala = str_replace('-', '',$signal);
	$signala = (int)$signala;
	if($signala>=1 AND $signala<=12 ){		
		return '<span class="signal1">'.$signal.$db.'</span>';	
	}elseif($signala>=13 AND $signala<=19 ){		
		return '<span class="signal2">'.$signal.$db.'</span>';	
	}elseif($signala>=20 AND $signala<=25 ){		
		return '<span class="signal3">'.$signal.$db.'</span>';	
	}elseif($signala>=26 AND $signala<=39 ){		
		return '<span class="signal4">'.$signal.$db.'</span>';	
	}else{		
		if($signala){
			$signal_ = sprintf("%.2f",$signal);
			return '<span class="signal0">'.$signal_.$db.' </span>';
		}else{
			return'---';
		}
	}
}
function deletOnu($data){
	global $db, $config, $lang;
	$db->SQLdelete('onus',['olt' => $data['olt'],'idonu' => $data['idonu']]);	
}

function SQLclear($result) {
	$result = str_replace("'","",$result);
	$result = str_replace('"',"",$result);
	$result = str_replace('>',"",$result);
	$result = str_replace('<',"",$result);
	return $result;
}
function totranslit($var, $lower = true, $punkt = true) {
	global $langtranslit;	
	if ( is_array($var) ) return "";
	$var = str_replace(chr(0), '', $var);
	if (!is_array ( $langtranslit ) OR !count( $langtranslit ) ) {
		$var = trim( strip_tags( $var ) );
		if ( $punkt ) $var = preg_replace( "/[^a-z0-9\_\-.]+/mi", "", $var );
		else $var = preg_replace( "/[^a-z0-9\_\-]+/mi", "", $var );
		$var = preg_replace( '#[.]+#i', '.', $var );
		$var = str_ireplace( ".php", ".ppp", $var );
		if ( $lower ) $var = strtolower( $var );
		return $var;
	}	
	$var = trim( strip_tags( $var ) );
	$var = preg_replace( "/\s+/ms", "-", $var );
	$var = str_replace( "/", "-", $var );
	$var = strtr($var, $langtranslit);	
	if ( $punkt ) $var = preg_replace( "/[^a-z0-9\_\-.]+/mi", "", $var );
	else $var = preg_replace( "/[^a-z0-9\_\-]+/mi", "", $var );
	$var = preg_replace( '#[\-]+#i', '-', $var );
	$var = preg_replace( '#[.]+#i', '.', $var );
	if ( $lower ) $var = strtolower( $var );
	$var = str_ireplace( ".php", "", $var );
	$var = str_ireplace( ".php", ".ppp", $var );	
	if( strlen( $var ) > 200 ) {		
		$var = substr( $var, 0, 200 );		
		if( ($temp_max = strrpos( $var, '-' )) ) $var = substr( $var, 0, $temp_max );	
	}	
	return $var;
}
