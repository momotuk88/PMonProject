<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
if($id==1){
	$getType = 'olt';
}elseif($id==2){
	$getType = 'switch';	
}elseif($id==3){
	$getType = 'switchl2';	
}elseif($id==4){
	$getType = 'ups';	
}elseif($id==11){
	$getTypeForm = 'switch';	
	$getsql = 'other';
}elseif($id==12){
	$getTypeForm = 'battery';	
	$getsql = 'other';
}elseif($id==13){
	$getTypeForm = 'ups';	
	$getsql = 'other';	
}elseif($id==14){
	$getTypeForm = 'sfp';	
	$getsql = 'sfp';	
}else{
	$getType = null;
}
/// ADD SFP
if($getTypeForm=='sfp'){
okno_title($lang['sfp']);
echo'<form action="/?do=send" method="post" id="formadd">';
echo'<input name="act" type="hidden" value="savesfp">';
$pole_name = '<input required name="model" class="input1" id="name" placeholder="Наприклад: Alistar SFP OLT Lte3680P BC 2DM Class C++" type="text" value="">';
echo form(['name'=>$lang['name'],'descr'=>$lang['sfpname'],'pole'=>$pole_name]);
$pole1 ='<select class="select" name="types" id="types"><option value="sm">Singlemode</option><option value="mm">Multimode </option></select>';
echo form(['name'=>$lang['typevolokno'],'descr'=>$lang['typevoloknodescr'],'pole'=>$pole1]);
$pole2 ='<select class="select" name="connector" id="connector"><option value="lc">LC</option><option value="sc">SC</option></select>';
echo form(['name'=>$lang['typeconnector'],'descr'=>'','pole'=>$pole2]);
$pole3 ='<select class="select" name="wavelength" id="wavelength"><option value="1310">1310</option><option value="1550">1550</option></select>';
echo form(['name'=>$lang['signalwav'],'descr'=>'','pole'=>$pole3]);
$pole4 ='<select class="select" name="dist" id="dist"><option value="3">3 км</option><option value="10">10 км</option><option value="20">20 км</option><option value="30">30 км</option><option value="40">40 км</option><option value="60">60 км</option><option value="80">80 км</option></select>';
echo form(['name'=>$lang['maxdist'],'descr'=>'','pole'=>$pole4]);
$pole5 ='<select class="select" name="speed" id="speed"><option value="1">1G</option><option value="10">10G</option><option value="20">20G</option><option value="40">40G</option><option value="60">60G</option><option value="80">80G</option></select>';
echo form(['name'=>$lang['sfpspeed'],'descr'=>'','pole'=>$pole5]);
echo'</form>';
echo'<div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['add'].'</button></div>';
okno_end();
die;	
}
/// OTHER ADD
$nomac = false;
okno_title($lang['addnewdevice']);
echo'<form action="/?do=send" method="post" id="formadd">';
if($getType){
	echo'<input name="act" type="hidden" value="savedevice">';
	$listDevice = getListDevice($getType);
	if(count($listDevice)){
		$pole_device = '<select class="select" name="deviceid" id="device">';
		foreach($listDevice as $Device)
			$pole_device .= '<option value="'.$Device['id'].'">'.$Device['name'].' '.$Device['model'].'</option>';
		$pole_device .='</select>';
	}else{
		$pole_device = '';
	}
}else{
	$pole_device = '<input name="devicemodel" class="input1" id="devicemodel" type="text" value="">';	
}
echo form(['name'=>$lang['model'],'descr'=>$lang['selectdevice'],'pole'=>$pole_device]);
$group = getListGroup();
if(is_array($group)){
	$listgroup = '<select class="select" name="group" id="group">';
	foreach($group as $gr){
		$listgroup .= '<option value="'.$gr['id'].'">'.$gr['name'].'</option>';
	}
	$listgroup .='</select>';
	echo form(['name'=>$lang['group'],'descr'=>'','pole'=>$listgroup]);
}
if($getType){
	$pole_name = '<input required name="name" class="input1" id="name" placeholder="Nazva ID" type="text" value="">';
	echo form(['name'=>$lang['inputnamedevice'],'descr'=>'','pole'=>$pole_name]);
	$pole_ip = '<input required name="ip" class="input1" id="ip" type="text" value="">';
	echo form(['name'=>$lang['ip'],'descr'=>$lang['ipdescr'],'pole'=>$pole_ip]);
}
if(isset($getTypeForm)){
	switch($getTypeForm){
		case 'switch':
			echo'<input name="act" type="hidden" value="saveswitch">';
			$pole_port = '<input required id="w80px" name="port" class="input1" placeholder="8" type="text" value="">';
			echo form(['name'=>$lang['port'],'descr'=>$lang['countport'],'pole'=>$pole_port]);
			$nomac = true;
		break;
		case 'battery':
			echo'<input name="act" type="hidden" value="savebattery">';
			$pole_amper = '<inputrequired  id="w80px" name="amper" class="input1" placeholder="72" type="text" value="">';
			echo form(['name'=>'Amer','descr'=>'','pole'=>$pole_amper]);		
			$pole_volt = '<inputrequired  id="w80px" name="volt" class="input1" placeholder="14" type="text" value="">';
			echo form(['name'=>'Volt','descr'=>'','pole'=>$pole_volt]);		
			$listBattery = $db->Multi('battery_list');
			if(count($listBattery)){
				$pole_battery = '<select class="select" name="types" id="types">';
				foreach($listBattery as $battery)
					$pole_battery .= '<option value="'.$battery['id'].'">'.$battery['descr'].' ('.$battery['types'].')</option>';
				$pole_battery .='</select>';
			}else{
				$pole_battery = '';
			}
			echo form(['name'=>$lang['typeakb'],'descr'=>'','pole'=>$pole_battery]);	
			$nomac = true;
		break;
		case 'ups':
			echo'<input name="act" type="hidden" value="saveups">';
			$pole_power = '<inputrequired  id="w80px" name="power" class="input1" placeholder="800" type="text" value="">';
			echo form(['name'=>$lang['powerakb'],'descr'=>$lang['powerakbw'],'pole'=>$pole_power]);			
			$nomac = true;
		break;
		default:break;
	}
}
if(!$nomac){
	$pole_mac = '<input name="mac" class="input1" id="ip" type="text" value="">';
	echo form(['name'=>$lang['mac'],'descr'=>$lang['ipdescr'],'pole'=>$pole_mac]);
}
$pole_sn = '<input name="sn" class="input1" id="sn" type="text" value="">';
echo form(['name'=>$lang['sn'],'descr'=>$lang['supporttmc'],'pole'=>$pole_sn]);
if($getType){
	echo'<div class="subpole">'.$lang['snmp'].'</div>';
	$pole_community = '<input required name="community" class="input1" id="ip" placeholder="public" type="text" value="">';
	echo form(['name'=>'Community','pole'=>$pole_community]);
}
echo'</form>';
echo'<div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['add'].'</button></div>';
okno_end();
