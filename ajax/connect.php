<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$act = isset($_POST['act']) ? Clean::text($_POST['act']): null;
$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
if($id && $act=='listport'){
	$listPort = $db->Multi('switch_port','*',['deviceid' => $id]);
	if(isset($listPort)){
		$select = '<select class="select" name="connp" id="connp">';
		#$select .= '<option value="0"></option>';
		foreach($listPort as $row){
			$dataPort = $db->Fast('connect_port','*',['curp'=>$row['id'],'curd'=>$id]);
			if(!isset($dataPort['id']) && preg_match('/sfp/i',$row['typeport']))
				$select .= '<option value="'.$row['id'].'">'.$row['nameport'].' '.$row['descrport'].'</option>';
		}
		$select .= '</select>';
		echo $select;
		die;
	}else{
		echo '';
		die;	
	}
}elseif($id && $act=='edit'){
	$dataConnect = $db->Fast('connect_port','*',['id'=>$id]);
	if(!empty($dataConnect['id'])){
	$sfpArray = getListSFParray();
	$dataSwitchCur = $db->Fast('switch','place,inf,model,netip',['id'=>$dataConnect['curd']]);
	okno_title('Комутація #:'.$id);	
	echo'<form action="/?do=send" method="post" id="formadd"><input name="act" type="hidden" value="updateconnect">';
	// назва комутатора
	echo'<div class="subpole-bord">'.$dataSwitchCur['inf'].' '.$dataSwitchCur['model'].' - <b>'.$dataSwitchCur['place'].'</b> ['.$dataSwitchCur['netip'].']</div>';
	$dataCurrentPort = $db->Multi('switch_port','*',['deviceid'=>$dataConnect['curd']]);
	if(count($dataCurrentPort)){
		$pole_port_cur = '<select class="select" name="curp" id="curp">';
		#$pole_port_cur = '<option value="0"></option>';
		foreach($dataCurrentPort as $CurPort){
			if(preg_match('/sfp/i',$CurPort['typeport'])) {
				$pole_port_cur .= '<option value="'.$CurPort['id'].'" '.($CurPort['id']==$dataConnect['curp']?'selected':'').'>'.$CurPort['nameport'].'</option>';
			}
		}
		$pole_port_cur .='</select>';
	}else{
		$pole_port_cur = '';
	}	
	// назва порта куди включено
	echo form(['name'=>$lang['port'],'descr'=>$lang['curentconnectport'],'pole'=>$pole_port_cur]);
	// SFP яка включено в порт діючий
	if(is_array($sfpArray) && !empty($dataConnect['cursfp'])){
		$pole_sfp_cur = '<select class="select" name="cursfp" id="curp"><option value="0"></option>';
		foreach($sfpArray as $idSfpCur => $CurSfp){			
			$pole_sfp_cur .= '<option value="'.$CurSfp['id'].'" '.($CurSfp['id']==$dataConnect['cursfp']?'selected':'').'>'.$CurSfp['connector'].' '.$CurSfp['types'].' '.$CurSfp['wavelength'].' '.$CurSfp['dist'].'km '.$CurSfp['speed'].'G</option>';
		}
		$pole_sfp_cur .='</select>';
		echo form(['name'=>$lang['sfp'],'descr'=>$lang['current'],'pole'=>$pole_sfp_cur]);
	}
	// Комутатор в який включено
	echo'<div class="subpole-bord">'.$lang['selectconnectdevice'].'</div>';
	$dataSwitchCon = $db->Fast('switch','place,inf,model,netip',['id'=>$dataConnect['connd']]);
	echo form(['name'=>$lang['name'],'descr'=>$lang['modeldevice'],'pole'=>$dataSwitchCon['place'].' '.$dataSwitchCon['inf'].' '.$dataSwitchCon['model']]);
	$dataConnectPort = $db->Multi('switch_port','*',['deviceid'=>$dataConnect['connd']]);
	if(count($dataConnectPort)){
		$pole_port_conn = '<select class="select" name="conp" id="curp"><option value="0"></option>';
		foreach($dataConnectPort as $ConPort){
			if(preg_match('/sfp/i',$ConPort['typeport'])) {
				$pole_port_conn .= '<option value="'.$ConPort['id'].'" '.($ConPort['id']==$dataConnect['connp']?'selected':'').'>'.$ConPort['nameport'].'</option>';
			}
		}
		$pole_port_conn .='</select>';
	}else{
		$pole_port_conn = '';
	}	
	echo form(['name'=>$lang['port'],'descr'=>$lang['connectport'],'pole'=>$pole_port_conn]);
	// SFP яка включено в порт діючий
	if(is_array($sfpArray) && !empty($dataConnect['connsfp'])){
		$pole_sfp_conn = '<select class="select" name="connfp" id="connp"><option value="0"></option>';
		foreach($sfpArray as $idSfpConn => $ConnSfp){			
			$pole_sfp_conn .= '<option value="'.$ConnSfp['id'].'" '.($ConnSfp['id']==$dataConnect['connsfp']?'selected':'').'>'.$ConnSfp['connector'].' '.$ConnSfp['types'].' '.$ConnSfp['wavelength'].' '.$ConnSfp['dist'].'km '.$ConnSfp['speed'].'G</option>';
		}
		$pole_sfp_conn .='</select>';
		echo form(['name'=>$lang['sfp'],'descr'=>$lang['current'],'pole'=>$pole_sfp_conn]);
	}
	echo'<div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['save'].'</button></form>';
	// access
	echo'<form action="/?do=send" method="post" id="formadds" style="position:relative;display:inline-block;"><input name="act" type="hidden" value="delconnect">';
	echo'<input name="id" type="hidden" value="'.$id.'"><button style=" background: #e55959;" type="submit" form="formadds" value="submit">'.$lang['deletconnect'].'</button>';
	// access	
	echo'</div></form>';
	okno_end();
	}
}elseif($id && $act=='add'){
	$dataPort = $db->Fast('switch_port','*',['id'=>$id]);
	if(!empty($dataPort['id'])){
		$dataSwitch = $db->Fast('switch','*',['id'=>$dataPort['deviceid']]);
		okno_title($lang['newconnect']);
		echo'<form action="/?do=send" method="post" id="formadd">';
		echo'<input name="curp" type="hidden" value="'.$dataPort['id'].'">';
		echo'<input name="curd" type="hidden" value="'.$dataPort['deviceid'].'">';
		echo'<input name="act" type="hidden" value="saveconnect">';
			$listDevice = $db->Multi('switch','id,place,inf,model');
			if(count($listDevice)){
				$pole_device = '<select class="select" name="connd" id="connd"><option value="0"></option>';
				foreach($listDevice as $Device)
					$pole_device .= '<option value="'.$Device['id'].'">'.$Device['place'].' '.$Device['inf'].''.$Device['model'].'</option>';
				$pole_device .='</select>';
			}else{
				$pole_device = '';
			}
		echo form(['name'=>$lang['device'],'descr'=>$lang['currentdevice'],'pole'=>$dataSwitch['place'].' '.$dataPort['nameport']]);
		$sfpArray = getListSFParray();
		$pole_sfp_cur = '<select class="select" name="cursfp" id="curp">';
		foreach($sfpArray as $idSfpCur => $CurSfp)			
			$pole_sfp_cur .= '<option value="'.$CurSfp['id'].'">'.$CurSfp['connector'].' '.$CurSfp['types'].' '.$CurSfp['wavelength'].' '.$CurSfp['dist'].'km '.$CurSfp['speed'].'G</option>';
		$pole_sfp_cur .='</select>';
		echo form(['name'=>$lang['sfp'],'descr'=>$lang['selectsfp'],'pole'=>$pole_sfp_cur]);
		echo form(['name'=>$lang['connectto'],'descr'=>$lang['selectconnectdevice'],'pole'=>$pole_device]);
		echo form(['name'=>$lang['port'],'descr'=>$lang['selectconnectdevice'],'pole'=>'<span class="js_replace"></span>']);
		$pole_sfp_conn = '<select class="select" name="connsfp" id="connsfp">';
		foreach($sfpArray as $idSfpCurs => $connSfp)			
			$pole_sfp_conn .= '<option value="'.$connSfp['id'].'">'.$connSfp['connector'].' '.$connSfp['types'].' '.$connSfp['wavelength'].' '.$connSfp['dist'].'km '.$connSfp['speed'].'G</option>';
		$pole_sfp_conn .='</select>';
		echo form(['name'=>$lang['sfp'],'descr'=>$lang['selectsfp'],'pole'=>$pole_sfp_conn]);
		echo'</form>';
		echo'<div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['connect'].'</button></div>';
		?><script>$('#connd').on('change', function() {var selected = $(this).val();$.post(root+'ajax/connect.php',{act:'listport',id:selected},function(response){$('.js_replace').html(response);$('.btnsend').show();},'html');});</script><?php
	okno_end();
	}
}
