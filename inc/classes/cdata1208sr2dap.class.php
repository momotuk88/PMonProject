<?php
if(!defined('PONMONITOR')){
	die('Hacking attempt!');
}
class CDATA_1208sr2dap { 
    private $indexdevice = array();
    private $now = null;
    private $primary = 'dist,name,status,reason';
    private $configapionugpon = 'dist,name,mac,status,rx,tx,eth,model,vendor,reason';
    private $filter = true;
    private $filters = ['/"/','/Hex-/i','/OID: /i','/STRING: /i','/Gauge32: /','/INTEGER: /i','/Counter32: /i','/SNMPv2-SMI::enterprises\./i','/iso\.3\.6\.1\.4\.1\./i'];
	/*
	Що підтрмує даний свіч, модель
	*/
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
	/*
		community - масив oid
		deviceoid - id олта
		ip - іп комутатора
	*/
    public function __construct($id = '', $AllConfig) {
		$this->initsnmp();
		$this->now = date('Y-m-d H:i:s');
		$this->filter = true;
		$this->id = $id;
		$this->ip = $AllConfig->switchIp[$id];
		$this->community = $AllConfig->switchCommunity[$id];
		$this->deviceoid = $AllConfig->SwitchOid;
	}  
	/*
	Декодуємо ід ону в інтерфейс
	*/
	public function portnametext1216($data){
		$port = floor($data/256)%256-12;
		$numonu = ($data%64);
		return '0/'.$port.':'.$numonu;
	}
	/*
	Декодуємо mac onu
	*/
	public function onumac($data) {
		if($data){
			$data = preg_replace('~^.*?( = )~i','',$data);
			if (strlen($data) === 18){
				$data = strtolower($data);
				$data = str_replace(' ','',$data);
				return preg_replace('/(.{2})/','\1:',$data,5);
			}else{
				$maconu = bin2hex($data);
				return preg_replace('/(.{2})/', '\1:', $maconu, 5);
			}
		}else return '';
	}
	/*
	Отримуємо всі onu по snmp з комутатора
	*/
	public function Load(){
		global $db, $PMonTables;		
		$listinface = $this->deviceoid[$this->id]['onu']['listname']['epon']['oid'];
		if(!empty($this->deviceoid[$this->id]['onu']['listname']['epon']['oid']))		
			$listonu = $this->snmp->walk($this->ip,$this->community,$listinface,true);
		if($listonu){
			$this->indexdevice = $listonu;
			$indexOnu = explodeRows(str_replace('.'.$listinface.'.','',$listonu));
			if(is_array($indexOnu)){
				foreach($indexOnu as $io => $eachsig) {
					$line = explode('=', $eachsig);
					if(isset($line[0]) && isset($line[1])) {
						$TempName['name'] = $line[1];
						$result[$io] = array('do' => 'onu','id'=>$this->id,'mac'=>ClearDataMac($this->prepareResult($TempName)['name']),'pon'=>'epon','inface'=>self::portnametext1216(trim($line[0])),'types'=>$this->primary,'keyonu'=> trim($line[0]));
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
		return (is_array($result) ? $result : null);
	}
	/*
	Кофіг для ONU щоб отримати по API
	*/
	public function ConfigApiOnu($data){
		$array = array('do' => 'onu','types' => $this->configapionugpon,'pon' => mb_strtolower($data['type']),'keyonu' => $data['keyonu'],'id' => $this->id);
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
			if(!empty($getData['mac'])) 
				$SQLset['mac'] = $getData['mac'];			
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
			if(is_array($SQLset))
				$db->SQLupdate($PMonTables['onus'],$SQLset,['idonu' => $ont['idonu']]);
			$result['type'] = $ont['type'];
			$result['status'] = $getData['status'];
			$result['wan'] = $getData['eth'];			
			if(!empty($getData['name']))	
				$result['name'] = $getData['name'];			
			if(!empty($getData['model']))	
				$result['model'] = $getData['model'];
			if(!empty($getData['reason']))	
				$result['reason'] = $getData['reason'];			
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
			return $result;
		}
	}	
	public function preparedataCDATA($dataApi,$type){
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
			case 'mac':
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
			case 'reason':
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
				self::savePonPortSwitch($value);
			}
			$db->SQLupdate($PMonTables['switch'],['updates_port' => $this->now],['id' => $this->id]);
		}
	}
	/*
	Зберігаємо ПОН порти
	*/
	protected function savePonPortSwitch($data){
		global $db, $PMonTables;
		if(!empty($data['llid'])){	
			$row = $db->Fast($PMonTables['switchport'],'*',['deviceid' => $this->id, 'llid' => $data['llid']]);
			if(!$row['id'])
				$db->SQLinsert($PMonTables['switchport'],['deviceid' => $this->id,'llid' => $data['llid'],'nameport' => $data['name'],'descrport' => $data['descr'],'typeport' => $data['typeport'],'operstatus' => 'none','added' => $this->now]);
		}
	}
	/*
	Отримуємо всю інформацію по портах комутатора і формуємо масив
	*/
	public function Port(){
		$data = array();		
		$OIdPortEth = $this->deviceoid[$this->id]['global']['listporteth']['port']['oid'];
		$OIdPortPon = $this->deviceoid[$this->id]['global']['listportpon']['port']['oid'];
		if(isset($OIdPortEth) && isset($OIdPortPon)){
			$ListPortTempEth = $this->snmp->walk($this->ip,$this->community,$OIdPortEth,true);
			$ListPortTempPon = $this->snmp->walk($this->ip,$this->community,$OIdPortPon,true);
		}
		if(isset($ListPortTempEth)){
			$EponListPort = str_replace('.'.$OIdPortEth.'.','',$ListPortTempEth);
			$IndexGePort = explodeRows($EponListPort);	
			if(is_array($IndexGePort)){
				$listPortge = array();
				$iport = 1;
				foreach ($IndexGePort as $idPort => $ValuePort) {				
					$infPort = explode('=', $ValuePort);
					if(!empty($infPort[0]) && !empty($infPort[0])){
						$listPortge[$idPort] = array('sort' =>$iport,'llid' =>trim($infPort[0]),'typeport' => 'sfp','name' => $this->clearData($infPort[1]));
						$iport++;
					}				
				}
				$data['port'] = $listPortge;
			}
		}
		if(isset($ListPortTempPon)){
			$EponListPort = str_replace('.'.$OIdPortPon.'.','',$ListPortTempPon);
			$IndexEponPort = explodeRows($EponListPort);	
			if(is_array($IndexEponPort)){
				$listPort = array();
				$ipon = 1;
				foreach ($IndexEponPort as $idPort => $ValuePort) {				
					$infPort = explode('=', $ValuePort);
					if(!empty($infPort[0]) && !empty($infPort[0])){
						$dataIndexPort = $this->clearResult($infPort[1]);
						$listPort[$idPort] = array('cardcount' =>64,'sort' =>$ipon,'idportolt' =>$ipon,'sfpid' =>trim($infPort[0]),'llid' =>trim($infPort[0]),'typeport' => 'epon','name' => 'EPON 0/'.$ipon,'descr' => $this->clearData($infPort[1]));
						$ipon++;
					}				
				}
				$data['pon'] = $listPort;
			}
		}
		return $data;
	}
	/*
	Зберігаємо інформацію по портах
	*/
    protected function savePonSwitch($dataPort) {
		global $db, $PMonTables;
		$row = $db->Multi($PMonTables['switchpon'],'*',['oltid' => $this->id,'idportolt' => $dataPort['idportolt']]);
		if(!count($row)){
			$db->SQLinsert($PMonTables['switchpon'],['support' => $dataPort['cardcount'],'sort' => $dataPort['sort'],'oltid' => $this->id,'pon' => $dataPort['name'],'sfpid' => $dataPort['sfpid'],'idportolt' => $dataPort['idportolt'],'added' => $this->now]);
		}
		if(!empty($dataPort['sfpid'])){		
			$db->SQLupdate($PMonTables['onus'],['portolt' => $dataPort['sfpid']],['olt' => $this->id,'zte_idport' => $dataPort['idportolt']]);
		}
		$allonu = $db->Multi($PMonTables['onus'],'*',['olt' => $this->id,'zte_idport' => $dataPort['idportolt']]);
		if(!empty($dataPort['sfpid']))
			$db->SQLupdate($PMonTables['switchpon'],['count' =>count($allonu)],['idportolt' =>$dataPort['idportolt'],'sfpid' =>$dataPort['sfpid'],'oltid' => $this->id]);
	}
	public function tempSaveSignalSaveOnuEpon($dataOnu){	
		global $db, $PMonTables, $config;
		$onu = $db->Fast($PMonTables['onus'],'status,rx,idonu',['olt' => $this->id,'keyonu' => $dataOnu['keyonu']]);
		if(!empty($onu['idonu'])){
			if(!empty($dataOnu['rx'])){
				$rx = $this->clear_rx($dataOnu['rx']);
			}
			$savehistor = false;
			if($rx){
				$db->SQLupdate($PMonTables['onus'],['rx' => $rx,'rating' => 1],['idonu' => $onu['idonu']]);
				$savehistor = SignalMonitor($onu['status'], $rx, $onu['rx'], $onu['idonu']);
			}
			if(!empty($config['onugraph']) && $config['onugraph']=='on' && $savehistor){
				$db->SQLInsert($PMonTables['historyrx'],['device' => $this->id,'onu' => $onu['idonu'],'signal' => $rx,'datetime' => $this->now]);
			}
		}
	}
    protected function savePortSwitch($data) {
		global $db, $PMonTables;
		if(!empty($data['llid'])){	
			$row = $db->Fast($PMonTables['switchport'],'*',['deviceid' => $this->id, 'llid' => $data['llid']]);
			if(!$row['id'])
				$db->SQLinsert($PMonTables['switchport'],['deviceid' => $this->id,'llid' => $data['llid'],'nameport' => $data['name'],'typeport' => $data['typeport'],'operstatus' => 'none','added' => $this->now]);
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
			$value = sprintf('%.2f',$value);
			$value = str_replace('0.00',0,$value);
		}
		return $value;
	}
	public function tempUpdateSignalCheck(){	
		global $db, $PMonTables;
		$db->SQLupdate($PMonTables['onus'],['rating' => 7],['olt' => $this->id,'status' => 2]);
	}
	public function tempSaveOnuEpon($dataOnu){	
		global $db, $config, $lang, $PMonTables;
		if(!empty($dataOnu['inface'])){
			preg_match('/0\/(\d+):(\d+)/i',$dataOnu['inface'],$dataMatch);
			$indexPortOlt = $dataMatch[1];
		}
		$dataOnu['status'] = (!empty($dataOnu['status']) ? $dataOnu['status'] : (!empty($dataOnu['dist']) ? 1 : 2));
		if(!empty($dataOnu['keyonu']) && $indexPortOlt){
			$arr = $db->Fast($PMonTables['onus'],'*',['zte_idport' =>$indexPortOlt,'keyonu' =>$dataOnu['keyonu'],'olt' => $dataOnu['id']]); 
			if(!empty($arr['idonu'])){
				if($dataOnu['status']==1 && $arr['status']==2){
					$SQLset['rating'] = 7;
				}else{
					$SQLset['rating'] = 1;
				}				
				if($dataOnu['status']==1 && $arr['status']==2){
					$SQLset['online'] = $this->now;
				}elseif($dataOnu['status']==2 && $arr['status']==1){
					$SQLset['offline'] = $this->now;
				}else{
					
				}
				$SQLset['updates'] = $this->now;
				$SQLset['cron'] = 1;
				$SQLset['status'] = $dataOnu['status'];
				$SQLset['type'] = $dataOnu['pon'];
				if(!empty($dataOnu['dist']))
					$SQLset['dist'] = $dataOnu['dist'];				
				if(!empty($dataOnu['name']))
					$SQLset['name'] = $dataOnu['name'];
				if(!empty($dataOnu['mac']))
					$SQLset['mac'] = $dataOnu['mac'];
				if(!empty($dataOnu['inface']))
					$SQLset['inface'] = $dataOnu['inface'];				
				if(!empty($dataOnu['reason']))
					$SQLset['reason'] = $dataOnu['reason'];	
				if($indexPortOlt){
					$SQLset['portolt'] = $indexPortOlt;
					$SQLset['zte_idport'] = $indexPortOlt;
				}
				$db->SQLupdate($PMonTables['onus'],$SQLset,['idonu' => $arr['idonu']]);
			}else{				
				$SQLinsert = array('olt' => $dataOnu['id'],'updates' => $this->now, 'added' => $this->now, ($dataOnu['status']==1?'online':'offline') => $this->now, 'rating' => 1,'keyonu' => $dataOnu['keyonu'],'status' => $dataOnu['status'],'zte_idport' => $indexPortOlt,'mac' => $dataOnu['mac'],'inface' => $dataOnu['inface'],'dist' => (!empty($dataOnu['dist']) ? $dataOnu['dist'] : 0),'name' => (!empty($dataOnu['name']) ? $dataOnu['name'] : ''),'reason' => (!empty($dataOnu['reason']) ? $dataOnu['reason'] : ''),'type' => $dataOnu['pon'],'cron' => 1,'portolt' => $indexPortOlt);
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
		$sqlList = $db->Multi($PMonTables['onus'],'keyonu,type',['olt' => $this->id, 'status' => 1]);
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