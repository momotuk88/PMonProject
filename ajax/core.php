<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$act = isset($_POST['act']) ? Clean::str($_POST['act']): null;
if($act=='mapunit'){
	$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;	
	if($id){
	$getLoca = $db->Fast($PMonTables['unit'],'*',['id'=>$id]);
	if(!empty($getLoca['id'])){
	okno_title($lang['geounit']);
	echo'<link rel="stylesheet" href="../style/map/leaflet.css" />';
	echo'<script src="../style/map/leaflet.js"></script><script src="../style/map/mymarker.js"></script><form action="/?do=send" method="post" id="formadd"><input name="act" type="hidden" value="savegeounit"><input name="id" type="hidden" value="'.$id.'">';
	echo'<div id="map" style="height: 500px;"></div>';
	$geo_lan = ($getLoca['lan']?$getLoca['lan']:$config['geo_lan']);
	$geo_lon = ($getLoca['lon']?$getLoca['lon']:$config['geo_lon']);
	if(!empty($getLoca['lon']) && !empty($getLoca['lan']))
		$marker = 'L.marker(['.$getLoca['lan'].','.$getLoca['lon'].'],{icon:maplocation}).addTo(map);';
$script = <<<HTML
<script>
var lat = '{$geo_lan}'; 
var lon = '{$geo_lon}';
var map = L.map('map');
map.setView([lat, lon], 18);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
{$marker}
var popup = L.popup();	
function onMapClick(e) {
var lat = e.latlng.lat.toFixed(6);
var lon = e.latlng.lng.toFixed(6);
popup
.setLatLng(e.latlng)
.setContent('<b>{$lang['geounit']}</b> <br>' +
'<input name="lan" type="hidden" value="' + lat + '"><input name="lon" type="hidden" value="' + lon + '"><span class="koomap"><b>{$lang['geo']}</b>: ' + lat + ' ' + lon + '</span><br>' +
'<button type="submit" class="cssadd">{$lang['save']}</button>')
.openOn(map);
}
map.on('click', onMapClick);</script>
HTML;
	echo $script.'</form>';
	okno_end();	
	}
	}	
}elseif($act=='mapmyfta'){
	$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;	
	if($id){
	$getLoca = $db->Fast($PMonTables['myfta'],'*',['id'=>$id]);
	if(!empty($getLoca['id'])){
	okno_title($lang['geounit']);
	echo'<link rel="stylesheet" href="../style/map/leaflet.css" />';
	echo'<script src="../style/map/leaflet.js"></script><script src="../style/map/mymarker.js"></script><form action="/?do=send" method="post" id="formadd"><input name="act" type="hidden" value="savegeomyfta"><input name="id" type="hidden" value="'.$id.'">';
	echo'<div id="map" style="height: 500px;"></div>';
	$geo_lan = ($getLoca['lan']?$getLoca['lan']:$config['geo_lan']);
	$geo_lon = ($getLoca['lon']?$getLoca['lon']:$config['geo_lon']);
	if(!empty($getLoca['lon']) && !empty($getLoca['lan']))
		$marker = 'L.marker(['.$getLoca['lan'].','.$getLoca['lon'].'],{icon:maplocation}).addTo(map);';
$script = <<<HTML
<script>
var lat = '{$geo_lan}'; 
var lon = '{$geo_lon}';
var map = L.map('map');
map.setView([lat, lon], 18);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
{$marker}
var popup = L.popup();	
function onMapClick(e) {
var lat = e.latlng.lat.toFixed(6);
var lon = e.latlng.lng.toFixed(6);
popup
.setLatLng(e.latlng)
.setContent('<b>{$lang['geounit']}</b> <br>' +
'<input name="lan" type="hidden" value="' + lat + '"><input name="lon" type="hidden" value="' + lon + '"><span class="koomap"><b>{$lang['geo']}</b>: ' + lat + ' ' + lon + '</span><br>' +
'<button type="submit" class="cssadd">{$lang['save']}</button>')
.openOn(map);
}
map.on('click', onMapClick);</script>
HTML;
	echo $script.'</form>';
	okno_end();	
	}
	}		
}elseif($act=='addnote'){
	$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;	
	if($id){
		$getLoca = $db->Fast($PMonTables['unit'],'id, note',['id'=>$id]);
		if(!empty($getLoca['id'])){
			okno_title($lang['unitnote']);
			echo'<form action="/?do=send" method="post" id="formadd"><input name="act" type="hidden" value="savenoteunit"><input name="id" type="hidden" value="'.$id.'">';
			echo form(['name'=>'Нотатка','descr'=>'','pole'=>'<textarea class="textarea1" rows="7" name="note">'.($getLoca['note']?$getLoca['note']:'').'</textarea>']);
			echo'</form><div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['save'].'</button></div>';
			okno_end();	
		}
	}	
}elseif($act=='maplocation'){
	$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;	
	if($id){
	$getLoca = $db->Fast($PMonTables['location'],'*',['id'=>$id]);
	if(!empty($getLoca['id'])){
	okno_title($lang['getlocation']);
	echo'<link rel="stylesheet" href="../style/map/leaflet.css" />';
	echo'<script src="../style/map/leaflet.js"></script><script src="../style/map/mymarker.js"></script><form action="/?do=send" method="post" id="formadd"><input name="act" type="hidden" value="savegeolocation"><input name="id" type="hidden" value="'.$id.'">';
	echo'<div id="map" style="height: 500px;"></div>';
	$geo_lan = ($getLoca['lan']?$getLoca['lan']:$config['geo_lan']);
	$geo_lon = ($getLoca['lon']?$getLoca['lon']:$config['geo_lon']);
	if(!empty($getLoca['lon']) && !empty($getLoca['lan']))
		$marker = 'L.marker(['.$getLoca['lan'].','.$getLoca['lon'].'],{icon:maplocation}).addTo(map);';
$script = <<<HTML
<script>
var lat = '{$geo_lan}'; 
var lon = '{$geo_lon}';
var map = L.map('map');
map.setView([lat, lon], 18);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
{$marker}
var popup = L.popup();	
function onMapClick(e) {
var lat = e.latlng.lat.toFixed(6);
var lon = e.latlng.lng.toFixed(6);
popup
.setLatLng(e.latlng)
.setContent('<b>{$lang['add_geo_base']}</b> <br>' +
'<input name="lan" type="hidden" value="' + lat + '"><input name="lon" type="hidden" value="' + lon + '"><span class="koomap"><b>{$lang['geo']}</b>: ' + lat + ' ' + lon + '</span><br>' +
'<button type="submit" class="cssadd">{$lang['save']}</button>')
.openOn(map);
}
map.on('click', onMapClick);
</script>
HTML;
	echo $script.'</form>';
	okno_end();	
	}
	}
}elseif($act=='editlocation'){
	if(!empty($USER['class']) && $USER['class']>=5){
		$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;	
		if($id){
			$getLoca = $db->Fast($PMonTables['location'],'*',['id'=>$id]);
			if(!empty($getLoca['id'])){
				okno_title($lang['editlocation'].' '.$getLoca['name']);
				echo'<form action="/?do=send" method="post" id="formadd" enctype="multipart/form-data"><input name="act" type="hidden" value="saveeditlocation"><input name="id" type="hidden" value="'.$getLoca['id'].'">';
				echo form(['name'=>$lang['name'],'descr'=>'','pole'=>'<input required name="name" class="input1" type="text" value="'.$getLoca['name'].'">']);	
				echo form(['name'=>'','descr'=>'','pole'=>'<textarea name="note" class="input1" rows="7" style="height: 100px;">'.$getLoca['note'].'</textarea>']);	
				echo form(['name'=>$lang['photo'],'descr'=>$lang['photo_info'],'pole'=>'<input type="file" id="file" name="file" multiple>']);	
				echo'</form><div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['save'].'</button></div>';
				okno_end();	
			}
		}
	}
}elseif($act=='delgroup'){
	if(!empty($USER['class']) && $USER['class']>=5){
		$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;	
		if($id){
			$getGR = $db->Fast($PMonTables['gr'],'*',['id'=>$id]);
			if(!empty($getGR['id'])){
				okno_title($lang['delet']);
				echo'<form action="/?do=send" method="post" id="formadd"><input name="id" type="hidden" value="'.$getGR['id'].'"><input name="act" type="hidden" value="delgroup">';
				echo'<div class="redton">Group delet?</div>';
				echo'</form><div class="polebtn"><button type="submit" form="formadd"  style="background: tomato;" value="submit">'.$lang['delet'].'</button></div>';
				okno_end();	
			}
		}
	}
}elseif($act=='delgroupdev'){
	if(!empty($USER['class']) && $USER['class']>=5){
		$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;	
		if($id){
			$getGRswitch = $db->Fast($PMonTables['switch'],'*',['id'=>$id]);
			if(!empty($getGRswitch['id'])){
				okno_title($lang['delet']);
				echo'<form action="/?do=send" method="post" id="formadd"><input name="id" type="hidden" value="'.$getGRswitch['id'].'"><input name="act" type="hidden" value="delgroupdev">';
				echo'<div class="redton">Group delet?</div>';
				echo'</form><div class="polebtn"><button type="submit" form="formadd"  style="background: tomato;" value="submit">'.$lang['delet'].'</button></div>';
				okno_end();	
			}
		}
	}
}elseif($act=='deletlocation'){
	if(!empty($USER['class']) && $USER['class']>=5){
		$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;	
		if($id){
			$getLoca = $db->Fast($PMonTables['location'],'*',['id'=>$id]);
			if(!empty($getLoca['id'])){
				okno_title($lang['delet']);
				echo'<form action="/?do=send" method="post" id="formadd"><input name="id" type="hidden" value="'.$getLoca['id'].'"><input name="act" type="hidden" value="deletlocation">';
				echo'<div class="redton">'.$lang['delet_location'].'</div>';
				echo'</form><div class="polebtn"><button type="submit" form="formadd"  style="background: tomato;" value="submit">'.$lang['delet'].'</button></div>';
				okno_end();	
			}
		}
	}
}elseif($act=='deletuser'){
	if(!empty($USER['class']) && $USER['class']>=6){
		$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;	
		if($id){
			$getUsers = $db->Fast($PMonTables['users'],'*',['id'=>$id]);
			if(!empty($getUsers['id'])){
				okno_title($lang['delet_us'].' '.$getUsers['username']);
				echo'<form action="/?do=send" method="post" id="formadd"><input name="id" type="hidden" value="'.$getUsers['id'].'"><input name="act" type="hidden" value="deletuser"><div class="redton">'.$lang['delet_users'].'</div></form><div class="polebtn"><button type="submit" form="formadd"  style="background: tomato;" value="submit">'.$lang['delet'].'</button></div>';
				okno_end();	
			}
		}
	}
}elseif($act=='delmonitor'){
	$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;	
	if($id){
		if(!empty($USER['class']) && $USER['class']>=4){
		okno_title($lang{'delet_onu_mon'});
		echo'<form action="/?do=send" method="post" id="formadd"><input name="idonu" type="hidden" value="'.$id.'"><input name="act" type="hidden" value="delmonitor"><div class="redton">Delet device</div></form><div class="polebtn"><button type="submit" form="formadd"  style="background: tomato;" value="submit">'.$lang['delet'].'</button></div>';
		okno_end();
		}
	}
}elseif($act=='addmonitor'){
	$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;	
	if($id){
		okno_title('Додати ONU в моніторинг');
		echo'<form action="/?do=send" method="post" id="formadd" ><input name="act" type="hidden" value="addmonitor"><input name="idonu" type="hidden" value="'.$id.'">';
		echo form(['name'=>$lang['name'],'descr'=>'','pole'=>'<input required name="name" class="input1" type="text">']);	
		echo'</form><div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['add'].'</button></div>';
		okno_end();	
	}
}elseif($act=='newlocation'){
	okno_title($lang['addlocation'].' '.$getLoca['name']);
	echo'<form action="/?do=send" method="post" id="formadd" enctype="multipart/form-data"><input name="act" type="hidden" value="newlocation">';
	echo form(['name'=>$lang['name'],'descr'=>'','pole'=>'<input required name="name" class="input1" type="text">']);	
	echo form(['name'=>'','descr'=>'','pole'=>'<textarea name="note" class="input1" rows="7" style="height: 100px;"></textarea>']);	
	echo form(['name'=>$lang['photo'],'descr'=>$lang['photo_info'],'pole'=>'<input type="file" id="file" name="file" multiple>']);	
	echo'</form><div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['add'].'</button></div>';
	okno_end();	
}elseif($act=='addgroup'){
	okno_title($lang['addlocation'].' '.$getLoca['name']);
	echo'<form action="/?do=send" method="post" id="formadd"><input name="act" type="hidden" value="addgroup">';
	echo form(['name'=>$lang['name'],'descr'=>'','pole'=>'<input required name="name" class="input1" type="text">']);	
	echo'</form><div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['add'].'</button></div>';
	okno_end();	
}elseif($act=='newuser'){
	$selectclass = '<select class="select" name="class" id="class">';
	$selectclass .= '<option value="1">'.$lang['class1'].'</option>';
	$selectclass .= '<option value="2">'.$lang['class2'].'</option>';
	$selectclass .= '<option value="3">'.$lang['class3'].'</option>';
	$selectclass .= '<option value="4">'.$lang['class4'].'</option>';
	$selectclass .= '<option value="5">'.$lang['class5'].'</option>';
	$selectclass .= '<option value="6">'.$lang['class6'].'</option>';
	$selectclass .= '<option value="7">'.$lang['class7'].'</option>';
	$selectclass .= '</select>';
	okno_title($lang['add_new_users']);
	echo'<form action="/?do=send" method="post" id="formadd"><input name="act" type="hidden" value="newuser">';
	echo form(['name'=>$lang['username'],'descr'=>'','pole'=>'<input autocomplete="off" required name="username" class="input1" type="text">']);	
	echo form(['name'=>$lang['class'],'descr'=>'','pole'=>$selectclass]);	
	echo form(['name'=>$lang['password'],'descr'=>'','pole'=>'<input autocomplete="off" required name="password" class="input1" type="text">']);	
	echo form(['name'=>$lang['mail'],'descr'=>'','pole'=>'<input autocomplete="off" required name="mail" class="input1" type="text">']);	
	echo form(['name'=>'Прив`язка до IP','descr'=>'','pole'=>'<input style="position:relative;top:2px;margin-right:5px;" type="checkbox" id="onlyip" name="onlyip"><label for="onlyip">включити прив`язку</label>']);	
	echo form(['name'=>'ІР користувача','descr'=>'','pole'=>'<input name="setip" class="input1" type="text">']);	
	echo'</form><div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['add'].'</button></div>';
	okno_end();	
}elseif($act=='edituser'){
		if(!empty($USER['class']) && $USER['class']>=6){
		$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;	
		if($id){
			$getUsers = $db->Fast($PMonTables['users'],'*',['id'=>$id]);
			if(!empty($getUsers['id'])){
				$selectclass = '<select class="select" name="class" id="class">';
				$selectclass .= '<option value="1" '.($getUsers['class']==1?'selected':'').'>'.$lang['class1'].'</option>';
				$selectclass .= '<option value="2" '.($getUsers['class']==2?'selected':'').'>'.$lang['class2'].'</option>';
				$selectclass .= '<option value="3" '.($getUsers['class']==3?'selected':'').'>'.$lang['class3'].'</option>';
				$selectclass .= '<option value="4" '.($getUsers['class']==4?'selected':'').'>'.$lang['class4'].'</option>';
				$selectclass .= '<option value="5" '.($getUsers['class']==5?'selected':'').'>'.$lang['class5'].'</option>';
				$selectclass .= '<option value="6" '.($getUsers['class']==6?'selected':'').'>'.$lang['class6'].'</option>';
				$selectclass .= '<option value="7" '.($getUsers['class']==7?'selected':'').'>'.$lang['class7'].'</option>';
				$selectclass .= '</select>';
				okno_title($lang['edit_users']);
				echo'<form action="/?do=send" method="post" id="formadd"><input name="id" type="hidden" value="'.$getUsers['id'].'"><input name="act" type="hidden" value="updateuser">';
				echo form(['name'=>$lang['username'],'descr'=>'','pole'=>'<input autocomplete="off" name="username" class="input1" type="text" value="'.$getUsers['username'].'">']);	
				echo form(['name'=>$lang['usname'],'descr'=>'','pole'=>'<input autocomplete="off" name="name" class="input1" type="text" value="'.$getUsers['name'].'">']);	
				echo form(['name'=>$lang['class'],'descr'=>'','pole'=>$selectclass]);	
				echo form(['name'=>$lang['edit_password'],'descr'=>'','pole'=>'<input style="position:relative;top:2px;margin-right:5px;" type="checkbox" id="editpass" name="editpass"><label for="onlyip">змінити пароль</label>']);	
				echo form(['name'=>$lang['newpassword'],'descr'=>'','pole'=>'<input autocomplete="off" name="newpassword" class="input1" type="text">']);	
				echo form(['name'=>$lang['mail'],'descr'=>'','pole'=>'<input autocomplete="off" name="mail" class="input1" type="text" value="'.$getUsers['email'].'">']);	
				echo form(['name'=>'Прив`язка до IP','descr'=>'','pole'=>'<input style="position:relative;top:2px;margin-right:5px;" type="checkbox" id="onlyip" name="onlyip" '.($getUsers['onlyip']=='on'?'checked':'').'><label for="onlyip">'.($getUsers['onlyip']=='on'?'включена прив`язка':'включити прив`язку').'</label>']);	
				echo form(['name'=>'ІР користувача','descr'=>'','pole'=>'<input name="setip" class="input1" type="text"  value="'.$getUsers['setip'].'">']);	
				echo'</form><div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['save'].'</button></div>';
				okno_end();	
			}
		}
	}
}elseif($act=='addpondog'){
	$SQLDeviceCron = $db->Multi('swcron');
	if(count($SQLDeviceCron)){
		foreach($SQLDeviceCron as $Cron){
			$Crondev[$Cron['oltid']]['id'] = $Cron['id'];
			$Crondev[$Cron['oltid']]['oltid'] = $Cron['oltid'];
			$Crondev[$Cron['oltid']]['status'] = $Cron['status'];
		}
	}	
	$SQLDevice = $db->Multi($PMonTables['switch'],'*',['monitor'=>'yes']);
	if(count($SQLDevice)){
		foreach($SQLDevice as $Device){
			$dev[$Device['id']]['id'] = $Device['id'];
			$dev[$Device['id']]['place'] = $Device['place'];
			$dev[$Device['id']]['netip'] = $Device['netip'];
		}
		$selectdevice = '<select class="select" name="deviceid" id="deviceid">';
		foreach($dev as $row){
			$selectdevice .= '<option value="'.$row['id'].'" '.($Crondev[$row['id']]['oltid']==$row['id']?'disabled':'').'>'.$row['place'].' '.$row['netip'].'</option>';
		}
		$selectdevice .= '</select>';
		$priority = '<select class="select" name="priority" id="priority">';
		$priority .= '<option value="2">'.$lang['priority2'].'</option>';
		$priority .= '<option value="3">'.$lang['priority3'].'</option>';
		$priority .= '</select>';
		okno_title($lang['addpondog']);
		echo'<form action="/?do=send" method="post" id="formadd"><input name="act" type="hidden" value="savepondog">';
		echo form(['name'=>$lang['device'],'descr'=>'','pole'=>$selectdevice]);
		#echo'<div class="subpole">'.$lang['type_priority'].'</div>';
		#echo form(['name'=>'','descr'=>'','pole'=>$priority]);
		echo'</form><div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['add'].'</button></div>';
		okno_end();
	}
}elseif($act=='monitor'){
	$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;	
	if($id){
		$device = $db->Fast($PMonTables['switch'],'*',['id'=>$id]);
		okno_title($lang['addssl'].' '.$device{'place'});
		echo'<form action="/?do=send" method="post" id="formadd"><input name="act" type="hidden" value="saveaccess"><input name="id" type="hidden" value="'.$device['id'].'">';
		echo form(['name'=>'IP','descr'=>'','pole'=>'<input required name="netip" class="input1" type="text" value="'.$device['netip'].'">']);
		echo'<div class="subpole">SNMP connection</div>';
		echo form(['name'=>'Community','descr'=>'SNMP Community "public"','pole'=>'<input name="public" class="input1" type="text" value="'.$device['snmpro'].'">']);
		echo form(['name'=>'Rewrite','descr'=>'SNMP Rewrite "private"','pole'=>'<input name="private" class="input1" type="text" value="'.$device['snmprw'].'">']);		
		echo'<div class="subpole">Telnet connection</div>';
		echo form(['name'=>'Username ','descr'=>'Authentication for Telnet "Username"','pole'=>'<input name="username" class="input1" type="text" value="'.$device['username'].'">']);
		echo form(['name'=>'Password','descr'=>'Authentication for Telnet "Password"','pole'=>'<input name="password" class="input1" type="text" value="'.$device['password'].'">']);
		echo'</form><div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['save'].'</button></div>';
		okno_end();	
	}
}elseif($act=='delete'){
	$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;	
	if($id){
		$device = $db->Fast($PMonTables['switch'],'*',['id'=>$id]);
		okno_title($device{'place'});
		echo'<form action="/?do=send" method="post" id="formadd"><input name="id" type="hidden" value="'.$device['id'].'"><input name="act" type="hidden" value="deletdevice"><div class="redton">Delet device</div></form><div class="polebtn"><button type="submit" form="formadd"  style="background: tomato;" value="submit">'.$lang['delet'].'</button></div>';
		okno_end();
	}
}