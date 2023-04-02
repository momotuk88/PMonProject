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
snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
@$snmpeth1 = snmp2_get($getOLT['netip'],$getOLT['snmpro'],"1.3.6.1.4.1.3902.1012.3.50.14.1.1.7.".$getONT['zte_idport'].'.'.$getONT['keyonu'].".1");
@$snmpeth2 = snmp2_get($getOLT['netip'],$getOLT['snmpro'],"1.3.6.1.4.1.3902.1012.3.50.14.1.1.7.".$getONT['zte_idport'].'.'.$getONT['keyonu'].".2");
@$snmpeth3 = snmp2_get($getOLT['netip'],$getOLT['snmpro'],"1.3.6.1.4.1.3902.1012.3.50.14.1.1.7.".$getONT['zte_idport'].'.'.$getONT['keyonu'].".3");
@$snmpeth4 = snmp2_get($getOLT['netip'],$getOLT['snmpro'],"1.3.6.1.4.1.3902.1012.3.50.14.1.1.7.".$getONT['zte_idport'].'.'.$getONT['keyonu'].".4");
@$snmptv = snmp2_get($getOLT['netip'],$getOLT['snmpro'],"1.3.6.1.4.1.3902.1012.3.50.19.1.1.1.".$getONT['zte_idport'].'.'.$getONT['keyonu'].'.1');
echo'<div class="zte_onu"><div class="zte_eth">';
// ETH1
if($snmpeth1){
	$eth1 = typeOnuztePort($snmpeth1);
	if(is_array($eth1)){
		echo'<div class="link link1"><div class="linkname">Eth1</div><img src="../style/img/'.$eth1['img'].'"><div class="linkstatus'.$eth1['status'].'"></div></div>';	
	}
}
// ETH2
if($snmpeth2){
	$eth2 = typeOnuztePort($snmpeth2);
	if(is_array($eth2)){
		echo'<div class="link link2"><div class="linkname">Eth2</div><img src="../style/img/'.$eth2['img'].'"><div class="linkstatus'.$eth2['status'].'"></div></div>';	
	}
}
// ETH3
if($snmpeth3){
	$eth3 = typeOnuztePort($snmpeth3);
	if(is_array($eth3)){
		echo'<div class="link link3"><div class="linkname">Eth3</div><img src="../style/img/'.$eth3['img'].'"><div class="linkstatus'.$eth3['status'].'"></div></div>';	
	}
}
// ETH4
if($snmpeth4){
	$eth4 = typeOnuztePort($snmpeth4);
	if(is_array($eth4)){
		echo'<div class="link link4"><div class="linkname">Eth4</div><img src="../style/img/'.$eth4['img'].'"><div class="linkstatus'.$eth4['status'].'"></div></div>';	
	}
}
// TV port
if($snmptv){
	$tv = typeOnuzteVideoPort($snmptv);
	if(is_array($tv)){	
		echo'<div class="linktvbord"></div><div class="linktv"><div class="linkname">TV</div><img src="../style/img/ztetv.png"><div class="linkstatus'.$tv['st'].'"></div></div>';
	}
}
echo'</div>';
echo'<div class="zte_status"><div class="zte_getstatus"><span>Port type:</span><span class="typortzte"><div class="eth_auto"></div><div class="eth_name">Auto</div><div class="eth_10"></div><div class="eth_name">10Mbps</div><div class="eth_100"></div><div class="eth_name">100Mbps</div><div class="eth_1000"></div><div class="eth_name">1G</div></span></div><div class="zte_gettype"><span>Port status:</span><span class="typeportstatus"><div class="eth_online"></div><div class="eth_name">Online</div><div class="eth_offline"></div><div class="eth_name">Offline</div><div class="eth_disable"></div><div class="eth_name">Disable</div></span></div></div></div>';

$control = '';
// eth1
if(is_array($eth1)){
	if($eth1['st']=='enable')
		$control .='<div class="control_eth"><b>Eth1</b><div class="ctrl disable" onclick="ajaxzteonuport(\'off\','.$id.',\'1\')">disable</div></div>';
	if($eth1['st']=='disable')
		$control .='<div class="control_eth"><b>Eth1</b><div class="ctrl enable" onclick="ajaxzteonuport(\'on\','.$id.',\'1\')">enable</div></div>';
}
// eth2
if(isset($eth2) and is_array($eth2)){
	if($eth2['st']=='enable')
		$control .='<div class="control_eth"><b>Eth2</b><div class="ctrl disable" onclick="ajaxzteonuport(\'off\','.$id.',\'2\')">disable</div></div>';
	if($eth2['st']=='disable')
		$control .='<div class="control_eth"><b>Eth2</b><div class="ctrl enable" onclick="ajaxzteonuport(\'on\','.$id.',\'2\')">enable</div></div>';
}
// eth3
if(isset($eth3) and is_array($eth3)){
	if($eth3['st']=='enable')
		$control .='<div class="control_eth"><b>Eth3</b><div class="ctrl disable" onclick="ajaxzteonu(\'port\','.$id.',\'3\',\'2\',\'popup\')">disable</div></div>';
	if($eth3['st']=='disable')
		$control .='<div class="control_eth"><b>Eth3</b><div class="ctrl enable" onclick="ajaxzteonu(\'port\','.$id.',\'3\',\'1\',\'popup\')">enable</div></div>';
}// eth4
if(isset($eth4) and is_array($eth4)){
	if($eth4['st']=='enable')
		$control .='<div class="control_eth"><b>Eth4</b><div class="ctrl disable" onclick="ajaxzteonu(\'port\','.$id.',\'4\',\'2\',\'popup\')">disable</div></div>';
	if($eth4['st']=='disable')
		$control .='<div class="control_eth"><b>Eth4</b><div class="ctrl enable" onclick="ajaxzteonu(\'port\','.$id.',\'4\',\'1\',\'popup\')">enable</div></div>';
}
// tv	
if(isset($tv) and is_array($tv)){
	if($tv['st']=='up'){
		$control .='<div class="control_eth"><b>TV</b><div class="ctrl disable" onclick="ztetvport(\'ontvzteport1\',\''.$id.'\');">disable</div></div>';
	}else{
		$control .='<div class="control_eth"><b>TV</b><div class="ctrl enable" onclick="ztetvport(\'offtvzteport1\',\''.$id.'\');">enable</div></div>';
	}
}
if($control)
	echo'<div class="onuztecontrol">'.$control.'</div>';
}
}
}

