<?php
if(!defined('PONMONITOR')){
	die('Hacking attempt!');
}
class HUAWEI_5608t { 
    private $indexdevice = array();
    private $now = null;
    private $primarygpon = 'status,dist,name,reason';
    private $primaryepon = 'status,dist,name,reason';
    private $configapionuepon = 'dist,status,reason,name,temp,eth,tx,rx';
    private $configapionugpon = 'onuerror,uservlan,countmacport,rx,tx,eth,bias,linepro,dist,model,service,name,status,reason';
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
		}	
	}
	public function huawei_ifindex($ifIndex){
		$return['olt'] = ($ifIndex & 16252928) >> 19;
		$return['slot'] = ($ifIndex & 253952) >> 13;
		$return['port'] = ($ifIndex & 3840) >> 8;
		return $return['olt'].'/'.$return['slot'].'/'.$return['port'];
	}	
	public function array_huawei_ifindex($ifIndex){
		$return['olt'] = ($ifIndex & 16252928) >> 19;
		$return['slot'] = ($ifIndex & 253952) >> 13;
		$return['port'] = ($ifIndex & 3840) >> 8;
		return $return;
	}
	public function Load(){
		global $db, $PMonTables;
		$resultGpon = $resultGpon ?? null;
		$resultEpon = $resultEpon ?? null;
		if (!empty($this->deviceoid[$this->id]['onu']['listsn']['gpon']['oid'])) {
			$listinfaceGpon = $this->deviceoid[$this->id]['onu']['listsn']['gpon']['oid'];
			$listGpononu = $this->snmp->walk($this->ip, $this->community, $listinfaceGpon, false);
			if ($listGpononu) {
				$indexGponOnu = explodeRows(str_replace('.'.$listinfaceGpon.'.','',$listGpononu));
				if ($indexGponOnu) {
					foreach ($indexGponOnu as $io => $eachsig) {
						$line = explode('=', $eachsig);
						if (isset($line[0], $line[1])) {
							preg_match('/(\d+)\.(\d+)/', $line[0], $mat);
							if (is_numeric($mat[1]) && is_numeric($mat[2])) {
								$resultGpon[$io] = [
									'inface' => $this->huawei_ifindex(trim($mat[1])) . ':' . trim($mat[2]),
									'sn' => SnHuawei($line[1]),
									'do' => 'onu',
									'id' => $this->id,
									'pon' => 'gpon',
									'types' => $this->primarygpon,
									'keyonu' => trim($mat[2]),
									'keyport' => trim($mat[1])
								];
							}
						}
					}
					if (count($resultGpon) === 0) {
						$db->SQLinsert($PMonTables['swlog'], [
							'deviceid' => $this->id,
							'types' => 'switch',
							'message' => 'empty gpon data',
							'added' => $this->now
						]);
					}
				}
			}
		}
		if(!empty($this->deviceoid[$this->id]['onu']['listmac']['epon']['oid'])){
			$listinfaceEpon = $this->deviceoid[$this->id]['onu']['listmac']['epon']['oid'];
			$listEpononu = $this->snmp->walk($this->ip,$this->community,$listinfaceEpon,false);	
			if($listEpononu){
				$indexEponOnu = explodeRows(str_replace('.'.$listinfaceEpon.'.','',$listEpononu));
				if($indexEponOnu){
					foreach($indexEponOnu as $ios => $eachsigs) {
						$lines = explode('=', $eachsigs);
						if(isset($lines[0]) && isset($lines[1])){
							preg_match('/(\d+).(\d+)/',$lines[0],$mats);
							if(is_numeric($mat[1]) && is_numeric($mat[2]))
								$resultEpon[$ios] = array('inface'=>$this->huawei_ifindex(trim($mats[1])).':'.trim($mats[2]),'mac'=>MacHuawei($lines[1]),'do' => 'onu','id'=>$this->id,'pon'=>'epon','types'=>$this->primaryepon,'keyonu'=> trim($mats[2]),'keyport'=> trim($mats[1]));
						}
					}
					if (count($resultEpon) === 0) {
						$db->SQLinsert($PMonTables['swlog'],['deviceid' =>$this->id,'types' =>'switch','message' =>'empty epon data','added' =>$this->now]);
					}
				}
			}
		}
		if (empty($resultGpon) && empty($resultEpon)) {
			$result = null;
		} else {
			$result = (empty($resultEpon)) ? $resultGpon : ((empty($resultGpon)) ? $resultEpon : array_merge($resultGpon, $resultEpon));
		}
		if(is_array($result)){
			$db->SQLupdate($PMonTables['onus'],['cron' => 2],['olt' => $this->id]);
			checkerONU($result,$this->id);
		}
		return ((count($result) === 0) ? null : $result);
	}
	public function ConfigApiOnu($data){
		if(mb_strtolower($data['type'])=='gpon')
			$cfg = array('do' => 'onu','types' => $this->configapionugpon,'pon' => mb_strtolower($data['type']),'keyport' => $data['zte_idport'],'keyonu' => $data['keyonu'],'id' => $this->id);
		if(mb_strtolower($data['type'])=='epon')
			$cfg = array('do' => 'onu','types' => $this->configapionuepon,'pon' => mb_strtolower($data['type']),'keyport' => $data['zte_idport'],'keyonu' => $data['keyonu'],'id' => $this->id);
		return $cfg;
	}
	public function Onu($dataPort,$dataOnu){
		$res = array();
		if(is_array($dataPort)){
			foreach($dataPort as $type => $value) {
				if(preg_match("/GPON/i",$dataOnu['type']) || preg_match("/gpon/i",$dataOnu['type']))
					$res[$type] = $this->preparedataGpon($value,$type);
				if(preg_match("/EPON/i",$dataOnu['type']) || preg_match("/epon/i",$dataOnu['type']))
					$res[$type] = $this->preparedataEpon($value,$type);
			}
		}
		if(!$dataPort && !$res){
			if(preg_match("/GPON/i",$dataOnu['type']) || preg_match("/gpon/i",$dataOnu['type']))
				$array_separated = explode(',',$this->configapionugpon);
			if(preg_match("/EPON/i",$dataOnu['type']) || preg_match("/epon/i",$dataOnu['type']))
				$array_separated = explode(',',$this->configapionuepon);
			foreach($array_separated as $type){
				$res[$type] = $dataOnu[$type];
			}
		}
		if(is_array($res)){
			$result = $this->updateonu($dataOnu,$res);
		}else{
			$result = false;
		}
		return ((count($result) === 0) ? null : $result);
	}
	public function updateonu($ont,$getData){
		global $db, $lang, $config;
		if(is_array($getData)){
			if(!empty($getData['tx'])){
				$SQLset['tx'] = $getData['tx'];
				$result['tx'] = $getData['tx'];
			}
			if(!empty($getData['rx'])){
				$SQLset['rx'] = $getData['rx'];
				$result['rx'] = $getData['rx'];
			}
			if(!empty($getData['dist'])) 
				$SQLset['dist'] = $getData['dist'];			
			if(!empty($getData['model'])) 
				$SQLset['model'] = $getData['model'];			
			if(!empty($getData['reason'])) 
				$SQLset['reason'] = $getData['reason'];
			if($ont['status']==2 && $getData['status']==1){
				if(!empty($getData['timeaut']))
					$SQLset['online'] = $this->now;
				if($getData['offline'])
					$SQLset['online'] = $getData['offline'];
				$SQLset['status'] = 1;
			}
			if($ont['status']==1 &&  $getData['status']==2){
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
			if(!empty($getData['dist'])) 
				$result['dist'] = $getData['dist'];				
			if(!empty($getData['reason'])) 
				$result['reason'] = $getData['reason'];				
			if(!empty($getData['name'])) 
				$result['name'] = $getData['name'];					
			if(!empty($getData['bias'])) 
				$result['bias'] = $getData['bias'];			
			if(!empty($getData['linepro'])) 
				$result['linepro'] = $getData['linepro'];
			if(!empty($ont['lastrx']))
				$result['lastrx'] = $ont['lastrx'];			
			if(!empty($ont['model']))
				$result['model'] = $ont['model'];
			if(!empty($getData['service'])) 
				$result['service'] = $getData['service'];
			if(!empty($getData['rx'])) 
				$result['rx'] = $getData['rx'];
			if(!empty($getData['tx'])) 
				$result['tx'] = $getData['tx'];			
			if(!empty($getData['countmacport'])) 
				$result['countmacport'] = $getData['countmacport'];			
			if(!empty($getData['uservlan'])) 
				$result['uservlan'] = $getData['uservlan'];			
			if(!empty($getData['onuerror'])) 
				$result['onuerror'] = $getData['onuerror'];			
			if(!empty($getData['olttx'])) 
				$result['olttx'] = $getData['olttx'];			
			if(!empty($getData['oltrx'])) 
				$result['oltrx'] = $getData['oltrx'];			
			if(!empty($getData['temp'])) 
				$result['temp'] = $getData['temp'];
			return (isset($result) ? $result : null);
		}
	}	
	public function preparedataGpon($dataApi,$type){
		$data = $this->clearData($dataApi);
		switch($type){
			case 'status':
				$result = ($data==1 ? 1 : 2);
			break;			
			case 'dist':
				$data = str_replace('-1', 0,$data);
				$result = (int)$data;
			break;
			case 'rx':
				if($data)
					$result = $this->clear_rx($data);
			break;			
			case 'tx':
				if($data)
					$result = $this->clear_rx($data);
			break;
			case 'service':
				$result = $data;
			break;				
			case 'model':
				$result = str_replace('_','',$data);
			break;				
			case 'uservlan':
				$result = $data;
			break;				
			case 'countmacport':				
				$result = $data;
			break;			
			case 'name':
				$result = $data;
			break;			
			case 'onuerror':
				$result = $data;
			break;
			case 'linepro':
				$result = $data;
			break;			
			case 'reason':
				$result = reasonGponHuawei($data);
			break;			
			case 'bias':
				$result = (isset($data) ? ceil($data/1000) : 0);
			break;			
			case 'eth':
				$result = ($data==34 || $data==24 || $data==2 ?'up':'down');				
			break;			
		}		
		return (isset($result) ? $result : null);
	}
	public function preparedataEpon($dataApi,$type){
		$data = $this->clearData($dataApi);
		switch($type){
			case 'status':
				$result = ($data==1 ? 2 : 1);
			break;			
			case 'dist':
				$data = str_replace('-1', 0,$data);
				$result = (int)$data;
			break;
			case 'rx':
				if($data)
					$result = $this->clear_rx($data);
			break;			
			case 'tx':
				if($data)
					$result = $this->clear_rx($data);
			break;			
			case 'olttx':
				if($data)
					$result = $this->clear_rx($data);
			break;			
			case 'oltrx':
				if($data)
					$result = $this->clear_rx($data);
			break;
			case 'name':
				$result = $data;
			break;				
			case 'temp':
				$result = $data;
			break;			
			case 'reason':
				$result = reasonGponHuawei($data);
			break;			
			case 'eth':
				$result = ($data==2?'down':'up');				
			break;			
		}		
		return ($result ? $result : null);
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
		$OIdPortPortGPON = $this->deviceoid[$this->id]['global']['listport']['port']['oid'];
		if(!empty($this->deviceoid[$this->id]['global']['listport']['port']['oid'])){
			$listPortGPON = $this->snmp->walk($this->ip,$this->community,$OIdPortPortGPON,true);
		}
		$listPortGPONs = str_replace('.'.$OIdPortPortGPON.'.','',$listPortGPON);
		$IndexPortGPON = explodeRows($listPortGPONs);	
		if(is_array($IndexPortGPON)){
			$listPorts = array();
			foreach ($IndexPortGPON as $idPort => $ValuePort) {	
			if (preg_match("/PON/i", $ValuePort) || preg_match("/ethernet/i", $ValuePort)) {			
                $infPort = explode('=', $ValuePort);
				if(!empty($infPort[0]) && !empty($infPort[0])){
					$oidstatus = trim(str_replace('keyport',$infPort[0],$this->deviceoid[$this->id]['global']['status']['port']['oid']));
					@$portstatus = $this->snmp->get($this->ip,$this->community,$oidstatus);
					if($portstatus){
						$portstatus = $this->clearData($portstatus);
						$listPorts[$idPort] = array('operstatus' =>$this->portstatus($portstatus),'id' =>trim($infPort[0]),'typeport' => getTypePortHuawei($ValuePort),'name' => getNameHuaweiport($infPort[1]));
					}
				}	
			}				
			}
			$data['port'] = $listPorts;
		}
		if(is_array($data['port'])){
			$i = 1;
			foreach($data['port'] as $idPonport => $valuePon){
				if(preg_match('/pon/i',$valuePon['typeport'])){
					$listPonGpon[$idPonport]['name'] = $valuePon['name'];
					$listPonGpon[$idPonport]['operstatus'] = $valuePon['operstatus'];
					$listPonGpon[$idPonport]['sort'] = $i;
					$listPonGpon[$idPonport]['sfpid'] = $valuePon['id'];
					if(preg_match('/gpon/i',$valuePon['typeport']))
						$listPonGpon[$idPonport]['cardcount'] = 128;
					if(preg_match('/epon/i',$valuePon['typeport']))
						$listPonGpon[$idPonport]['cardcount'] = 64;
					$i++;
				}				
			}
			if(is_array($listPonGpon)){
				usort($listPonGpon, function($arr, $brr){
					return ($arr['sort'] - $brr['sort']);	
				});
				$data['pon'] = $listPonGpon;
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
			$db->SQLupdate($PMonTables['onus'],['portolt' => $dataPort['sfpid']],['olt' => $this->id,'zte_idport' => $dataPort['sfpid']]);
		}
		$allonu = $db->Multi($PMonTables['onus'],'*',['olt' => $this->id,'zte_idport' => $dataPort['sfpid']]);
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
				$db->SQLinsert($PMonTables['switchport'],['deviceid' => $this->id,'llid' => $data['id'],'nameport' => $data['name'],'typeport' => $data['typeport'],'operstatus' => $data['operstatus'],'added' => $this->now]);
			}
		}
	}
	public function tempUpdateSignalCheck(){	
		global $db;

	}
	public function tempSaveSignalSaveOnuGpon($dataOnu){	
		global $db, $PMonTables, $config;
		$savehistor = false;
		$onu = $db->Fast($PMonTables['onus'],'status,rx,idonu',['olt' => $this->id,'zte_idport' => $dataOnu['keyport'],'keyonu' => $dataOnu['keyonu']]);
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
	public function tempSaveSignalSaveOnuEpon($dataOnu){	
		global $db, $PMonTables, $config;
		$savehistor = false;
		$onu = $db->Fast($PMonTables['onus'],'status,rx,idonu',['olt' => $this->id,'zte_idport' => $dataOnu['keyport'],'keyonu' => $dataOnu['keyonu']]);
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
	public function portstatus($value){
		if(preg_match('/1/i',$value) || preg_match('/4/i',$value)){
			return 'up';	
		}else{
			return 'down';	
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
		if(preg_match('/214748/i',$value)) {
			$value = 0; 
		}else{
			$value = str_replace('"', '',$value);
			$value = trim($value);
			$value = str_replace('N/A',0,$value);
			$value = $value/100;
			$value = sprintf('%.2f',$value);
			$value = str_replace('0.00',0,$value);
		}
		return $value;
	}
	public function tempSaveOnuGpon($dataOnu){	
		global $db, $config, $lang, $PMonTables;
		$dataOnu['dist'] = str_replace('-1', 0,$dataOnu['dist']);
		$dataOnu['status'] = (!empty($dataOnu['status']) ? ($dataOnu['status']==1 ? 1 : 2 ) : (!empty($dataOnu['dist']) ? 1 : 2));
		if(is_numeric($dataOnu['keyport'])){
			$onucard = $this->array_huawei_ifindex($dataOnu['keyport']);
			if(is_array($onucard)){
				$SQLset['sw_shelf'] = $onucard['olt'];
				$SQLinsert['sw_shelf'] = $onucard['olt'];
				$SQLset['sw_slot'] = $onucard['slot'];
				$SQLinsert['sw_slot'] = $onucard['slot'];
				$SQLset['portolt'] = $onucard['slot'];
				$SQLinsert['portolt'] = $onucard['slot'];
			}
		}
		if(is_numeric($dataOnu['keyonu'])){
			$arr = $db->Fast($PMonTables['onus'],'*',['zte_idport' =>$dataOnu['keyport'],'keyonu' =>$dataOnu['keyonu'],'olt' => $dataOnu['id']]); 
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
				if(!empty($dataOnu['name']))
					$SQLset['name'] = $dataOnu['name'];
				if(!empty($dataOnu['sn']))
					$SQLset['sn'] = $dataOnu['sn'];
				if(!empty($dataOnu['inface']))
					$SQLset['inface'] = $dataOnu['inface'];				
				if(!empty($dataOnu['reason']))
					$SQLset['reason'] = reasonGponHuawei($dataOnu['reason']);					
				if(!empty($dataOnu['rx']))
					$SQLset['rx'] = $dataOnu['rx'];	
				$SQLset['zte_idport'] = $dataOnu['keyport'];
				$db->SQLupdate($PMonTables['onus'],$SQLset,['idonu' => $arr['idonu']]);
			}else{			
				$SQLinsert['olt'] = $dataOnu['id'];
				$SQLinsert['added'] = $this->now;
				$SQLinsert[($dataOnu['status']==1?'online':'offline')] = $this->now;
				$SQLinsert['updates'] = $this->now;
				$SQLinsert['rating'] = 1;
				$SQLinsert['keyonu'] = $dataOnu['keyonu'];
				$SQLinsert['status'] = $dataOnu['status'];
				if(!empty($dataOnu['sn']))
					$SQLinsert['sn'] = $dataOnu['sn'];					
				if(!empty($dataOnu['name']))
					$SQLinsert['name'] = $dataOnu['name'];				
				if(!empty($dataOnu['reason']))
					$SQLinsert['reason'] = reasonGponHuawei($dataOnu['reason']);				
				if(!empty($dataOnu['rx']))
					$SQLinsert['rx'] = $dataOnu['rx'];
				$SQLinsert['inface'] = $dataOnu['inface'];
				$SQLinsert['zte_idport'] = $dataOnu['keyport'];
				$SQLinsert['cron'] = 1;
				$SQLinsert['type'] = $dataOnu['pon'];
				$SQLinsert['dist'] = (!empty($dataOnu['dist']) ? $dataOnu['dist'] : 0);
				$db->SQLinsert($PMonTables['onus'],$SQLinsert);
			}
		}
	}	
	public function tempSaveOnuEpon($dataOnu){	
		global $db, $config, $lang, $PMonTables;
		$dataOnu['dist'] = str_replace('-1', 0, $dataOnu['dist']);
		// EPON:STATUS 1 -offline 2 - online
		$dataOnu['status'] = (!empty($dataOnu['status']) ? ($dataOnu['status']==1 ? 2 : 1 ) : (!empty($dataOnu['dist']) ? 1 : 2));
		if(is_numeric($dataOnu['keyport'])){
			$onucard = $this->array_huawei_ifindex($dataOnu['keyport']);
			if(is_array($onucard)){
				$SQLset['sw_shelf'] = $onucard['olt'];
				$SQLinsert['sw_shelf'] = $onucard['olt'];
				$SQLset['sw_slot'] = $onucard['slot'];
				$SQLinsert['sw_slot'] = $onucard['slot'];
				$SQLset['portolt'] = $onucard['slot'];
				$SQLinsert['portolt'] = $onucard['slot'];
			}
		}
		if(is_numeric($dataOnu['keyonu'])){
			$arr = $db->Fast($PMonTables['onus'],'*',['zte_idport' =>$dataOnu['keyport'],'keyonu' =>$dataOnu['keyonu'],'olt' => $dataOnu['id']]); 
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
				if(!empty($dataOnu['name']))
					$SQLset['name'] = $dataOnu['name'];
				if(!empty($dataOnu['mac']))
					$SQLset['mac'] = $dataOnu['mac'];
				if(!empty($dataOnu['inface']))
					$SQLset['inface'] = $dataOnu['inface'];				
				if(!empty($dataOnu['reason']))
					$SQLset['reason'] = reasonGponHuawei($dataOnu['reason']);					
				if(!empty($dataOnu['rx']))
					$SQLset['rx'] = $dataOnu['rx'];	
				$SQLset['zte_idport'] = $dataOnu['keyport'];
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
				if(!empty($dataOnu['name']))
					$SQLinsert['name'] = $dataOnu['name'];				
				if(!empty($dataOnu['reason']))
					$SQLinsert['reason'] = reasonGponHuawei($dataOnu['reason']);				
				if(!empty($dataOnu['rx']))
					$SQLinsert['rx'] = $dataOnu['rx'];
				$SQLinsert['inface'] = $dataOnu['inface'];
				$SQLinsert['zte_idport'] = $dataOnu['keyport'];
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
				$getONUstatusPort = $db->Multi($PMonTables['onus'],'idonu,status',['olt' => $this->id,'zte_idport'=>$port['sfpid']]);
				$getONUstatusPortOn = $db->Multi($PMonTables['onus'],'idonu,status',['status' => 1,'olt' => $this->id,'zte_idport'=>$port['sfpid']]);
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
		$sqlList = $db->Multi($PMonTables['onus'],'keyonu,zte_idport,idonu,type',['olt' => $this->id, 'status' => 1]);
		$array = array();
		if(is_array($sqlList)){
			foreach($sqlList as $key => $value){
				if(is_numeric($value['keyonu']) && !empty($value['type']))
					$array[$key] = array('id'=>$this->id,'keyport'=>$value['zte_idport'],'keyonu'=>$value['keyonu'],'pon'=>$value['type'],'do'=>'onu','types'=>'rx');
			}
		}
		return $array;
	}
}
?>