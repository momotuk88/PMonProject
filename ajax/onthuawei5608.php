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
	/// typedown
	@$res_type = snmp2_real_walk($getOLT['netip'],$getOLT['snmpro'],'1.3.6.1.4.1.2011.6.128.1.1.2.101.1.8.'.$getONT['zte_idport'].'.'.$getONT['keyonu']);
	if($res_type){
		$is = 1;
		foreach($res_type as $keys => $values){
			$resultType[$is]['type'] = HuaweiReasonGpon(str_replace('INTEGER:','',str_replace('Z','',str_replace('"','',str_replace(' ','',$values)))));
			$is++;
		}
	}
	/// typedown
	/// timdedown
	@$res_time = snmp2_real_walk($getOLT['netip'],$getOLT['snmpro'],'1.3.6.1.4.1.2011.6.128.1.1.2.101.1.7.'.$getONT['zte_idport'].'.'.$getONT['keyonu']);
	if($res_time){
		$i = 1;
		foreach($res_time as $key => $value){
			preg_match('/101.1.7.([\d]+).([\d]+).([\d]+)$/',$key,$arr);
			$result[$i]['time'] = str_replace('STRING:','',str_replace('Z','',str_replace('"','',$value)));
			$i++;
		}
	}
	// STYLE
	if(is_array($result) && is_array($resultType)){
		echo'<div class="block_all">';		
		echo'<h2><b>Актуальна інформація по відключенням  '.$getONT['type'].' '.$getONT['inface'].'</b><div class="list_key_spoiler show_all">Показати</div></h2>';
		echo'<div class="block_body">';	
		foreach($result as $key => $time_curent){
			echo'<div class="list_time">';
			echo'<div class="down_time">'.date( "Y-m-d H:i:s", (strtotime($time_curent['time']." -2 hour"))).'</div>';
			echo'<div class="down_type">'.$lang[$resultType[$key]['type']].'</div>';
			echo'</div>';
		}
		echo'</div>';
		echo'</div>';
	}
}
}
}
?>
<SCRIPT>
$(document).ready(function(){
	$('.list_key_spoiler').on('click',function(){
		$(this).parents('.block_all').find('.block_body').slideToggle(300);
		$(this).toggleClass('open');
		if ($(this).hasClass('show_all')){
			if ($(this).hasClass('open')) {
				$(this).html('Сховати');
				$('.list_key_spoiler:not(.open)').trigger('click');
			} else {
				$(this).html('Показати');
				$('.list_key_spoiler.open').trigger('click');
			}
		}
	});	
});
</SCRIPT>

