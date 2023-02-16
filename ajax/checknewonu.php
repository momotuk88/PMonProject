<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
if($_POST['id']){
	$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
	if($id){
		$noreg = false;
		$switch = $db->Fast('switch','*',['id'=>$id]);
		/// ZTE C320  not registered ZTE
		if($switch['oidid']==7){
			snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
			@$noreg = snmp2_real_walk($switch['netip'],$switch['snmpro'],'1.3.6.1.4.1.3902.1012.3.13.3.1.2');
		}		
		// HUAWEI
		elseif($switch['oidid']==14){

		}
		if($noreg){
			echo'<div id="ajaxonu"><a class="noregonulist" href="/?do=regonu&id='.$switch['id'].'"><img src="../style/img/accept.png">Не зареєстровані ONU<div class="countger">3</div></a></div>';
		}
	}
}

