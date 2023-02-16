<?php
if(!defined('PONMONITOR')){
	die('Hacking attempt!');
}
class ZTE_c320_2 { 
    private $indexdevice = array();
    private $now = null;
    private $primarygpon = 'dist,status,reason,name';
    private $configapionugpon = 'dist,status,reason,name,note,eth,rx,tx,model,vendor,uptime,typereg,config,mngtvlan';
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
	public function llid2s($llid,$on) {
		$lx=sprintf("%08x",$llid);
		switch ($lx[0]) {
			case '1':
				$sh=hexdec($lx[1])+1;
				$sl=hexdec($lx[2].$lx[3]);
				$ol=hexdec($lx[4].$lx[5]);
				break;
			case '2':
				$sh=hexdec($lx[3]);
				$sl=hexdec($lx[4].$lx[5]);
				$ol=hexdec($lx[6].$lx[7]);
				if ($cl>16) {
					$cl-=16; $sl++;
				}
					$ol1=$ol;
				break;

			case '3':
				$sh=hexdec($lx[1])+1;
				$sl=hexdec($lx[2].$lx[3]);
				$ol=($sl&0x07)+1;
				$sl=$sl>>3;
				$on=hexdec($lx[4].$lx[5]);
				break;
			case '6':
				$sh=hexdec($lx[1])+1;
				$sl=hexdec($lx[2].$lx[3]);
				$ol=0;
				break;
		}
		return "{$sh}/{$sl}/{$ol}:{$on}";
	}
	private function valueToSerial($tempsn) {
		if (strpos($tempsn,'Hex-STRING') !== false){
			$tempsn = preg_replace('~^.*?( = )~i','',$tempsn);
			$tempsn = preg_replace('/Hex-STRING/','',$tempsn);
			$tmpv = explode(" ",$tempsn);
			$val1 = hexdec($tmpv[1]);
			$val2 = hexdec($tmpv[2]);
			$val3 = hexdec($tmpv[3]);
			$val4 = hexdec($tmpv[4]);
			$val5 = $tmpv[5];
			$val6 = $tmpv[6];
			$val7 = $tmpv[7];
			$val8 = $tmpv[8];
			return chr($val1).chr($val2).chr($val3).chr($val4).$val5.$val6.$val7.$val8;
		}else{
			$tempsn = preg_replace('~^.*?( = )~i','',$tempsn);
			$onu_snc1 = preg_replace ('/STRING:/','',$tempsn);
			$tmpv = explode(" ","$onu_snc1");
			$tmpe = str_split($tmpv[1]);
			return $tmpe[1].$tmpe[2].$tmpe[3].$tmpe[4].strtoupper(dechex(ord($tmpe[5]))).strtoupper(dechex(ord($tmpe[6]))).strtoupper(dechex(ord($tmpe[7]))).strtoupper(dechex(ord($tmpe[8])));
		}
	}	
	/*
	Отримуємо всі onu по snmp з комутатора
	*/
	public function Load(){
		global $db, $PMonTables;		
		$listGponinface = $this->deviceoid[$this->id]['onu']['listsn']['gpon']['oid'];
		$listGPON = $this->snmp->walk($this->ip,$this->community,$listGponinface,true);
		if($listGPON){
			$indexGponOnu = explodeRowsTwo(str_replace('.'.$listGponinface.'.','',$listGPON));
			if(is_array($indexGponOnu)){
				foreach($indexGponOnu as $io => $eachsig) {
					$line = explode('=', $eachsig);
					if(isset($line[0]) && isset($line[1])) {
						preg_match('/(\d+).(\d+)/i',$line[0],$dataMatch);
						$result[$io] = array('do' => 'onu','id'=>$this->id,	'sn'=>$this->valueToSerial($eachsig),'pon'=>'gpon','inface'=>$this->llid2s(trim($dataMatch[1]),trim($dataMatch[2])),'types'=>$this->primarygpon,'keyonu'=> trim($dataMatch[2]),'keyport'=> trim($dataMatch[1]),'portolt'=> trim($dataMatch[1]));
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
			$result['wan'] = ($getData['eth']==5?'up':'down');;			
			$result['wanportzte'] = $this->type_speed_zte($getData['eth']);			
			if(!empty($getData['name']))	
				$result['name'] = $getData['name'];			
			if(!empty($getData['note']))	
				$result['note'] = $getData['note'];			
			if(!empty($getData['model']))	
				$result['model'] = $getData['model'];
			if(!empty($getData['reason']))	
				$result['reason'] = $getData['reason'];			
			if(!empty($getData['vendor']))	
				$result['vendor'] = $getData['vendor'];				
			if(!empty($getData['mngtvlan']))	
				$result['mngtvlan'] = $getData['mngtvlan'];			
			if(!empty($getData['typereg']))	
				$result['typereg'] = $getData['typereg'];			
			if(!empty($getData['config']))	
				$result['config'] = $getData['config'];
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
	function type_speed_zte($type){
		switch($type){
			case 6: 
				$result1['img']='eth1g';	
				$result1['txt']='1 Gbps';
				$result1['st']='enable';
				$result1['st_tx']='enable';					
				$result1['status']='up';	
			break; 			
			case 3: 
				$result1['img']='ethup';	
				$result1['txt']='10 Mbps';
				$result1['st']='enable';
				$result1['st_tx']='enable';					
				$result1['status']='up';	
			break;   
			case 5: 
				$result1['img']='ethup';	
				$result1['txt']='100 Mbps';	
				$result1['st']='enable';
				$result1['st_tx']='enable';				
				$result1['status']='up';	
			break;
			case 65535: 
				$result1['img']='ethdown';	
				$result1['txt']='Offline';
				$result1['st']='disable';					
				$result1['status']='down';	
			break; 	
			case 1: 
				$result1['img']='eth_na';	
				$result1['txt']='Down';
				$result1['st']='down';	
				$result1['st_tx']='Down';	
				$result1['status']='down';					
			break;
			case 0: 
				$result1['img']='ethdown';	
				$result1['txt']='Down';	
				$result1['st']='disable';
				$result1['st_tx']='disable';				
				$result1['status']='down';	
			break;
		}
		return $result1;
	}	
	public function preparedataCDATA($dataApi,$type){
		$data = $this->clearData($dataApi);
		switch($type){
			case 'status':
				$result = (isset($data) && $data==3 ? 1 : 2);
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
					$result = sprintf('%.2f',($data==65535?0:(intval($data) - 15000) * 0.002));
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
			case 'typereg':
				$result = (isset($data) ? $this->typereg($data) : null);
			break;				
			case 'name':
				$result = str_replace('$','',$data);
			break;				
			case 'note':
				$result = str_replace('$','',$data);
			break;				
			case 'config':
				$result = $data;
			break;				
			case 'mngtvlan':
				$result = $data;
			break;				
			case 'model':
				$result = $data;
			break;			
			case 'reason':
				$result = $this->reason($data);
			break;				
			case 'eth':
				$result = $data;
			break;			
		}		
		return $result;
	}
	public function typereg($type){
		if ($type == 1){
			$res = "regModeSn";
		} elseif ($type == 2){
			$res = "regModePw";
		} elseif ($type == 3){
			$res = "regModeSnPlusPw";
		} elseif ($type == 4){
			$res = "regModeRegisterId";
		} elseif ($type == 5){
			$res = "regModeRegisterIdPlus8021x";
		} elseif ($type == 6){
			$res = "regModeRegisterIdPlusMutual";
		} elseif ($type == 7){
			$res = "regModeTefPw";
		} elseif ($type == 8){
			$res = "regModeSnPlusTefPw";
		} elseif ($type == 9){
			$res = "regModeLoid";
		} elseif ($type == 10){
			$res = "regModeLoidPlusPw";
		}  
		return $res;
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
		$OIdPortPort = $this->deviceoid[$this->id]['global']['listport']['port']['oid'];
		if(!$this->indexdevice)
			$ListPort = $this->snmp->walk($this->ip,$this->community,$OIdPortPort,true);
		$AllListPort = str_replace('.'.$OIdPortPort.'.','',$ListPort);
		$IndexponPort = explodeRows($AllListPort);	
		if(is_array($IndexponPort)){
			$listPort = array();
			foreach ($IndexponPort as $idPort => $ValuePort) {				
                $infPort = explode('=', $ValuePort);
				if(!empty($infPort[0]) && !empty($infPort[0])){
					$portName = $this->snmp->get($this->ip,$this->community,'1.3.6.1.2.1.2.2.1.2.'.trim($infPort[0]));
					$listPort[$idPort] = array('descrport' =>trim(str_replace('"','',str_replace('STRING:','',$portName))),'id' =>trim($infPort[0]),'typeport' => getTypePort($ValuePort),'name' => getNameZteport($infPort[1]));
				}				
			}
			$data['port'] = $listPort;
		}
		if(is_array($data['port'])){
			foreach($data['port'] as $idPonport => $valuePon){
				if(preg_match('/GPON/i',$valuePon['name'])) {
					preg_match('/1\/(\d+)\/(\d+)/',$valuePon['name'],$mat);
					$listPon[$idPonport]['name'] = 'GPON 1/'.$mat[1].'/'.$mat[2].'';
					$listPon[$idPonport]['sort'] = $mat[1];
					$listPon[$idPonport]['descrport'] = $valuePon['descrport'];
					$listPon[$idPonport]['sfpid'] = $valuePon['id'];
					$listPon[$idPonport]['llid'] = $valuePon['id'];
					$listPon[$idPonport]['typeport'] = $valuePon['typeport'];
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
		return (is_array($data) ? $data : null);
	}
	/*
	Зберігаємо інформацію по портах
	*/
    protected function savePortSwitch($data) {
		global $db, $PMonTables;
		if(!empty($data['id'])){	
			$row = $db->Fast($PMonTables['switchport'],'*',['deviceid' => $this->id, 'llid' => $data['id']]);
			if(!$row['id']){
				$db->SQLinsert($PMonTables['switchport'],['deviceid' => $this->id,'llid' => $data['id'],'descrport' => (!empty($data['descrport']) ? $data['descrport'] : ''),'nameport' => $data['name'],'typeport' => $data['typeport'],'operstatus' => 'none','added' => $this->now]);
			}else{
				#$db->SQLupdate($PMonTables['switchport'],['descrport' => (!empty($data['descrport']) ? $data['descrport'] : '')],['id' => $row['id']]);
			}
		}
	}
    protected function savePonSwitch($dataPort) {
		global $db, $PMonTables;
		$row = $db->Multi($PMonTables['switchpon'],'*',['oltid' => $this->id,'sfpid' => $dataPort['sfpid']]);
		if(!count($row)){
			$db->SQLinsert($PMonTables['switchpon'],['idportolt' => $dataPort['sort'],'support' => $dataPort['cardcount'],'sort' => $dataPort['sort'],'oltid' => $this->id,'pon' => $dataPort['name'],'sfpid' => $dataPort['sfpid'],'added' => $this->now]);
		}
		if(!empty($dataPort['sfpid']) && !empty($dataPort['sort'])){		
			$inf = str_replace('GPON ', '',$dataPort['name']);
			preg_match('/(\d+)\/(\d+)\/(\d+)/',$inf,$mat);
			$get = array('sw_shelf' => $mat[1],'sw_slot' => $mat[2],'sw_port' => $mat[3],'olt' => $this->id);			
			$db->SQLupdate($PMonTables['onus'],['portolt' => $dataPort['sfpid']],$get);
		}
		$allonu = $db->Multi($PMonTables['onus'],'*',['olt' => $this->id,'portolt' => $dataPort['sfpid']]);
		if(!empty($dataPort['sfpid']))
			$db->SQLupdate($PMonTables['switchpon'],['count' =>count($allonu)],['sfpid' =>$dataPort['sfpid'],'oltid' => $this->id]);
	}
	public function tempSaveSignalSaveOnuGpon($dataOnu){	
		global $db, $PMonTables, $config;
		$savehistor = false;
		$onu = $db->Fast($PMonTables['onus'],'status,rx,idonu',['olt' => $this->id,'portolt' => $dataOnu['keyport'],'keyonu' => $dataOnu['keyonu']]);
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
	public function check_signal($value){
		if (preg_match('/-80000/i',$value)){
			return 0;
		} elseif(preg_match('/65535/i',$value)) {
			return 0;
		} else{
			return sprintf("%.2f",intval($value)/1000);			
		}
	}
	public function clear_rx($value){
		if(preg_match('/6553/i',$value)) {
			return 0; 
		}else{
			return $this->check_signal($value);
		}
	}
	public function tempUpdateSignalCheck(){	
		global $db, $PMonTables;

	}
	public function reason($check1){
		switch ($check1) {
			case 0 : $type = 'err1';	break;			
			case 1 : $type = 'err5';    break;	
			case 2 : $type = 'err6';    break; 
			case 3 : $type = 'err6';    break;	
			case 7 : $type = 'err25';   break;	
			case 9 : $type = 'err1';    break;	
			case 12: $type = 'err0';    break;				
		}
		return $type;
	}
	public function tempSaveOnuGpon($dataOnu = array()){	
		global $db, $config, $lang, $PMonTables;
		$statusONU = (!empty($dataOnu['status']) && $dataOnu['status']==3 ? 1 : 2);
		preg_match('/(\d+)\/(\d+)\/(\d+):/',$dataOnu['inface'],$mat);
		if(!empty($dataOnu['keyonu']) && !empty($dataOnu['keyport'])){
			$arr = $db->Fast($PMonTables['onus'],'*',['zte_idport' =>$dataOnu['keyport'],'keyonu' =>$dataOnu['keyonu'],'olt' => $dataOnu['id']]); 
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
				$SQLset['sw_shelf'] = $mat[1];
				$SQLset['sw_slot'] = $mat[2];
				$SQLset['sw_port'] = $mat[3];
				$SQLset['updates'] = $this->now;
				$SQLset['cron'] = 1;
				$SQLset['status'] = $statusONU;
				$SQLset['type'] = $dataOnu['pon'];
				if(!empty($dataOnu['dist']))
					$SQLset['dist'] = $dataOnu['dist'];				
				if(!empty($dataOnu['name']))
					$SQLset['name'] = $dataOnu['name'];				
				if(!empty($dataOnu['descr']))
					$SQLset['descr'] = $dataOnu['descr'];
				if(!empty($dataOnu['sn']))
					$SQLset['sn'] = $dataOnu['sn'];
				if(!empty($dataOnu['inface']))
					$SQLset['inface'] = $dataOnu['inface'];				
				if(!empty($dataOnu['reason']))
					$SQLset['reason'] = $this->reason($dataOnu['reason']);	
				if(!empty($dataOnu['keyport'])){
					$SQLset['portolt'] = $dataOnu['keyport'];
					$SQLset['zte_idport'] = $dataOnu['keyport'];
				}
				$db->SQLupdate($PMonTables['onus'],$SQLset,['idonu' => $arr['idonu']]);
			}else{				
				$SQLinsert = array('sw_shelf' => $mat[1],'sw_slot' => $mat[2],'sw_port' => $mat[3],'olt' => $dataOnu['id'],'updates' => $this->now,'added' => $this->now, ($statusONU==1?'online':'offline') => $this->now,'rating' => 1,'keyonu' => $dataOnu['keyonu'],'status' => $statusONU,'sn' => $dataOnu['sn'],'inface' => $dataOnu['inface'],'dist' => (!empty($dataOnu['dist']) ? $dataOnu['dist'] : 0),'name' => (!empty($dataOnu['name']) ? $dataOnu['name'] : ''),'reason' => (!empty($dataOnu['reason']) ? $this->reason($dataOnu['reason']) : ''),'type' => $dataOnu['pon'],'cron' => 1,'zte_idport' => $dataOnu['keyport'],'portolt' => $dataOnu['keyport']);
				$db->SQLinsert($PMonTables['onus'],$SQLinsert);
			}
		}
	}
	public function StatisticOLT(){	
		global $db, $PMonTables;
		$getPort = $db->Multi($PMonTables['switchpon'],'*',['oltid' => $this->id]);
		if(count($getPort)){
			foreach($getPort as $port){
				$inf = str_replace('GPON ', '',$port['pon']);
				preg_match('/(\d+)\/(\d+)\/(\d+)/',$inf,$mat);
				$get = array('sw_shelf' => $mat[1],'sw_slot' => $mat[2],'sw_port' => $mat[3],'olt' => $this->id);
				$geton = array('status' => 1,'sw_shelf' => $mat[1],'sw_slot' => $mat[2],'sw_port' => $mat[3],'olt' => $this->id);
				$getONUstatusPort = $db->Multi($PMonTables['onus'],'idonu,status',$get);
				$getONUstatusPortOn = $db->Multi($PMonTables['onus'],'idonu,status',$geton);
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
					$array[$key] = array('id'=>$this->id,'keyonu'=>$value['keyonu'],'keyport'=>$value['portolt'],'pon'=>$value['type'],'do'=>'onu','types'=>'rx');
			}
		}
		return (is_array($array) ? $array : null);
	}
}
?>