<?php
if(!defined('PONMONITOR')){
	die('Hacking attempt!');
}
class CDATA_1616sn { 
    private $indexdevice = array();
    private $now = null;
    private $primary = 'dist,status,name';
    private $configapionugpon = 'dist,status,name,rx,tx,model,vendor,eth';
    private $filter = true;
    private $filters = [
		'/"/','/Hex-/i','/OID: /i','/STRING: /i',
		'/Gauge32: /','/INTEGER: /i','/Counter32: /i',
		'/SNMPv2-SMI::enterprises\./i','/iso\.3\.6\.1\.4\.1\./i'
	];
	/*
	Що підтрмує даний свіч, модель
	*/
	public function Support($check){
		switch($check){
			case 'port': 
				return true;
			break;			
			case 'onu': 
				return true;
			break;				
			case 'saveonu': 
				return true;
			break;				
			case 'api': 
				return true;
			break;				
		}	
	}
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
	public function inface1616($data){
		$port = floor($data/256)%256-6;
		$numonu = ($data%128);
		return '0/'.$port.':'.$numonu;
	}
	/*
	Отримуємо всі onu по snmp з комутатора
	*/
	public function Load(){
		global $db, $PMonTables;		
		$listinface = $this->deviceoid[$this->id]['onu']['listsn']['gpon']['oid'];
		if(!empty($this->deviceoid[$this->id]['onu']['listsn']['gpon']['oid']))		
			$listonu = $this->snmp->walk($this->ip,$this->community,$listinface,true);
		if($listonu){
			$this->indexdevice = $listonu;
			$indexOnu = explodeRows(str_replace('.'.$listinface.'.','',$listonu));
			if(is_array($indexOnu)){
				foreach($indexOnu as $io => $eachsig) {
					$line = explode('=', $eachsig);
					if(isset($line[0]) && isset($line[1])) {
						$result[$io] = array('inface'=>$this->inface1616(trim($line[0])),'sn'=>SnHuawei($eachsig),'do' => 'onu','id'=>$this->id,'pon'=>'gpon','types'=>$this->primary,'keyonu'=> trim($line[0]));
					}	
				}
				if(is_array($result)){
					$db->SQLupdate($PMonTables['onus'],['cron' => 2],['olt' => $this->id]);
				}else{
					$db->SQLinsert($PMonTables['swlog'],['deviceid' =>$this->id,'types' =>'switch','message' =>'emptyarraydata','added' =>$this->now]);
				}
			}
		}else{
			$db->SQLinsert($PMonTables['swlog'],['deviceid' =>$this->id,'types' =>'switch','message' =>'emptysnmpwalk','added' =>$this->now]);
		}
			if(is_array($result)){
				checkerONU($result,$this->id);
			}
			return (is_array($result) ? $result : null);
	}
	/*
	Кофіг для ONU щоб отримати по API
	*/
	public function ConfigApiOnu($data){
		$array = array('do' => 'onu','types' => $this->configapionugpon,'pon' => mb_strtolower($data['type']),'keyonu' => $data['keyonu'],'keyport' => $data['zte_idport'],'id' => $this->id);
		return $array;
	}
	/*
	Перебираємо всю інформацію отриману по ONU
	*/
	public function Onu($dataPort,$dataOnu){
		if(is_array($dataPort)){
			foreach($dataPort as $type => $value) {
				$res[$type] = $this->preparedataCDATA($value,$type);
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
		}else{
			$result = false;
		}
		return $result;
	}
	/*
	Перебираємо всю інформацію отриману по ONU, деяку зберігаємо в базі і виводимо в результаті
	*/
	public function updateonu($ont,$getData){
		global $db, $config, $PMonTables;
		if(is_array($getData)){
			if(!empty($getData['tx'])){
				$SQLset['tx'] = $getData['tx'];
				$result['tx'] = $getData['tx'];
			}
			if(!empty($getData['rx'])){
				$SQLset['rx'] = $getData['rx'];
				$result['rx'] = $getData['rx'];
			}
			if(!empty($getData['name'])) 
				$SQLset['name'] = $getData['name'];			
			if(!empty($getData['sn'])) 
				$SQLset['sn'] = $getData['sn'];			
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
			if(is_array($SQLset))
				$db->SQLupdate($PMonTables['onus'],$SQLset,['idonu' => $ont['idonu']]);
			$result['type'] = $ont['type'];
			$result['status'] = $getData['status'];
			$result['wan'] = $getData['eth'];			
			if(!empty($getData['name']))	
				$result['name'] = $getData['name'];				
			if(!empty($getData['model']))	
				$result['model'] = $getData['model'];				
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
			if(!empty($getData['eth'])) 
				$result['eth'] = $getData['eth'];
			return $result;
		}
	}	
	public function preparedataCDATA($dataApi,$type){
		$data = $this->clearData($dataApi);
		switch($type){
			case 'status':
				$result = (isset($data) && $data==1 ? 1 : 2);
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
			case 'name':
				$result = $data;
			break;				
			case 'model':
				$result = $data;
			break;			
			case 'vendor':
				$result = $data;
			break;				
			case 'eth':
				$result = ($data==1?'up':'down');				
			break;			
		}		
		return $result;
	}
	public function clearResult($value){
		return str_replace('/','',trim(str_replace('"','',$value)));
	}
	/*
	Зберігаємо всю інформацію по портах комутатора
	*/
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
    protected function savePortSwitch($data) {
		global $db, $PMonTables;
		if(!empty($data['llid'])){	
			$row = $db->Fast($PMonTables['switchport'],'*',['deviceid' => $this->id, 'llid' => $data['llid']]);
			if(!empty($row['id'])){
				
			}else{
				$db->SQLinsert($PMonTables['switchport'],['deviceid' => $this->id,'llid' => $data['llid'],'nameport' => $data['name'],'typeport' => $data['typeport'],'operstatus' => 'none','added' => $this->now]);
			}
		}
	} 
	/*
	Отримуємо всю інформацію по портах комутатора і формуємо масив
	*/
	public function Port(){
		$OIdPortPon = $this->deviceoid[$this->id]['global']['listport']['port']['oid'];
		if(isset($OIdPortPon)){
			$ListPortTempPon = $this->snmp->walk($this->ip,$this->community,$OIdPortPon,true);
		}
		if(isset($ListPortTempPon)){
			$EponListPort = str_replace('.'.$OIdPortPon.'.','',$ListPortTempPon);
			$IndexEponPort = explodeRows($EponListPort);	
			if(is_array($IndexEponPort)){
				$listPort = array();
				foreach ($IndexEponPort as $idPort => $ValuePort) {	
					$infPort = explode('=', $ValuePort);
					if(!empty($infPort[1])){
						$getinf = clearData1108($infPort[1]);
						if(!empty($infPort[0]) && !empty($infPort[1]) && !preg_match('/:/i',$getinf)) {
							$nameport = getNameCdataport($getinf);
							$listPort[$idPort] = array('llid' =>trim($infPort[0]),'id' =>trim($infPort[0]),'typeport' => getTypePortHuawei($nameport),'name' => $nameport);
						}	
					}					
				}
				$data['port'] = $listPort;
			}
		}
		if(is_array($data['port'])){
			foreach($data['port'] as $idPonport => $valuePon){
				if(preg_match('/pon/i',$valuePon['name'])) {
					preg_match('/0\/0\/(\d+)/',$valuePon['name'],$mat);
					$listPon[$idPonport]['name'] = $valuePon['name'];
					$listPon[$idPonport]['sort'] = $mat[1];
					$listPon[$idPonport]['typeport'] = 'gpon';
					$listPon[$idPonport]['sfpid'] = $valuePon['id'];
					$listPon[$idPonport]['llid'] = $valuePon['id'];
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
		print_R($data);
		return $data;
	}
    protected function savePonSwitch($dataPort) {
		global $db, $PMonTables;
		$row = $db->Multi($PMonTables['switchpon'],'*',['oltid' => $this->id,'sfpid' => $dataPort['sfpid']]);
		if(!count($row)){
			$db->SQLinsert($PMonTables['switchpon'],['idportolt' => $dataPort['sfpid'],'support' => $dataPort['cardcount'],'sort' => $dataPort['sort'],'oltid' => $this->id,'pon' => $dataPort['name'],'sfpid' => $dataPort['sfpid'],'added' => $this->now]);
		}
		if(!empty($dataPort['sfpid']) && !empty($dataPort['sort'])){		
			$db->SQLupdate($PMonTables['onus'],['portolt' => $dataPort['sfpid']],['olt' => $this->id,'zte_idport' => $dataPort['sort']]);
		}
		$allonu = $db->Multi($PMonTables['onus'],'*',['olt' => $this->id,'portolt' => $dataPort['sfpid']]);
		if(!empty($dataPort['sfpid']))
			$db->SQLupdate($PMonTables['switchpon'],['count' =>count($allonu)],['sfpid' =>$dataPort['sfpid'],'oltid' => $this->id]);
	}
	public function tempSaveSignalSaveOnuGpon($dataOnu){	
		global $db, $PMonTables, $config;
		$savehistor = false;
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
	public function check_signal($rx){
		if ($rx == 0 OR !$rx OR $rx == NULL) {
			return 0;
		} else {
			return sprintf("%.2f",($rx/100));  
		}
	}
	public function clear_rx($value){
		if(preg_match('/6553/i',$value)) {
			return 0; 
		}else{
			return self::check_signal($value);
		}
	}
	public function tempUpdateSignalCheck(){	
		global $db, $PMonTables;

	}
	public function tempSaveOnuGpon($dataOnu = array()){	
		global $db, $config, $lang, $PMonTables;
		$statusONU = (!empty($dataOnu['status']) && $dataOnu['status']==1 ? 1 : 2);
		if(!empty($dataOnu['inface'])){
			preg_match('/0\/(\d+):(\d+)/i',$dataOnu['inface'],$dataMatch);
			$indexPortOlt = $dataMatch[1];
		}
		if(!empty($dataOnu['keyonu'])){
			$arr = $db->Fast($PMonTables['onus'],'*',['keyonu' =>$dataOnu['keyonu'],'olt' => $dataOnu['id']]); 
			if(!empty($arr['idonu'])){
				if($statusONU==1 && $arr['status']==2){
					$SQLset['rating'] = 7;
				}else{
					$SQLset['rating'] = 1;
				}				
				if($statusONU==1 && $arr['status']==2){
					$SQLset['online'] = $this->now;
				}elseif($statusONU==2 && $arr['status']==1){
					$SQLset['offline'] = $this->now;
				}else{
					
				}
				$SQLset['updates'] = $this->now;
				$SQLset['cron'] = 1;
				$SQLset['status'] = $statusONU;
				$SQLset['type'] = $dataOnu['pon'];
				if(!empty($dataOnu['dist']))
					$SQLset['dist'] = $dataOnu['dist'];				
				if(!empty($dataOnu['name']))
					$SQLset['name'] = $dataOnu['name'];
				if(!empty($dataOnu['sn']))
					$SQLset['sn'] = $dataOnu['sn'];
				if(!empty($dataOnu['inface']))
					$SQLset['inface'] = $dataOnu['inface'];				
				if(!empty($dataOnu['reason']))
					$SQLset['reason'] = $dataOnu['reason'];	
				if(!empty($dataOnu['rx']))
					$SQLset['rx'] = $dataOnu['rx'];	
				if($indexPortOlt){
					$SQLset['portolt'] = $indexPortOlt;
					$SQLset['zte_idport'] = $indexPortOlt;
				}
				$db->SQLupdate($PMonTables['onus'],$SQLset,['idonu' => $arr['idonu']]);
			}else{				
				$SQLinsert = array('olt' => $dataOnu['id'],'updates' => $this->now,'added' => $this->now,($statusONU==1?'online':'offline') => $this->now,'rating' => 1,'keyonu' => $dataOnu['keyonu'],'status' => $statusONU,'sn' => $dataOnu['sn'],'inface' => $dataOnu['inface'],'dist' => (!empty($dataOnu['dist']) ? $dataOnu['dist'] : 0),'name' => (!empty($dataOnu['name']) ? $dataOnu['name'] : ''),'reason' => (!empty($dataOnu['reason']) ? $dataOnu['reason'] : ''),'type' => $dataOnu['pon'],'cron' => 1,'zte_idport' => $indexPortOlt,'portolt' => $indexPortOlt);
				$db->SQLinsert($PMonTables['onus'],$SQLinsert);
			}
		}
	}
	public function Status($status){
		if($status==1){
			return 1;
		}else{
			return 2;
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
		$sqlList = $db->Multi($PMonTables['onus'],'keyonu,portolt,type',['olt' => $this->id, 'status' => 1]);
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