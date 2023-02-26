<?php
if(!defined('PONMONITOR')){
	die('Hacking attempt!');
}
class BDCOM_Epon { 
    private $indexdevice = array();
    private $now = null;
    private $primary = 'status,dist,inface';
    private $configapionuepon = 'rx,eth,inface,dist,tx,pvid,model,vendor,rxolt,status,mac';
    private $filter = true;
    private $filters = [
		'/"/',
		'/Hex-/i','/OID: /i','/STRING: /i',
		'/Gauge32: /','/INTEGER: /i','/Counter32: /i',
		'/SNMPv2-SMI::enterprises\./i','/iso\.3\.6\.1\.4\.1\./i'
	];
    public function __construct($swid,$allcfg){
		if(is_numeric($swid)){
			$this->snmp= new SnmpMonitor();
			$this->now = date('Y-m-d H:i:s');
			$this->filter = true;
			$this->id = $swid;
			$this->ip = $allcfg->switchIp[$swid];
			$this->community = $allcfg->switchCommunity[$swid];
			$this->deviceoid = $allcfg->SwitchOid;
		}
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
		if(is_numeric($this->id)){		
			$listinface = '1.3.6.1.4.1.3320.101.10.1.1.3';
			$listonu = $this->snmp->walk($this->ip,$this->community,$listinface,false);	
			if($listonu){
				$this->indexdevice = $listonu;
				$indexOnu = explodeRows(str_replace('.'.$listinface.'.','',$listonu));
				if($indexOnu){
					foreach($indexOnu as $io => $eachsig) {
						$line = explode('=', $eachsig);
						if(isset($line[0]) && isset($line[1])) {
							$mac = ClearDataMac($line[1]);
							if($mac)
								$result[$io] = array('do' => 'onu','id'=>$this->id,'pon'=>'epon','mac'=>$mac,'types'=>$this->primary,'keyonu'=> trim($line[0]));
						}	
					}
					if(is_array($result)){
						$db->SQLupdate($PMonTables['onus'],['cron' => 2],['olt' => $this->id]);
					}
				}
			}else{
				$db->SQLinsert($PMonTables['swlog'],['deviceid' =>$this->id,'types' =>'switch','message' =>'err1 empty snmpwalk <b>'.$listinface.'</b>','added' =>$this->now]);
			}
			if(is_array($result)){
				checkerONU($result,$this->id);
			}
			return (is_array($result) ? $result : null);
		}
	}
	public function ConfigApiOnu($data){
		$cfg = array('do' => 'onu','types' => $this->configapionuepon,'pon' => mb_strtolower($data['type']),'keyonu' => $data['keyonu'],'id' => $this->id);
		return $cfg;
	}
	public function statusBdcom($status){
		switch($status){
            case "0": return 1; break;
            case "1": return 1; break;
            case "2": return 2; break;
            case "3": return 1; break;
            case "4": return 2; break;
        }
	}
	public function Onu($dataPort,$dataOnu){
		$res = array();	
		if(is_array($dataPort)){
			foreach($dataPort as $type => $value) {
				$res[$type] = $this->preparedataBDCOM($value,$type);
			}
		}
		if(!$dataPort && !$res){
			$array_separated = explode(',',$this->configapionuepon);
			foreach($array_separated as $type) {
				$res[$type] = $dataOnu[$type];
			}
		}
		if(is_array($res)){
			$result = $this->updateonu($dataOnu,$res);
		}else{
			$result = false;
		}
		return $result;
	}
	public function updateonu($ont,$getData){
		global $db, $lang, $config;
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
			if(!empty($getData['model'])) 
				$SQLset['model'] = $getData['model'];
			if(!empty($getData['vendor'])) 
				$SQLset['vendor'] = $getData['vendor'];			
			$SQLset['status'] = $this->statusBdcom($getData['status']);			
			if(!empty($getData['mac'])) 
				$SQLset['mac'] = $getData['mac'];
			if(!empty($getData['dist'])) 
				$SQLset['dist'] = $getData['dist'];
			if($ont['status']==2 && $getData['status']==1){
				$SQLset['online'] = $this->now;
				$SQLset['status'] = 1;
			}elseif($ont['status']==1 &&  $getData['status']==2){
				$SQLset['offline'] = $this->now;
				$SQLset['status'] = 2;
			}
			if(is_array($SQLset)){
				$SQLwhere['idonu'] = $ont['idonu'];
				$db->SQLupdate('onus',$SQLset,$SQLwhere);
			}			
			$result['type'] = $ont['type'];
			$result['status'] = $getData['status'];
			$result['wan'] = $getData['eth'];
			if(!empty($getData['mac']))			
				$result['mac'] = $getData['mac'];			
			if(!empty($getData['model']))	
				$result['model'] = $getData['model'];
			if(!empty($getData['vendor']))	
				$result['vendor'] = $getData['vendor'];
			if(!empty($getData['dist'])) 
				$result['dist'] = $getData['dist'];			
			if(!empty($getData['pvid'])) 
				$result['pvid'] = $getData['pvid'];
			if(!empty($ont['lastrx']))
				$result['lastrx'] = $ont['lastrx'];
			if(!empty($getData['rxolt'])) 
				$result['rxolt'] = $getData['rxolt'];
			if(!empty($getData['rx'])) 
				$result['rx'] = $getData['rx'];
			if(!empty($getData['tx'])) 
				$result['tx'] = $getData['tx'];
			return $result;
		}
	}	
	public function volot_css($value){
		return (int)$value;
	}
	public function preparedataBDCOM($dataApi,$type){
		$data = $this->clearData($dataApi);
		switch($type){
			case 'status':
				$result = $this->statusBdcom($data);
			break;			
			case 'dist':
				if(isset($data))
					$result = (int)$data;
			break;
			case 'rx':
				if($data)
					$result = $this->clear_rx($data);
				break;			
			case 'rxolt':
				if($data)
					$result = $this->clear_rx($data);
			break;
			case 'tx':
				if($data)
					$result = $this->clear_rx($data);
			break;
			case 'mac':
				if($dataApi)
					$result = ClearDataMac($dataApi);
			break;
			case 'model':
				$result = $data;
			break;
			case 'vendor':
				$result = $data;
			break;			
			case 'device':
				$result = $data;
			break;			
			case 'pvid':
				$result = $data;
			break;			
			case 'eth':
				$result = ($data=='up'?'up':'down');				
			break;			
		}		
		return (isset($result) ? $result : null);
	}
	public function clearResult($value){
		$value = str_replace('"','',$value);
		$value = trim($value);
		$value = str_replace('/','',$value);
		return $value;
	}
	public function savePort($dataPort){
		global $db, $PMonTables;
		if(!empty($dataPort['port'])){
			foreach($dataPort['port'] as $value){
				self::savePortSwitch($value);
			}
		}
		if(!empty($dataPort['pon'])){
			foreach($dataPort['pon'] as $value){
				self::savePonSwitch($value);
			}
			$db->SQLupdate($PMonTables['switch'],['updates_port' => $this->now],['id' => $this->id]);
		}
	}
	public function Port(){
		$data = array();
		$OIdPortEpon = $this->deviceoid[$this->id]['onu']['listname']['epon']['oid'];
		$EponListPort = $this->snmp->walk($this->ip,$this->community,$OIdPortEpon,true);
		$EponListPort = str_replace('.'.$OIdPortEpon.'.','',$EponListPort);
		$IndexEponPort = explodeRows($EponListPort);	
		if(is_array($IndexEponPort)){
			$listPort = array();
			foreach ($IndexEponPort as $idPort => $ValuePort) {				
                $infPort = explode('=', $ValuePort);
				if(!empty($infPort[0]) && !empty($infPort[0])){
					$dataIndexPort = $this->clearResult($infPort[1]);
					if(!preg_match('/EPON0(\d+):(\d+)/i',$dataIndexPort) AND !preg_match('/VLAN/i',$dataIndexPort) AND !preg_match('/Null/i',$dataIndexPort)){
						$listPort[$idPort] = array('id' =>trim($infPort[0]),'typeport' => getTypePort($dataIndexPort),'name' => getNameBdcomport($dataIndexPort));
					}
				}				
			}
			$data['port'] = $listPort;
		}
		if(is_array($data['port'])){
			foreach($data['port'] as $idPonport => $valuePon){
				if(preg_match('/pon/i',$valuePon['name'])) {
					preg_match('/EPON 0\/(\d+)/',$valuePon['name'],$mat);
					$listPon[$idPonport]['name'] = 'EPON 0/'.$mat[1].'';
					$listPon[$idPonport]['sort'] = $mat[1];
					$listPon[$idPonport]['sfpid'] = $valuePon['id'];
					$listPon[$idPonport]['cardcount'] = 64;
				}
			}
			if(is_array($listPon)){
				usort($listPon, function($arr, $brr){
					return ($arr['sort'] - $brr['sort']);	
				});
				$data['pon'] = $listPon;
			}
		}
		return (is_array($data) ? $data : null);
	}
    protected function savePonSwitch($dataPort) {
		global $db, $PMonTables;
		$row = $db->Multi($PMonTables['switchpon'],'*',['oltid' => $this->id,'sfpid' => $dataPort['sfpid']]);
		if(!count($row))
			$db->SQLinsert($PMonTables['switchpon'],['support' => $dataPort['cardcount'],'sort' => $dataPort['sort'],'oltid' => $this->id,'pon' => $dataPort['name'],'sfpid' => $dataPort['sfpid'],'added' => $this->now]);
		if(!empty($dataPort['sfpid']) && !empty($dataPort['sort'])){		
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
			if(!empty($row['id'])){
				
			}else{
				$db->SQLinsert($PMonTables['switchport'],['deviceid' => $this->id,'llid' => $data['id'],'nameport' => $data['name'],'typeport' => $data['typeport'],'operstatus' => 'none','added' => $this->now]);
			}
		}
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
	public function tempSaveSignalSaveOnuEpon($dataOnu){	
		global $db, $PMonTables, $config;
		$savehistor = $savehistor ?? null;
		$onu = $db->Fast($PMonTables['onus'],'status,rx,idonu',['olt' => $this->id,'keyonu' => $dataOnu['keyonu']]);
		if(!empty($onu['idonu'])){
			$rx = $rx ?? null;
			if(!empty($dataOnu['rx'])){
				$rx = $this->clear_rx($dataOnu['rx']);
				if($rx){
					$db->SQLupdate($PMonTables['onus'],['rx' => $rx,'rating' => 1],['idonu' => $onu['idonu']]);
				}
			}
			if($config['logsignal']=='on'){
				if($rx){
					$savehistor = SignalMonitor($onu['status'], $rx, $onu['rx'], $onu['idonu']);
				}
			}else{
				$savehistor = true;
			}
			if(!empty($config['onugraph']) && $config['onugraph']=='on' && $savehistor && $rx){
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
			$value = $value/10;
			$value = sprintf('%.2f',$value);
			$value = str_replace('0.00',0,$value);
		}
		return $value;
	}
	public function tempSaveOnuEpon($dataOnu){	
		global $db, $config, $lang, $PMonTables;
		if(!empty($dataOnu['inface'])){
			$inface = str_replace('epon','', strtolower(str_replace(' ', '',trim($dataOnu['inface']))));
			preg_match('/0\/(\d+):(\d+)/i',$inface,$dataMatch);
			$indexPortOlt = $dataMatch[1];
		}
		$dataOnu['status'] = (!empty($dataOnu['status']) ? $this->statusBdcom($dataOnu['status']): 2);
		if(!empty($dataOnu['keyonu'])){
			$arr = $db->Fast($PMonTables['onus'],'*',['keyonu' =>$dataOnu['keyonu'],'olt' => $dataOnu['id']]); 
			if(!empty($arr['idonu'])){
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
					$SQLset['dist'] = $dataOnu['dist'];
				if(!empty($dataOnu['mac']))
					$SQLset['mac'] = $dataOnu['mac'];
				$SQLset['inface'] = $inface;					
				if(!empty($dataOnu['rx']))
					$SQLset['rx'] = $dataOnu['rx'];	
				if($indexPortOlt){
					$SQLset['portolt'] = $indexPortOlt;
					$SQLset['zte_idport'] = $indexPortOlt;
				}
				$db->SQLupdate($PMonTables['onus'],$SQLset,['idonu' => $arr['idonu']]);
			}else{			
				$SQLinsert['olt'] = $dataOnu['id'];
				$SQLinsert['added'] = $this->now;
				$SQLinsert[($dataOnu['status']==1?'online':'offline')] = $this->now;
				$SQLinsert['updates'] = $this->now;
				$SQLinsert['rating'] = 1;
				$SQLinsert['keyonu'] = $dataOnu['keyonu'];
				$SQLinsert['status'] = $dataOnu['status'];
				if(!empty($dataOnu['mac']))
					$SQLinsert['mac'] = $dataOnu['mac'];				
				if(!empty($dataOnu['rx']))
					$SQLinsert['rx'] = $dataOnu['rx'];
				$SQLinsert['inface'] = $inface;
				$SQLinsert['portolt'] = $indexPortOlt;
				$SQLinsert['zte_idport'] = $indexPortOlt;
				$SQLinsert['cron'] = 1;
				$SQLinsert['type'] = $dataOnu['pon'];
				$SQLinsert['dist'] = (!empty($dataOnu['dist']) ? $dataOnu['dist'] : 0);
				$db->SQLinsert($PMonTables['onus'],$SQLinsert);
			}
		}
	}
	public function StatisticOLT(){	
		global $db, $PMonTables;
		$getPort = $db->Multi($PMonTables['switchpon'],'*',['oltid' => $this->id]);
		if(count($getPort)){
			foreach($getPort as $port){
				$getONUstatusPort = $db->Multi($PMonTables['onus'],'idonu,status',['olt' => $this->id,'portolt'=>$port['sfpid']]);
				$getONUstatusPortOn = $db->Multi($PMonTables['onus'],'idonu,status',['status' => 1,'olt' => $this->id,'portolt'=>$port['sfpid']]);
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
}
?>