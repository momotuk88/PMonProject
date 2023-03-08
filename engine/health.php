<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$insert = $insert ?? null;
$timeout = 100000;
$retries = 5;
require ROOT_DIR.'/inc/init.monitor.php';
$sqlselectswitch = $db->Multi('switch', 'id,netip,snmpro,ping', ['monitor' => 'yes']);
if (count($sqlselectswitch) > 0) {
    snmp_set_quick_print(false);
    snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
    foreach ($sqlselectswitch as $arr) {
        $starttime = microtime(true);
        $data = @snmp2_get($arr['netip'], $arr['snmpro'], '1.3.6.1.2.1.1.1.0', $timeout, $retries);
        if ($data) {
            $insert = [
                'time' => sprintf('%.2f', (microtime(true) - $starttime)),
                'datetime' => date('Y-m-d H:i:s'),
                'system' => $arr['id'],
                'status' => 1,
            ];            
            $updates = ['timeping' => date('Y-m-d H:i:s')];
            if ($arr['ping'] === 'down') {
                $updates['ping'] = 'up';
            }
        } else {
            if ($arr['ping'] === 'up') {
                $insert = [
                    'time' => 0,
                    'datetime' => date('Y-m-d H:i:s'),
                    'system' => $arr['id'],
                    'status' => 2,
                ];
                $updates = [
                    'ping' => 'down',
                    'timeping' => date('Y-m-d H:i:s'),
                ];                
                // $updates['monitor'] = 'no'; (commented out as it's not being used)
            }
        }
        if (isset($insert)) {
            $db->SQLinsert($PMonTables['pingstats'], $insert);
        }
        if (isset($updates)) {
            $db->SQLupdate($PMonTables['switch'], $updates, ['id' => $arr['id']]);
        }
    }
}
sleep(3);
$sqlselectswitchafter = $db->Multi('switch', 'id, netip, snmpro, oidid', ['monitor' => 'yes']);
if (count($sqlselectswitchafter)) {
    foreach ($sqlselectswitchafter as $switch) {
        if (in_array($switch['oidid'], [12, 2, 3, 1])) {
            $res_snmp = get_curl_api(['do' => 'device', 'id' => $switch['id']], true, 10);
            if (is_array($res_snmp['result'])) {
                $arrhealth = [];
                foreach ($res_snmp['result'] as $type => $value) {
                    if ($type == 'cpu') {
                        $arrhealth['cpu'] = $value;
                    }
                    if ($type == 'temp') {
                        $arrhealth['temp'] = $value;
                        $health['types'] = 'com1';
                    }
                }
                if (!empty($arrhealth) && !empty($health['types'])) {
                    $dataswitch[$switch['id']]['data'] = serialize($arrhealth);
                    $dataswitch[$switch['id']]['types'] = $health['types'];
                }
            }
        }
    }
}
if(is_array($dataswitch)){
	foreach($dataswitch as $switch => $value){
		if($switch && !empty($value['data'])){
			$db->query("INSERT INTO `".$PMonTables['monitoring']."` (`datetime`,`types`,`values`,`deviceid`) VALUES ('".date('Y-m-d H:i:s')."','".$value['types']."','".$value['data']."','".(int)$switch."')");
		}
	}
}
if(strtotime($config['backup']) < strtotime(date('Y-m-d H:i:s').' -20hour')){
if (!is_dir('backup')) {
    mkdir('backup');
}
$mysqlhost = DBHOST;
$mysqlname = DBNAME;
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
];
try {
    $pdo = new PDO("mysql:host=$mysqlhost;dbname=$mysqlname;charset=utf8",DBUSER,DBPASS,$options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
$stmt = $pdo->query('SHOW TABLES');
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
$file = fopen('backup/['.date('Y-m-d H:i:s').']_backup.sql', 'w');
foreach ($tables as $table) {
    $stmt = $pdo->query("SELECT * FROM `$table`");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    fwrite($file, "DROP TABLE IF EXISTS `$table`;\n");
    $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
    $create_table = $stmt->fetch(PDO::FETCH_COLUMN, 1);
    fwrite($file, $create_table . ";\n");
    foreach ($rows as $row) {
        $row = array_map('addslashes', $row);
        fwrite($file, "INSERT INTO `$table` (`" . implode('`, `', array_keys($row)) . "`) VALUES ('" . implode('\', \'', $row) . "');\n");
    }
}
fclose($file);
$db->SQLupdate($PMonTables['config'],['value'=>date('Y-m-d H:i:s'),'update'=>date('Y-m-d H:i:s')],['name'=>'backup']);
}
?>
