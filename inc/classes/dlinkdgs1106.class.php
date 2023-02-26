<?php
if(!defined('PONMONITOR')){
	die('Hacking attempt!');
}
class DlinkDGS1106ME { 
    private $indexdevice = array();
    private $now = null;
    private $filter = true;
    private $filters = ['/"/','/Hex-/i','/OID: /i','/STRING: /i','/Gauge32: /','/INTEGER: /i','/Counter32: /i','/SNMPv2-SMI::enterprises\./i','/iso\.3\.6\.1\.4\.1\./i'];
	public function Support($check){
		switch ($check) {
			case 'port' : 
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
	public function Load(){
		global $db;		
	}	
	public function explodeRows($data) {
		$result = explode("\n", $data);
		return ($result);
	}
	public function clearResult($value){
		$value = str_replace('"','',$value);
		$value = trim($value);
		$value = str_replace('/','',$value);
		return $value;
	}
	public function Port(){
		global $db;		
		$OIdPortList = $this->deviceoid[$this->id]['global']['listport']['port']['oid'];
		if(!$this->indexdevice){
			$ListPortSw = $this->snmp->walk($this->ip,$this->community,$OIdPortList,true);
		}else{
			$ListPortSw = $this->indexdevice;
		}
		$ListPortSwitch = str_replace('.'.$OIdPortList.'.','',$ListPortSw);
		$IndexPort = $this->explodeRows($ListPortSwitch);	
		if(is_array($IndexPort)){
			$listPort = array();
			foreach ($IndexPort as $idPort => $ValuePort) {				
                $infPort = explode('=', $ValuePort);
				if(!empty($infPort[0]) && !empty($infPort[0])){
					$dataIndexPort = $this->clearResult($infPort[1]);
					if(!preg_match('/1\/(\d+)/i',$dataIndexPort) AND !preg_match('/System/i',$dataIndexPort) AND !preg_match('/802/i',$dataIndexPort)){
						$listPort[$idPort] = array('id' =>trim($infPort[0]),'typeport' => getTypePortDlink1106(trim($infPort[0])),'name' => getNamesDlink1106(trim($infPort[0])));
					}
				}				
			}
			$data['port'] = $listPort;
			if(is_array($data['port'])){
				foreach($data['port'] as $idPonport => $valuePon){
					$DATAPort[$idPonport]['id'] = $valuePon['id'];
					$DATAPort[$idPonport]['typeport'] = $valuePon['typeport'];
					$DATAPort[$idPonport]['name'] = $valuePon['name'];
					$OIdPortStatus = $this->deviceoid[$this->id]['global']['status']['port']['oid'];
					$OIdPortStatus = str_replace('keyport',$valuePon['id'],$OIdPortStatus);
					$ListPortSt = $this->snmp->get($this->ip,$this->community,$OIdPortStatus,true);
					$dataStatus = getFormatSNMP($ListPortSt,'integer');
					$DATAPort[$idPonport]['status'] = $this->Status($dataStatus);
				}
			}
		}
		return (is_array($DATAPort) ? $DATAPort : null);
	}
	public function savePort($dataPort){
		global $db;
		if($dataPort){
			foreach($dataPort as $value){
				self::savePortSwitch($value);
			}
		}
	}
    protected function savePortSwitch($data) {
		global $db;
		if(!empty($data['id'])){	
			$row = $db->Fast('switch_port','*',['deviceid' => $this->id, 'llid' => $data['id']]);
			if(!empty($row['id']))
				$db->SQLupdate('switch_port',['operstatus' => $data['status'],($data['status']=='down'?'timedown':'timeup')=>$this->now],['id' => $row['id']]);
			if(!$row['id'])
				$db->SQLinsert('switch_port',['deviceid' => $this->id,'llid' => $data['id'],'nameport' => $data['name'],'typeport' => $data['typeport'],'operstatus' => $data['status'],'added' => $this->now,($data['status']=='down'?'timedown':'timeup')=>$this->now]);
		}
	}
	public function Status($status){
		if($status==2){
			return 'down';
		}else{
			return 'up';
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
}
?>