<?php
if(!defined('PONMONITOR')){
	die('Hacking attempt!');
}
class Planet2424 { 
    private $snmpdata = array();
    private $now = null;
    private $filter = true;
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
		$oidlistport = $this->deviceoid[$this->id]['global']['listport']['port']['oid'];
		$snmpdata = $this->snmp->walk($this->ip,$this->community,$oidlistport,true);
		if(!$snmpdata){
			die('empty');
		}		
		$tempdata = str_replace('.'.$oidlistport.'.','',$snmpdata);
		$infaceport = $this->explodeRows($tempdata);	
		if(is_array($infaceport)){
			foreach ($infaceport as $keyport => $valuedata) {				
                $match = explode('=', $valuedata);
				if(!empty($match[0]) && !empty($match[0])){
					$dataIndexPort = $this->clearResult($match[1]);
					if(preg_match('/Port/i',$dataIndexPort)){
						$keyidport = (int)trim($match[0]);
						$listarrayport[$keyport] = array('id' =>$keyidport,'typeport' => ($keyidport<=8?'combosfp':'sfp'),'name' => 'Port 0/'.$keyidport
						);
					}
				}				
			}
			$data['port'] = $listarrayport;
			if(is_array($data['port'])){
				foreach($data['port'] as $idport => $valuedataport){
					$dataport[$idport]['id'] = $valuedataport['id'];
					$dataport[$idport]['typeport'] = $valuedataport['typeport'];
					$dataport[$idport]['name'] = $valuedataport['name'];
					$oidportoperstatus = $this->deviceoid[$this->id]['global']['status']['port']['oid'];
					$oidportoperstatus_ = str_replace('keyport',$valuedataport['id'],$oidportoperstatus);
					$resultsnmpdata = $this->snmp->get($this->ip,$this->community,$oidportoperstatus_,true);
					$resultclear = trim(str_replace('=','',str_replace('INTEGER:','',str_replace($oidportoperstatus_,'',$resultsnmpdata))));
					$dataport[$idport]['status'] = $this->status($resultclear);
				}
			}
		}
		return (is_array($dataport) ? $dataport : null);
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
			if(!empty($row['id'])){
				$db->SQLupdate('switch_port',['operstatus' => $data['status'],($data['status']=='down'?'timedown':'timeup')=>$this->now],['id' => $row['id']]);
			}else{
				$db->SQLinsert('switch_port',['deviceid' => $this->id,'llid' => $data['id'],'nameport' => $data['name'],'typeport' => $data['typeport'],'operstatus' => $data['status'],'added' => $this->now,($data['status']=='down'?'timedown':'timeup')=>$this->now]);
			}
		}
	}
	public function status($status){
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
		$value = str_replace('=', '',$value);
		$value = trim($value);	
		return $value;
	}
}
?>