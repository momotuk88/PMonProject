<?php
if(!defined('PONMONITOR')){
	die('Hacking attempt!');
}
class BDCOM_Gpon { 
    private $indexdevice = array();
    private $now = null;
    private $primary = 'status,dist,sn';
    private $configapionugpon = 'dist,eth,name,uptime,vendor,admin,status,sn,tx,rx,reason';
    private $filter = true;
    private $filters = [
		'/"/',
		'/Hex-/i','/OID: /i','/STRING: /i',
		'/Gauge32: /','/INTEGER: /i','/Counter32: /i',
		'/SNMPv2-SMI::enterprises\./i','/iso\.3\.6\.1\.4\.1\./i'
	];
    public function __construct($id = '', $AllConfig) {
		$this->initsnmp();
		$this->now = date('Y-m-d H:i:s');
		$this->filter = true;
		$this->id = $id;
		$this->ip = $AllConfig->switchIp[$id];
		$this->community = $AllConfig->switchCommunity[$id];
		$this->deviceoid = $AllConfig->SwitchOid;
	}  
	public function Support($check){
		switch ($check) {
			case 'port' : 
				return true;
			break;			
			case 'onu' : 
				return true;
			break;				
			case 'saveonu' : 
				return true;
			break;				
			case 'api' : 
				return true;
			break;				
			case 'fileonu' : 
				return true;
			break;			
		}	
	}
	public function Load(){	
		global $db, $PMonTables;
		$result = array();
		$ListIndex = false;
		if(is_array($this->deviceoid)){
			$LintNameOID = $this->deviceoid[$this->id]['onu']['listname']['gpon']['oid'];	
			if($LintNameOID){
				$ListIndex = $this->snmp->walk($this->ip,$this->community,$LintNameOID,true);
				$this->indexdevice = $ListIndex;
				$LintNameOID = str_replace('.'.$LintNameOID.'.','',$ListIndex);
			}
			if($ListIndex){
				$Index = explodeRows($LintNameOID);	
				if(is_array($Index)){
					foreach ($Index as $io => $eachsig) {
						$line = explode('=', $eachsig);
						if (isset($line[0]) && isset($line[1])) {
							$TempName['name'] = $line[1];
							$NameIndex = $this->prepareResult($TempName);
							if (preg_match('/(GPON[0-9]{1,2}\/[0-9]{1,2}:)[0-9]{1,2}/',$NameIndex['name'],$RexNam))
								$result[$io] = array('do' => 'onu','id'=>$this->id,'pon'=>'gpon','inface'=>str_replace('gpon','', strtolower(str_replace(' ', '',trim($RexNam[0])))),'types'=>$this->primary,'keyonu'=> trim($line[0]));
						}	
					}
					if(is_array($result)){
						$db->SQLupdate($PMonTables['onus'],['cron' => 2],['olt' => $this->id]);
					}else{
						$db->SQLinsert($PMonTables['swlog'],['deviceid' =>$this->id,'types' =>'switch','message' =>'emptyarraydata','added' =>$this->now]);
					}
				}
			}
		}
		return (is_array($result) ? $result : null);
	}
	public function ConfigApiOnu($data){
		$cnf = array('do' => 'onu','types' => $this->configapionugpon,'pon' => mb_strtolower($data['type']),'keyonu' => $data['keyonu'],'id' => $this->id);
		return $cnf;
	}
	public function Onu($dataPort,$dataOnu){
		$res = array();			
		if(is_array($dataPort)){
			foreach($dataPort as $type => $value) {
				$res[$type] = $this->preparedataBDCOM($value,$type);
			}
		}
		if(!$dataPort && !$res){
			$array_separated = explode(',',$this->configapionugpon);
			foreach($array_separated as $type) {
				$res[$type] = $dataOnu[$type];
			}
		}
		if(is_array($res)){
			$result = $this->updateonu($dataOnu,$res);
		}
		return (is_array($result) ? $result : null);
	}
	public function updateonu($ont,$getData){
		global $db, $lang, $config, $PMonTables;
		$result = array();	
		if(is_array($getData)){
			if(!empty($getData['tx'])){
				$SQLset['tx'] = $getData['tx'];
				$result['tx'] = $getData['tx'];
			}
			if(!empty($getData['rx'])){
				$SQLset['rx'] = $getData['rx'];
				$result['rx'] = $getData['rx'];
			}
			if(!empty($getData['reason'])) 
				$SQLset['reason'] = $getData['reason'];			
			if(!empty($getData['vendor'])) 
				$SQLset['vendor'] = $getData['vendor'];
			if(!empty($getData['dist'])) 
				$SQLset['dist'] = $getData['dist'];
			if($ont['status']==2 && $getData['status']==1){
				if(!empty($getData['timeaut']))
					$SQLset['online'] = $this->now;
				if($getData['offline'])
					$SQLset['online'] = $getData['offline'];
				$SQLset['status'] = 1;
			}elseif($ont['status']==1 &&  $getData['status']==2){
				$SQLset['offline'] = $this->now;
				$SQLset['status'] = 2;
			}
			if(is_array($SQLset)){
				$db->SQLupdate($PMonTables['onus'],$SQLset,['idonu' => $ont['idonu']]);
			}			
			$result['type'] = $ont['type'];
			$result['status'] = $getData['status'];
			$result['wan'] = $getData['eth'];			
			if(!empty($getData['reason']))	
				$result['reason'] = $getData['reason'];			
			if(!empty($getData['uptime']))	
				$result['uptime'] = $getData['uptime'];
			if(!empty($getData['vendor']))	
				$result['vendor'] = $getData['vendor'];
			if(!empty($getData['dist'])) 
				$result['dist'] = $getData['dist'];
			if(!empty($ont['lastrx']))
				$result['lastrx'] = $ont['lastrx'];
			if(!empty($getData['rx'])) 
				$result['rx'] = $getData['rx'];
			if(!empty($getData['tx'])) 
				$result['tx'] = $getData['tx'];
			return (is_array($result) ? $result : null);
		}
	}	
	public function preparedataBDCOM($dataApi,$type){
		$data = $this->clearData($dataApi);
		switch($type){
			case 'status':
				if(isset($data)){
					$result = ($data==1 ? 1 : 2);
				}else{
					$result = 2;
				}
			break;			
			case 'dist':
				if(isset($data)){
					$result = (int)$data;
				}else{
					$result = 0;
				}
			break;
			case 'rx':
				if($data){
					$result = $this->clear_rx($data);
				}else{
					$result = 0;
				}
			break;			
			case 'tx':
				if($data){
					$result = $this->clear_rx($data);
				}else{
					$result = 0;
				}
			break;
			case 'sn':
				if($dataApi)
					$result = $dataApi;
			break;
			case 'vendor':
				$result = $data;
			break;				
			case 'reason':
				$result = $this->reason($data);
			break;			
			case 'name':
				$result = $data;
			break;				
			case 'uptime':
				$result = $data;
			break;				
			case 'admin':
				$result = $data;
			break;			
			case 'eth':
				$result = $data;
				$result = ($result==1?'up':'down');				
			break;			
		}		
		return $result;
	}
	public function clearResult($value){
		$value = str_replace('"','',$value);
		$value = trim($value);
		$value = str_replace('/','',$value);
		return $value;
	}
	public function savePort($dataPort){
		global $db;
		if(!empty($dataPort['port'])){
			foreach($dataPort['port'] as $value){
				self::savePortSwitch($value);
			}
		}
		if(!empty($dataPort['pon'])){
			foreach($dataPort['pon'] as $value){
				self::savePonSwitch($value);
			}
		}
	}
	public function Port(){
		$data = array();		
		$OIdPortgpon = $this->deviceoid[$this->id]['onu']['listname']['gpon']['oid'];
		if(!$this->indexdevice){
			$ListPortTemp = $this->snmp->walk($this->ip,$this->community,$OIdPortgpon,true);
		}else{
			$ListPortTemp = $this->indexdevice;
		}
		if($ListPortTemp){
			$ListPortTemps = str_replace('.'.$OIdPortgpon.'.','',$ListPortTemp);
			$IndexGponPort = explodeRows($ListPortTemps);
			if(is_array($IndexGponPort)){
				$listPort = array();
				foreach ($IndexGponPort as $idPort => $ValuePort) {				
					$infPort = explode('=', $ValuePort);
					if(!empty($infPort[0]) && !empty($infPort[0])){
						$dataIndexPort = $this->clearResult($infPort[1]);
						if(!preg_match('/GPON0(\d+):(\d+)/i',$dataIndexPort) AND !preg_match('/VLAN/i',$dataIndexPort) AND !preg_match('/Null/i',$dataIndexPort)){
							$listPort[$idPort] = array('id' =>trim($infPort[0]),'typeport' => getTypePort($dataIndexPort),'name' => getNameBdcomport($dataIndexPort));
						}
					}				
				}
				$data['port'] = $listPort;
			}

			if(is_array($data['port'])){
				foreach($data['port'] as $idPonport => $valuePon){
					if(preg_match('/GPON/i',$valuePon['name'])) {
						preg_match('/GPON 0\/(\d+)/',$valuePon['name'],$mat);
						$listPon[$idPonport]['name'] = 'GPON 0/'.$mat[1].'';
						$listPon[$idPonport]['sort'] = $mat[1];
						$listPon[$idPonport]['sfpid'] = $valuePon['id'];
						$listPon[$idPonport]['cardcount'] = 128;
					}
				}
				if(is_array($listPon)){
					usort($listPon, function($arr, $brr){
						return ($arr['sort'] - $brr['sort']);	
					});
					$data['pon'] = $listPon;
				}
			}
		}				
		return $data;
	}
    protected function savePonSwitch($dataPort) {
		global $db, $PMonTables;
		$row = $db->Multi($PMonTables['switchpon'],'*',['oltid' => $this->id,'sfpid' => $dataPort['sfpid']]);
		if(!count($row))
			$db->SQLinsert($PMonTables['switchpon'],['support' => $dataPort['cardcount'],'sort' => $dataPort['sort'],'oltid' => $this->id,'pon' => $dataPort['name'],'sfpid' => $dataPort['sfpid'],'added' => $this->now]);
		if(!empty($dataPort['sfpid'])){		
			$db->SQLupdate($PMonTables['onus'],['portolt' => $dataPort['sfpid']],['olt' => $this->id,'zte_idport' => $dataPort['sort']]);
		}
		$allonu = $db->Multi($PMonTables['onus'],'*',['olt' => $this->id,'portolt' => $dataPort['sfpid']]);
		if(!empty($dataPort['sfpid'])){
			$SQLportset['count'] = count($allonu);
			$db->SQLupdate($PMonTables['switchpon'],$SQLportset,['sfpid' =>$dataPort['sfpid'],'oltid' => $this->id]);
		}
	}
    protected function savePortSwitch($data) {
		global $db, $PMonTables;
		if(!empty($data['id'])){	
			$row = $db->Fast($PMonTables['switchport'],'*',['deviceid' => $this->id, 'llid' => $data['id']]);
			if(!$row['id'])
				$db->SQLinsert($PMonTables['switchport'],['deviceid' => $this->id,'llid' => $data['id'],'nameport' => $data['name'],'typeport' => $data['typeport'],'operstatus' => 'none','added' => $this->now]);
		}
	}
    protected function initsnmp() {
        $this->snmp = new SnmpMonitor();
		if(!$this->snmp)
			die('snmp&');
    }  
    private function prepareResult(array $data): array {
        if($this->filter){
            $result = array_map(
                function($value) {
                    return preg_replace($this->filters, '', $value);
					},
				$data
			);
        }
        return ($this->filter && isset($result)) ? $result : $data;
    }
	public function tempUpdateSignalCheck(){	
		global $db;

	}
	public function tempSaveSignalSaveOnuGpon($dataOnu){	
		global $db, $PMonTables, $config;
		$savehistor = false;
		$onu = $db->Fast($PMonTables['onus'],'status,rx,idonu',['olt' => $this->id,'keyonu' => $dataOnu['keyonu']]);
		if(!empty($onu['idonu'])){
			if(!empty($dataOnu['rx'])){
				$rx = $this->clear_rx($dataOnu['rx']);
			}
			if($rx){
				$db->SQLupdate($PMonTables['onus'],['rx' => $rx,'rating' => 1],['idonu' => $onu['idonu']]);
				$savehistor = SignalMonitor($onu['status'], $rx, $onu['rx'], $onu['idonu']);
			}
			if(!empty($config['onugraph']) && $config['onugraph']=='on' && $savehistor){
				$db->SQLInsert($PMonTables['historyrx'],['device' => $this->id,'onu' => $onu['idonu'],'signal' => $rx,'datetime' => $this->now]);
			}
		}
	}	
	public function clearData($value){
		$value = str_replace('INTEGER:', '',$value);
		$value = str_replace('Hex-STRING:', '',$value);
		$value = str_replace('STRING:', '',$value);
		$value = str_replace('Gauge32:', '',$value);
		$value = str_replace('"', '',$value);
		$value = str_replace(' ', '',$value);
		$value = trim($value);	
		return $value;
	}
	public function clear_rx($value){
		if(preg_match('/6553/i',$value)) {
			$value = 0; 
		}else{
			$value = str_replace('"', '',$value);
			$value = trim($value);
			$value = str_replace('N/A',0,$value);
			$value = $value;
			$value = sprintf('%.2f',$value);
			$value = str_replace('0.00',0,$value);
		}
		return $value;
	}
	public function tempSaveOnuGpon($dataOnu){	
		global $db, $config, $lang, $PMonTables;
		if(!empty($dataOnu['inface'])){
			preg_match('/0\/(\d+):(\d+)/i',$dataOnu['inface'],$dataMatch);
			$indexPortOlt = $dataMatch[1];
		}
		if(!empty($dataOnu['sn']))
			$dataOnu['sn'] = $dataOnu['sn'];
		$dataOnu['status'] = (!empty($dataOnu['status']) ? $dataOnu['status'] : (!empty($dataOnu['dist']) ? 1 : 2));
		if(!empty($dataOnu['keyonu'])){
			$arr = $db->Fast($PMonTables['onus'],'*',['zte_idport' =>$indexPortOlt,'keyonu' =>$dataOnu['keyonu'],'olt' => $dataOnu['id']]); 
			if(!empty($arr['id'])){
				if($dataOnu['status']==1 && $arr['status']==2){
					$SQLset['rating'] = 7;
				}else{
					$SQLset['rating'] = 1;
				}
				$SQLset['updates'] = $this->now;
				$SQLset['cron'] = 1;
				if($dataOnu['status']==1 && $arr['status']==2){
					$SQLset['online'] = $this->now;
				}elseif($dataOnu['status']==2 && $arr['status']==1){
					$SQLset['offline'] = $this->now;
				}else{
					
				}
				$SQLset['status'] = $dataOnu['status'];
				$SQLset['type'] = $dataOnu['pon'];
				if(!empty($dataOnu['dist']))
					$SQLset['dist'] = (int)$dataOnu['dist'];
				if(!empty($dataOnu['sn']))
					$SQLset['sn'] = $dataOnu['sn'];
				if(!empty($dataOnu['inface']))
					$SQLset['inface'] = $dataOnu['inface'];	
				if($indexPortOlt){
					$SQLset['portolt'] = $indexPortOlt;
					$SQLset['zte_idport'] = $indexPortOlt;
				}
				$db->SQLupdate($PMonTables['onus'],$SQLset,['idonu' => $arr['idonu']]);
			}else{				
				$SQLinsert = array(
					'olt' => $dataOnu['id'],($dataOnu['status']==1?'online':'offline') => $this->now,'added' => $this->now,'updates' => $this->now,'rating' => 1,
					'keyonu' => $dataOnu['keyonu'],'status' => $dataOnu['status'],'sn' => $dataOnu['sn'],'inface' => $dataOnu['inface'],
					'dist' => (!empty($dataOnu['dist']) ? (int)$dataOnu['dist'] : 0),
					'type' => $dataOnu['pon'],'cron' => 1,
					'zte_idport' => $indexPortOlt, 'portolt' => $indexPortOlt
				);
				$db->SQLinsert($PMonTables['onus'],$SQLinsert);
			}
		}
	}
	public function saveOnuCommands(){
		global $db;

	}
	public function StatisticOLT(){	
		global $db, $PMonTables;
		$getPort = $db->Multi($PMonTables['switchpon'],'*',['oltid' => $this->id]);
		if(count($getPort)){
			foreach($getPort as $port){
				$getONUstatusPort = $db->Multi($PMonTables['onus'],'idonu,status',['olt' => $this->id,'zte_idport'=>$port['idportolt']]);
				$getONUstatusPortOn = $db->Multi($PMonTables['onus'],'idonu,status',['status' => 1,'olt' => $this->id,'zte_idport'=>$port['idportolt']]);
				$arrayPort[$port['id']]['port'] = $port['pon'];
				$arrayPort[$port['id']]['count'] = count($getONUstatusPort);
				$arrayPort[$port['id']]['online'] = count($getONUstatusPortOn);
				$arrayPort[$port['id']]['offline'] = count($getONUstatusPort) - count($getONUstatusPortOn);
			}
		}
		if(isset($arrayPort)){
			foreach($arrayPort as $idport => $value){
				$db->SQLupdate($PMonTables['switchpon'],['count' => ($value['count'] ?? 0),'online' => ($value['online'] ?? 0),'offline' => ($value['offline'] ?? 0)],['id' => $idport]);
			}
		}
	}
	public function getListOnuOnline(){	
		global $db, $PMonTables;
		$sqlList = $db->Multi($PMonTables['onus'],'keyonu,idonu,type',['olt' => $this->id, 'status' => 1]);
		$array = array();
		if(is_array($sqlList)){
			foreach($sqlList as $key => $value){
				if(!empty($value['keyonu']) && !empty($value['type']))
					$array[$key] = array('id'=>$this->id,'keyonu'=>$value['keyonu'],'pon'=>$value['type'],'do'=>'onu','types'=>'rx');
			}
		}
		return $array;
	}
	public function reason($check1){
		switch($check1) {
                case "0": #none(0), err20
					$type = 'err22'; 				
				break;
                case "1": #dying-gasp(1), err1
					$type = 'err1';				
				break;
                case "2": #laser-always-on(2),  err2
					$type = 'err2'; 
				break;
                case "3": #admin-down(3),  err27
					$type = 'err27';
				break;
                case "4": #omcc-down(4), err28 
					$type = 'err28';#pon to los	
				break;                
				case "5": #unknown(5),  err5
					$type = 'err5';
				break;				
				case "6": #pon-los(6), err6
					$type = 'err6';
				break;				
				case "7": #lcdg(7),   err7
					$type = 'err7';
				break;				
				case "8": #wire-down(8), err8
					$type = 'err8';
				break;				
				case "9": #omci-mismatch(9),  err9
					$type = 'err9';
				break;				
				case "10":#password-mismatch(10),  err29 
					$type = 'err29';
				break;				
				case "11": #reboot(11),  err12
					$type = 'err12';
				break;				
				case "12": #ranging-failed(12) err13
					$type = 'err13';
				break;
                default: 
					$type = 'err20';
				break;
        }
		return $type;
	}	
}
?>