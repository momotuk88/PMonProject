<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
require ROOT_DIR.'/inc/init.api.php';
$checkapikey = true;
$api = true;
$result = array();
$zaputapi = array();
if(!$checkapikey){
	$result['error'] = 'key_error';
	$api = false;
}
if($_POST['do']){
	$do = totranslit($_POST['do']);
} else if (isset($do)){
	$do = totranslit($_POST['do']);
} else {
	$result['error'] = 'need_to_select_type';
	$api = false;
	$do = false;
}
if(isset($_POST['id']) && is_numeric($_POST['id']) && intval($_POST['id']) > 0){
	$id = (int)intval($_POST['id']);
	$switch = $db->Fast('switch','*',['id'=>$id]);
	if(!$switch['id']){
		$result['error'] = 'missing_equipment';
		$api = false;
	}
}
if(isset($_POST['pon'])){
	$pon = $_POST['pon'];
}
if(isset($_POST['keyonu']) && is_numeric($_POST['keyonu']) && intval($_POST['keyonu']) > 0){
	$keyonu = (int)intval($_POST['keyonu']);
}else{
	$keyonu = 0;
}
$keyport = $idonu = $idport = $oidid = 0;
if(isset($_POST['keyport']) && ctype_digit($_POST['keyport'])){
	$keyport = (int)$_POST['keyport'];
}
if(isset($_POST['idonu']) && ctype_digit($_POST['idonu'])){
	$idonu = (int)$_POST['idonu'];
}
if(isset($_POST['idport']) && ctype_digit($_POST['idport'])){
	$idport = (int)$_POST['idport'];
}
if(isset($_POST['oidid']) && ctype_digit($_POST['oidid'])){
	$oidid = (int)$_POST['oidid'];
}
if(isset($_POST['types'])){
	$types = $_POST['types'];
	$types = trim($types, ',');
}
if(isset($_POST['oid'])){
	$getoid = totranslit($_POST['oid']);
	$zaputapi['oid'] = $getoid;
}
if(isset($_POST['swid'])){
	$getswid = (int)$_POST['swid'];
	$zaputapi['swid'] = $getswid;
}
$monitorApi = new MonitorApi();
if($api && $checkapikey){
	$zaputapi['oidid'] = $switch['oidid'];
	$zaputapi['id'] = $switch['id'];
	$zaputapi['netip'] = $switch['netip'];
	$zaputapi['snmpro'] = $switch['snmpro'];
	switch($do){
		case 'onu': // запити по ону
			if(isset($pon,$types)){
				$zaputapi['types'] = $types;
				if(isset($keyport))
					$zaputapi['keyport'] = $keyport;
				$zaputapi['keyonu'] = $keyonu;
				$zaputapi['pon'] = $pon;
				$zaputapi['global'] = 'onu';
				$array_api = $monitorApi->format($zaputapi);
				$result = $monitorApi->apiget($array_api);	
			}else{
				$result['error'] = 'missing_onu';
			}	
		break;
		case 'port': // моніторим статус портів
			$zaputapi['types'] = $types;
			if(isset($keyport))
				$zaputapi['keyport'] = $keyport;
			$result = $monitorApi->apiport($zaputapi);	
		break;
		case 'oid':
			if(!empty($zaputapi['oid'])){
				$result = $monitorApi->apigetAll($zaputapi);
			}else{
				$result['error'] = 'err5';
			}
		break;
		case 'ont':	
			if($_REQUEST['key']){
				$apikey = totranslit($_REQUEST['key']);
				$checkapikey = check_api_key($apikey);
			}
			if($idonu && $checkapikey){
				$result = result_ont_api($idonu);	
			}else{
				$result['error'] = 'missing_ont_request';
			}
		break;
		case 'device':
			if(!empty($zaputapi['oidid']))
				$result = $monitorApi->monitorDevice($zaputapi);
		break;
	}
}
header('Content-type: application/json');
echo json_encode((isset($result) ? $result : 'empty_result'));
die;
?>
