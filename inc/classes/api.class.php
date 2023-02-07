<?php
if(!defined('PONMONITOR')){
	die('Hacking attempt!');
}
class MonitorApi{
	public $timer;
    private $snmp = ['public','private',1000000,5];
    private $filter = true;
	private $filters = ['/"/','/Hex-STRING/i','/OID: /i','/STRING: /i','/Gauge32: /','/INTEGER: /i','/Counter32: /i','/SNMPv2-SMI::enterprises\./i','/iso\.3\.6\.1\.4\.1\./i'];
    public function __construct(){
		global $db;
			$this->snmp = new SnmpMonitor();
			$this->timer = microtime(true);
    }
    public function setFilter($filter){
        $this->filter = boolval($filter);
    }
    private function prepareResult(array $data): array{
        if( $this->filter ) {
            $result = array_map(
                function($value) {
                    return preg_replace($this->filters, '', $value);
                },
                $data
            );
        }
        return ($this->filter && isset($result)) ? $result : $data;
    }
    public function apiport($array){
		$data = [];		
		$oidInErrorswitch = '1.3.6.1.2.1.2.2.1.14.'.$array['keyport'];
		$getIn = $this->snmp->get($array['netip'],$array['snmpro'],$oidInErrorswitch);
		$data['in'] = @$this->trimSNMPOutput($getIn,$oidInErrorswitch);		
		$oidOutErrorswitch = '1.3.6.1.2.1.2.2.1.20.'.$array['keyport'];
		$getOut = $this->snmp->get($array['netip'],$array['snmpro'],$oidOutErrorswitch);
		$data['out'] = @$this->trimSNMPOutput($getOut,$oidOutErrorswitch);		
		return $data;
	}
	public function monitorDevice($array){
		global $db, $lang;
		$ListOId = $db->Multi('oid','types,oid,result',['pon'=>'device','oidid'=>$array['oidid'],'inf' => 'health']);
		$confapi = array();
		if(is_array($ListOId)){
			foreach($ListOId as $conf) {
				/*
				$confapi['id'] = $array['id'];
				$confapi['netip'] = $array['netip'];
				$confapi['snmpro'] = $array['snmpro'];
				$confapi['oid'][$conf['types']] = $conf['oid'];
				*/
				if(!empty($conf['oid'])){
					$getIn = $this->snmp->get($array['netip'],$array['snmpro'],$conf['oid']);
					$res = @$this->trimSNMPOutput($getIn,$conf['oid']);	
					if($res)
						$data = $this->getResultFromat($res,$conf['result']);
					$confapi['result'][$conf['types']] = $data;					
				}
			}
		}
		return $confapi;
	}
	public function monitorUPS($array){
		global $monitor_cfg;
		foreach($array['oid'] as $type => $value) {
			if($type && !empty($value)){
				$get = @$this->snmp->get($array['netip'],$array['snmpro'],$value);
				if($get){
					$data = $this->trimSNMPOutput($get,$value);
					if(!empty($array['result'][$type]))
						$data = $this->getResultFromat($data,$array['result'][$type]);
				}
				$result[$type] = $data;
			}
		}	
		return $result;		
	}
	public function apiups($array){
		global $db, $lang;
		$ListOId = $db->Multi('oid','types,oid,result',['pon'=>'device','oidid'=>$array['oidid'],'inf' => 'health']);
		$confapi = array();
		if(is_array($ListOId)){
			foreach($ListOId as $conf) {
				$confapi['id'] = $array['id'];
				$confapi['netip'] = $array['netip'];
				$confapi['snmpro'] = $array['snmpro'];
				$confapi['oid'][$conf['types']] = $conf['oid'];
				if(!empty($conf['result']))
					$confapi['result'][$conf['types']] = $conf['result'];
			}
		}else{
			$confapi['result'] = 'err5';
		}
		return $confapi;
	}
	public function format($array){
		global $monitor_cfg, $db, $lang, $config;
		$FormatOID = $db->Multi('oid','types,oid,result',['pon'=>$array['pon'],'oidid'=>$array['oidid'],'inf' => $array['global']]);
		$confapi = array();
		if(is_array($FormatOID)){
			foreach($FormatOID as $conf){
				$confapi['oid'][$conf['types']] = $conf['oid'];
				if(!empty($conf['result']))
					$confapi['result'][$conf['types']] = $conf['result'];
			}
			$array_separated = explode(',',$array['types']);
			$result = array();
			foreach($array_separated as $type) {
				if(!empty($confapi['oid'][$type])){
					$result[$type]['oid'] = $this->zamina($confapi['oid'][$type],($array['keyonu']??false),($array['keyport']??false));
					$result[$type]['netip'] = $array['netip'];
					$result[$type]['id'] = $array['id'];
					$result[$type]['snmpro'] = $array['snmpro'];
					if(!empty($array['keyonu']))
						$result[$type]['keyonu'] = $array['keyonu'];
					$result[$type]['type'] = $type;
					if(!empty($array['keyport']))
						$result[$type]['keyport'] = $array['keyport'];			
					if(!empty($confapi['result'][$type]))
						$result[$type]['format'] = $confapi['result'][$type];
				}
			}
		}
		return $result;
	}
    public function zamina($oid, $keyonu = false, $keyport = false){
		if(isset($keyonu) || isset($keyport)){
			$result = str_replace('keyonu',$keyonu,$oid);
			$result = str_replace('keyport',$keyport,$result);
			$result = str_replace('s',$keyonu,$result);
		}else{
			$result = trim($oid);
		}
		$result = trim($result);
		return $result;
	}
	public function getResultFromat($data,$format = null){
		$result = $data;
		if($format){
			if(preg_match('/a:2:/',$format)){
				$res = unserialize($format);
				$result = $res[$data];
			}
			if(preg_match('/FUNCT/i',$format)) {
				preg_match('/=(.*)INT(\d+)=/i',$format,$dataMatch);
				if(preg_match('/FUNCT1/',$dataMatch[1])){
					$result = $data / $dataMatch[2];
				}
				if(preg_match('/FUNCT2/',$dataMatch[1])){
					$result = $data * $dataMatch[2];
				}
			}	
		}
		return $result;
	}
	public function apiget($array){
		global $monitor_cfg;
		foreach($array as $type => $value) {
			if($type && !empty($value['oid'])){
				$get = @$this->snmp->get($value['netip'],$value['snmpro'],$value['oid']);
				$data = $this->trimSNMPOutput($get,$value['oid']);
				if(!empty($value['format'])){
					$result[$type] = $this->getResultFromat($data,$value['format']);	
				}else{
					$result[$type] = $data;	
				}						
			}
		}
		return (isset($result) ? $result :  '');
	}	
	public function trimSNMPOutput($snmpData, $oid) {
		$value = str_replace('INTEGER:', '',$snmpData);
		$value = str_replace('Hex-STRING:', '',$value);
		$value = str_replace('STRING:', '',$value);
		$value = str_replace('Gauge32:', '',$value);
		$value = str_replace('Gauge64:', '',$value);
		$value = str_replace('Counter32:', '',$value);
		$value = str_replace('Counter64:', '',$value);
		$value = str_replace('Timeticks:', '',$value);
		$value = str_replace($oid, '',$value);
		$value = str_replace('=', '',$value);
		$value = str_replace('"', '',$value);
		$value = str_replace(' ', '',$value);
		return trim($value);		
	}	
	public function trimSNMPOut($snmpData) {
		$value = str_replace('INTEGER:', '',$snmpData);
		$value = str_replace('Hex-STRING:', '',$value);
		$value = str_replace('STRING:', '',$value);
		$value = str_replace('Gauge32:', '',$value);
		$value = str_replace('Gauge64:', '',$value);
		$value = str_replace('Counter32:', '',$value);
		$value = str_replace('Counter64:', '',$value);
		$value = str_replace('Timeticks:', '',$value);
		$value = str_replace('"', '',$value);
		$value = str_replace(' ', '',$value);
		return trim($value);		
	}
	public function trimSNMPOutput2($snmpData, $oid, $returnAsStr = false) {
		$oidValue = array('Counter32:','Counter64:','Gauge32:','Gauge64:','INTEGER:','OID:','Timeticks:','Hex-STRING:','STRING:','Hex-STRING:','STRING:','Network Address:');
		$result = ($returnAsStr) ? '' : array('', '');
		if (!empty($snmpData)) {
			if (!is_array($oidValue)) {
				$oidValue = explode(',', $oidValue);
			}
			$snmpData = str_replace('"', '', $snmpData);
			$snmpData = str_replace($oid, '', $snmpData);
			$snmpData = str_replace($oidValue, '', $snmpData);
			$snmpData = trim($snmpData, '. \n\r\t');
			if (!$returnAsStr) {
				$snmpData = explode('=', $snmpData);
				if (isset($snmpData[1])) {
					$snmpData[0] = trim($snmpData[0]);
					$snmpData[1] = trim($snmpData[1]);
				}
			}
			$result = ($snmpData[0] ? $snmpData[0] : false);
		}
		return (isset($result) ? $result : NULL);
	}
	public function apigetAll($value){
		$snmp = @new SNMP(SNMP::VERSION_2C,$value['netip'],$value['snmpro']);
		$data = @$snmp->get($value['oid'], true);
		if($data){
			if(!empty($value['format'])){
				$result['result'] = $this->getResultFromat($data,$value['format']);	
			}else{
				if(!empty($value['oidid'])){
					$result['result'] = $this->trimSNMPOut($data);
					#$result['result'] = ($dtats==3?1:2);
				}else{
					$result['result'] = $data;
				}				
			}
		}else{
			$result['result'] = 0;
		}
		return $result;
	}	
	public function apiwalk($array){
		global $cache;
		$result = [];
		foreach($array as $type => $value) {
			$namecache = 'cache_'.$type.'_'.$value['id'];
			if(false===($getdata=$cache->get($namecache))){
				$getdata = $this->snmp_walk($value['netip'],$value['snmpro'],$value['snmp'],$value['oid']);
				$cache->set($namecache,$getdata,0,15*60);
			}
			$result[$type] = $namecache;
		}
		return $result;
	}
	public function snmp_walk($netip,$snmpro,$type,$oid){
		snmp_set_quick_print(1);
		if($type=='snmp'){
			$result = @snmp2_real_walk($netip,$snmpro,$oid);
		}elseif($type=='class'){
			$snmp = @new SNMP(SNMP::VERSION_2C,$netip,$snmpro);
			$result = @$snmp->walk($oid, true);
		}
		if(!$result)
			$result = false;
		return $result;
	}
	private function prepareData($data){
        if( !is_array($data) ) {
            $data = array($data);
        }
        return array_map(function($value){
            return preg_replace('/[^\.A-Z0-9_ !@#$%^&()+={}[\]\',~`\-\'":;\\/*|><?]|\.+$/i', '', $value);
        }, $data);
    }
	public function array_get($switch){
		global $monitor_cfg;

	}
}
