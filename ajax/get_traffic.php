<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$portid = isset($_POST['portid']) ? Clean::int($_POST['portid']): null;
$getport = $db->Fast('switch_port','*',['id'=>$portid]);
$getswitch = $db->Fast('switch','*',['id'=>$getport['deviceid']]);
// підключення до комутатора через SNMP і отримання даних про трафік на порту
$port = $getport['llid'];
$snmp_data = snmp2_get($getswitch['netip'],$getswitch['snmpro'], "1.3.6.1.2.1.2.2.1.10.$port");
$snmp_data1 = snmp2_get($getswitch['netip'],$getswitch['snmpro'], "1.3.6.1.2.1.2.2.1.16.$port");
$snmp_data_array = explode("\n", $snmp_data);
$snmp_data_array1 = explode("\n", $snmp_data1);
$in_octets = intval(str_replace("1.3.6.1.2.1.2.2.1.10.$port = Counter32: ", "", $snmp_data_array[0])) * 8;
$out_octets = intval(str_replace("1.3.6.1.2.1.2.2.1.16.$port = Counter32: ", "", $snmp_data_array1[0])) * 8;
 // обробка даних та підготовка відповіді у форматі JSON
$result_array = array();
$result_array["in_bps"] = $in_octets;
$result_array["out_bps"] = $out_octets;
$result_array["in_mbps"] = $in_octets;
$result_array["out_mbps"] = $out_octets;

function formatMbps($value) {
  $mbps = round($value / 1000000, 2);
  return $mbps . ' MBps';
}
echo'in:'.formatMbps($result_array["in_mbps"]).' out:'.formatMbps($result_array["out_mbps"]).'';
?>