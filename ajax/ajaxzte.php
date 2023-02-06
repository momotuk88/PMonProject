<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$dia = isset($_POST['dia']) ? Clean::int($_POST['dia']): null;
$onu = isset($_POST['onu']) ? Clean::int($_POST['onu']): null;
$act = isset($_POST['act']) ? Clean::text($_POST['act']): null;
$port = isset($_POST['act']) ? Clean::int($_POST['port']): null;
switch($act){
	case 'port': 
		if($dia==1){
		// enable port
		}else{
		// disable port
		}
		$dataOnu = $db->Fast('onus','*',['idonu'=>$onu]);
		$dataSwitch = $db->Fast('switch','*',['id'=>$dataOnu['olt']]);
		#if(!empty($dataSwitch['username']) && !empty($dataSwitch['password']) && !empty($dataOnu['type'])){
			okno_title('Налаштування інтерфейсу');
			echo'<textarea name="code" rows="5" cols="33" class="telnetcmd">'.$cmd.'</textarea>';
			okno_end();	
		#}
	break;	
	case 'reboot': 	
		$dataOnu = $db->Fast('onus','*',['idonu'=>$onu]);
		$dataSwitch = $db->Fast('switch','*',['id'=>$dataOnu['olt']]);
		if(!empty($dataSwitch['username']) && !empty($dataSwitch['password']) && !empty($dataOnu['type'])){

		}
	break;	
}
