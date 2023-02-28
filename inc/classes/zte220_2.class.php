<?php
if(!defined('PONMONITOR')){
	die('Hacking attempt!');
}
class ZTE_c220_2 { 
    private $indexdevice = array();
    private $now = null;
    private $primarygpon = 'dist,status,reason,name';
    private $primaryepon = 'dist,status,reason';
    private $configapionugpon = 'dist,status';
    private $configapionuepon = 'status,dist,config,reason,rx,tx,model,eth,volt,temp,offline,vendor,device,vlanmode';
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
	public function epon_mac($tempmac) {
		if (strpos($tempmac,'Hex') !== false){
			$tempmac = preg_replace('~^.*?( = )~i','',$tempmac);
			$tempmac = preg_replace('/Hex-STRING/','',$tempmac);
			$tempmac = preg_replace('/ : /','',$tempmac);
			$tempmac = preg_replace('/ /','',$tempmac);
			$tempmac = trim($tempmac, " \"");
			$tempmac = trim($tempmac, '"');
			$tempmac = stripslashes($tempmac);
			$tempmac = strtolower($tempmac);
			$tempmac = substr(preg_replace('/(.{2})/','\1:',$tempmac,6), 0, -1);
		}else{
			$tempmac = preg_replace('~^.*?( = )~i','',$tempmac);
			$tempmac = preg_replace('/STRING/','',$tempmac);
			$tempmac = preg_replace('/ : /','',$tempmac);
			$tempmac = trim($tempmac, " \"");
			$tempmac = trim($tempmac, '"');
			$tempmac = stripslashes($tempmac);	
			$tempmac = bin2hex($tempmac);
			$tempmac = strtolower($tempmac);
			$tempmac = substr(preg_replace('/(.{2})/','\1:',$tempmac,6), 0, -1);
		}
		return $tempmac;
	}
	public function epon_convert($llid) {
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
	public function Load(){
		global $db, $PMonTables;		
		$listoidinface = $this->deviceoid[$this->id]['onu']['listmac']['epon']['oid'];
		$listepondata = $this->snmp->walk($this->ip,$this->community,$listoidinface,true);
		if($listepondata){
			$indexEponOnu = explodeRowsTwo(str_replace('.'.$listoidinface.'.','',$listepondata));
			if(is_array($indexEponOnu)){
				foreach($indexEponOnu as $io => $eachsig) {
					$line = explode('=', $eachsig);
					if(isset($line[0]) && isset($line[1])) {
						$result[$io] = array('do' => 'onu','id'=>$this->id,'mac'=>$this->epon_mac($line[1]),'pon'=>'epon','inface'=>$this->epon_convert(trim($line[0])),'types'=>$this->primaryepon,'keyonu'=> trim($line[0]));
					}	
				}
				if(is_array($result)){
					$db->SQLupdate($PMonTables['onus'],['cron' => 2],['olt' => $this->id]);
				}
			}
		}else{
			$db->SQLinsert($PMonTables['swlog'],['deviceid' =>$this->id,'types' =>'switch','message' =>'emptysnmpwalk','added' =>$this->now]);
		}
		if(is_array($result)){
			checkerONU($result,$this->id);
		}
		return ((count($result) === 0) ? null : $result);
	}
	public function ConfigApiOnu($data){
		if($data['type']=='epon')
			$array = array('do' => 'onu','types' => $this->configapionuepon,'pon' => 'epon','keyonu' => $data['keyonu'],'id' => $this->id);
		return $array;
	}
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
			$result['wan'] = ($getData['eth']==2?'up':'down');;			
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
			if(!empty($getData['temp'])) 
				$result['temp'] = $getData['temp'];				
			if(!empty($getData['device'])) 
				$result['device'] = $getData['device'];				
			if(!empty($getData['config'])) 
				$result['config'] = $getData['config'];			
			if(!empty($getData['volt'])) 
				$result['volt'] = $getData['volt'];			
			if(!empty($getData['vlanmode'])) 
				$result['vlanmode'] = $getData['vlanmode'];			
			if(!empty($getData['bias'])) 
				$result['bias'] = $getData['bias'];
			if(!empty($getData['offline'])) 
				$result['offline'] = $getData['offline'];
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
					$result = sprintf('%.2f',$data);
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
			case 'config':
				$result = $data;
			break;				
			case 'volt':
				if(preg_match('/N/i',$data)) {
					$result = 0;	
				}else{
					$result = sprintf('%.2f',$data);
				}
			break;				
			case 'bias':
				if($data){
					$result = sprintf('%.2f',$data);
				}else{
					$result = 0;
				}
			break;			
			case 'temp':
				if($data){
					$result = sprintf('%.2f',$data);
				}else{
					$result = 0;
				}
			break;				
			case 'vlanmode':
				$result = $data;
			break;				
			case 'model':
				$result = $data;
			break;			
			case 'device':
				$result = $data;
			break;			
			case 'offline':
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
	public function clearResult($value){
		return str_replace('/','',trim(str_replace('"','',$value)));
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
				self::savePonPortSwitch($value);
			}
			$db->SQLupdate($PMonTables['switch'],['updates_port' => $this->now],['id' => $this->id]);
		}
	}
	protected function savePonPortSwitch($data){
		global $db, $PMonTables;
		if(!empty($data['llid'])){	
			$row = $db->Fast($PMonTables['switchport'],'*',['deviceid' => $this->id, 'llid' => $data['llid']]);
			if(!$row['id'])
				$db->SQLinsert($PMonTables['switchport'],['deviceid' => $this->id,'llid' => $data['llid'],'nameport' => $data['name'],'typeport' => $data['typeport'],'operstatus' => $data['operstatus'],'added' => $this->now]);
		}
	}
	public function Port(){
		$oidport = $this->deviceoid[$this->id]['global']['listport']['port']['oid'];
		if($oidport){
			$listportsnmp = $this->snmp->walk($this->ip,$this->community,$oidport,true);
			$alllistport = str_replace('.'.$oidport.'.','',$listportsnmp);
			$indexarrayport = explodeRows($alllistport);	
		}
		if(is_array($indexarrayport)){
			foreach($indexarrayport as $idPort => $ValuePort) {	
                $infPort = explode('=', $ValuePort);
				if(!empty($infPort[0]) && !empty($infPort[1])){
					$portstatus = $this->snmp->get($this->ip,$this->community,'1.3.6.1.2.1.2.2.1.8.'.trim($infPort[0]));
					if(preg_match('/INTEGER/i',trim($portstatus))) {
						preg_match('/INTEGER(.*)/',trim($portstatus),$mat);
						$status = trim(str_replace('"','',str_replace(':','',$mat[1])));
					}else{
						$status = trim(str_replace('"','',str_replace('INTEGER:','',$portstatus)));
					}
					$portSw = trim(str_replace('"','',str_replace('STRING:','',$infPort[1])));
					$listport[$idPort] = array('operstatus' =>($status==1?'up':'down'),'id' =>trim($infPort[0]),'typeport' => getTypePort($portSw),'name' => (isset($portSw)?$portSw:''));
				}				
			}
			$data['port'] = $listport;
		}
		if(is_array($data['port'])){
			foreach($data['port'] as $idPonport => $valuePon){
				if(preg_match('/epon/i',mb_strtolower($valuePon['name']))) {
					preg_match('/_0\/(\d+)\/(\d+)/',$valuePon['name'],$mat);
					$listPon[$idPonport]['name'] = 'EPON 0/'.$mat[1].'/'.$mat[2].'';
					$listPon[$idPonport]['sort'] = $mat[1];
					$listPon[$idPonport]['sfpid'] = $valuePon['id'];
					$listPon[$idPonport]['llid'] = $valuePon['id'];
					$listPon[$idPonport]['typeport'] = $valuePon['typeport'];
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
    protected function savePortSwitch($data) {
		global $db, $PMonTables;
		if(!empty($data['id'])){	
			$row = $db->Fast($PMonTables['switchport'],'*',['deviceid' => $this->id, 'llid' => $data['id']]);
			if(!$row['id']){
				$db->SQLinsert($PMonTables['switchport'],['deviceid' => $this->id,'llid' => $data['id'],'operstatus' => (!empty($data['operstatus']) ? $data['operstatus'] : ''),'nameport' => $data['name'],'typeport' => $data['typeport'],'added' => $this->now]);
			}else{
				$db->SQLupdate($PMonTables['switchport'],['operstatus' => (!empty($data['operstatus']) ? $data['operstatus'] : '')],['id' => $row['id']]);
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
			$inf = str_replace('EPON ', '',$dataPort['name']);
			preg_match('/(\d+)\/(\d+)\/(\d+)/',$inf,$mat);
			$get = array('sw_shelf' => ($mat[1]+1),'sw_slot' => $mat[2],'sw_port' => $mat[3],'olt' => $this->id);			
			$db->SQLupdate($PMonTables['onus'],['portolt' => $dataPort['sfpid']],$get);
		}
		$allonu = $db->Multi($PMonTables['onus'],'*',['olt' => $this->id,'portolt' => $dataPort['sfpid']]);
		if(!empty($dataPort['sfpid']))
			$db->SQLupdate($PMonTables['switchpon'],['count' =>count($allonu)],['sfpid' =>$dataPort['sfpid'],'oltid' => $this->id]);
	}
	public function tempSaveSignalSaveOnuGpon($dataOnu){	
	
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
		if (preg_match('/N/i',$value)){
			return 0;
		} elseif(preg_match('/65535/i',$value)) {
			return 0;
		} else{
			return sprintf("%.2f",$value);			
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
		
	}	
	public function tempSaveOnuEpon($dataOnu = array()){	
		global $db, $config, $lang, $PMonTables;
		$statusONU = (!empty($dataOnu['status']) && $dataOnu['status']==1 ? 1 : 2);
		preg_match('/(\d+)\/(\d+)\/(\d+):/',$dataOnu['inface'],$mat);
		if(is_numeric($dataOnu['keyonu'])){
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
				$SQLset['sw_shelf'] = $mat[1];
				$SQLset['sw_slot'] = $mat[2];
				$SQLset['sw_port'] = $mat[3];
				$SQLset['updates'] = $this->now;
				$SQLset['cron'] = 1;
				$SQLset['status'] = $statusONU;
				$SQLset['type'] = $dataOnu['pon'];
				if(!empty($dataOnu['dist']))
					$SQLset['dist'] = $dataOnu['dist'];				
				if(!empty($dataOnu['mac']))
					$SQLset['mac'] = $dataOnu['mac'];
				if(!empty($dataOnu['inface']))
					$SQLset['inface'] = $dataOnu['inface'];				
				if(!empty($dataOnu['reason']))
					$SQLset['reason'] = $this->reason($dataOnu['reason']);	
				if(!empty($dataOnu['keyport'])){
					#$SQLset['portolt'] = $dataOnu['keyport'];
					#$SQLset['zte_idport'] = $dataOnu['keyport'];
				}
				$db->SQLupdate($PMonTables['onus'],$SQLset,['idonu' => $arr['idonu']]);
			}else{				
				$SQLinsert = array('sw_shelf' => $mat[1],'sw_slot' => $mat[2],'sw_port' => $mat[3],'olt' => $dataOnu['id'],'updates' => $this->now,'added' => $this->now, ($statusONU==1?'online':'offline') => $this->now,'rating' => 1,'keyonu' => $dataOnu['keyonu'],'status' => $statusONU,'mac' => $dataOnu['mac'],'inface' => $dataOnu['inface'],'dist' => (!empty($dataOnu['dist']) ? $dataOnu['dist'] : 0),'reason' => (!empty($dataOnu['reason']) ? $this->reason($dataOnu['reason']) : ''),'type' => $dataOnu['pon'],'cron' => 1);
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
				$get = array('sw_shelf' => ($mat[1]+1),'sw_slot' => $mat[2],'sw_port' => $mat[3],'olt' => $this->id);
				$geton = array('status' => 1,'sw_shelf' => ($mat[1]+1),'sw_slot' => $mat[2],'sw_port' => $mat[3],'olt' => $this->id);
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
		$sqlList = $db->Multi($PMonTables['onus'],'keyonu,portolt,type',['olt' => $this->id,'status' => 1]);
		if(is_array($sqlList)){
			foreach($sqlList as $key => $value){
				if(is_numeric($value['keyonu'])){
					$temp['id'] = $this->id;
					$temp['keyonu'] = $value['keyonu'];
					if(!empty($value['portolt']))
						$temp['keyport'] = $value['portolt'];
					$temp['pon'] = $value['type'];
					$temp['do'] = 'onu';
					$temp['types'] = 'rx';
					$array[$key] = $temp;
				}
			}
		}
		return (is_array($array) ? $array : null);
	}
}
?>