<?php
if(!defined('PONMONITOR')){
	die('Hacking attempt!');
}
class Equipment { 
	public function __construct($id = ''){
		$this->loadSwitchConfig($id);
		$this->loadSwitchOID($id);
	}
    protected function loadSwitchOID($id = ''){
		global $db;
		if($id && $this->switchOidId[$id]){
			$getSwitch['oidid'] = $this->switchOidId[$id];
			$sql = $db->Multi('oid','*',$getSwitch); 
			foreach($sql as $sqlid => $conf){
				$this->SwitchOid[$id][$conf['inf']][$conf['types']][$conf['pon']]['oid'] = $conf['oid'];
				if(!empty($conf['result']))
					$this->SwitchOid[$id][$conf['inf']][$conf['types']][$conf['pon']]['result'] = $conf['result'];
			}								
		}
	}
    protected function loadSwitchConfig($id = '') {
		global $db;
			$switchId['id'] = $id;
			$switch = $db->Fast('switch','*',$switchId);
		if (!empty($switch['id'])) {
			$this->switchId[$switch['id']] = $switch['id'];
			$this->switchIp[$switch['id']] = $switch['netip'];
			$this->switchCommunity[$switch['id']] = $switch['snmpro'];
			$this->switchOidId[$switch['id']] = $switch['oidid'];
			if(!empty($switch['switchfile']))
				$this->switchModule[$switch['id']] = $switch['switchfile'];
		}	
	}	
}
?>