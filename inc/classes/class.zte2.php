<?php
if(!defined('PONMONITOR')){
	die('Hacking attempt!');
}
class ZTE2 { 
    private $primary = 'status,dist,reason,offline';
    private $configapionuepon = 'status,dist,rx,tx,mac,model,volt,reason,bias,temp,vendor,device,offline,eth,vlanmode';
    private $filter = true;
    private $filters = [
        '/"/',
        '/Hex-/i',
        '/OID: /i',
        '/STRING: /i',
        '/Gauge32: /',
        '/INTEGER: /i',
        '/Counter32: /i',
        '/SNMPv2-SMI::enterprises\./i',
        '/iso\.3\.6\.1\.4\.1\./i',
    ];
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
			case 'panel' : 
				return true;
			break;				
			case 'fileonu' : 
				return 'zte2.php';
			break;			
		}	
	}
    public function __construct($id = '', $AllConfig) {
		$this->initsnmp();
		$this->filter = true;
		$this->id = $id;
		$this->ip = $AllConfig->switchIp[$id];
		$this->community = $AllConfig->switchCommunity[$id];
		$this->deviceoid = $AllConfig->SwitchOid;
	}  
	public function ConfigApiOnu($dataOnu){
		if($dataOnu['type']=='EPON')
			$data = ['do' => 'onu','types' => $this->configapionuepon,'pon' => mb_strtolower($dataOnu['type']),'keyonu' => $dataOnu['keyonu'],'id' => $this->id];
		return $data;
	}
	public function Onu($dataPort,$dataOnu){
		$res = [];
		if($dataPort){
			foreach($dataPort as $type => $value) {
				$res[$type] = $this->preparedata($value,$type);
			}
		}
		if(is_array($res)){
			$result = $this->updateonu($dataOnu,$res);
		}else{
			$result = false;
		}
		return $result;
	}
	public function temp_color_css($value){
		if($value>=1 AND $value<=19){
			return 'colorgree';
		}elseif($value>=20 AND $value<=49){
			return 'colornice';
		}elseif($value>=50 AND $value<=59){
			return 'colorhot';
		}elseif($value>=60 AND $value<=70){
			return 'coloralarm';
		}else{
			return 'colornice';	
		}
	}
	public function volot_css($value){
		return (int)$value;
	}
	public function timer_replace($data){
		return str_replace('0000-00-00 00:00:00', 0, $data);
	}
	public function vlanmode($types){
		switch ($types) {
			case 1:
				return 'Transparent transmission';
			break;
			case 2:
				return 'Tag mode';
			break;			
			case 3:
				return 'Translation mode';
			break;			
			case 4:
				return 'Trunk mode';
			break;			
			case 5:
				return 'Hybrid mode';
			break;
		}
	}
	public function updateonu($ont,$real_inf_onu){
		global $db, $lang, $config;
		$result = array();		
		if(is_array($real_inf_onu)){
			if(!empty($real_inf_onu['tx'])){
				$upd[] = 'tx = '.$db->safesql($real_inf_onu['tx']);
				$result['tx'] = $real_inf_onu['tx'];
			}
			if(!empty($real_inf_onu['rx'])){
				$upd[] = 'pwr = '.$db->safesql($real_inf_onu['rx']);
				$result['rx'] = $real_inf_onu['rx'];
			}
			if(!empty($real_inf_onu['model'])) $upd[] = 'model = '.$db->safesql($real_inf_onu['model']);
			if(!empty($real_inf_onu['vendor'])) $upd[] = 'vendor = '.$db->safesql($real_inf_onu['vendor']);
			if(!empty($real_inf_onu['reason'])) $upd[] = 'reason = '.$db->safesql($real_inf_onu['reason']);
			$upd[] = 'dist = '.$db->safesql($real_inf_onu['dist']??0);
			if($ont['status']==2 && $real_inf_onu['status']==1){
				if(!$real_inf_onu['timeaut'])
					$upd[] = 'online = '.$db->safesql(NOW());
				if($real_inf_onu['offline'])
					$upd[] = 'online = '.$db->safesql($real_inf_onu['offline']);
				$upd[] = 'status = '.$db->safesql(1);
			}elseif($ont['status']==1 &&  $real_inf_onu['status']==2){
				$upd[] = 'offline = '.$db->safesql(NOW());
				$upd[] = 'status = '.$db->safesql(2);
			}
			if(isset($upd))
				$db->query('UPDATE onus SET '.implode(',',$upd).' WHERE idonu = '.$db->safesql($ont['idonu']));
			
			$result['type'] = $ont['type'];
			$result['status'] = $real_inf_onu['status'];
			$result['wan'] = $real_inf_onu['eth'];			
			if(!empty($real_inf_onu['reason']))	$result['reason'] = $real_inf_onu['reason'];
			if(!empty($real_inf_onu['model']))	$result['model'] = $real_inf_onu['model'];
			if(!empty($real_inf_onu['vendor']))	$result['vendor'] = $real_inf_onu['vendor'];
			if(!empty($real_inf_onu['device']))	$result['device'] = $real_inf_onu['device'];
			if(!empty($real_inf_onu['dist'])) $result['dist'] = $real_inf_onu['dist'];
			if(!empty($ont['last_pwr'])) $result['last_pwr'] = $ont['last_pwr'];
			if(!empty($real_inf_onu['temp'])){
				$result['temp'] = $real_inf_onu['temp'];
				$result['tempcolor'] = $this->temp_color_css($real_inf_onu['temp']);
			}
			if(!empty($real_inf_onu['volt'])){
				$result['volt'] = $real_inf_onu['volt'];
				$result['voltcolor'] = $real_inf_onu['volt'];
			}
			if(!empty($real_inf_onu['curent'])) $result['curent'] = $real_inf_onu['curent'];
			if(!empty($real_inf_onu['rx'])) $result['rx'] = $real_inf_onu['rx'];
			if(!empty($real_inf_onu['tx'])) $result['tx'] = $real_inf_onu['tx'];
			if(!empty($real_inf_onu['offline'])) $result['offline'] = $real_inf_onu['offline'];			
			if(!empty($real_inf_onu['vlanmode'])) $result['vlanmode'] = $real_inf_onu['vlanmode'];
			return $result;
		}
	}	
	public function preparedata($dataApi,$type){
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
			case 'model':
				$result = $data;
			break;
			case 'volt':
				$result = $data;
				if($result)
					$result = sprintf('%.2f',$result);
				$result = str_replace('0.00',0,$result);
				if(!$result)
					$result = 0;
			break;
			case 'reason':
				$result = $data;
			break;
			case 'bias':
				$result = $data;
				if($result)
					$result = sprintf('%.2f',$result);
				$result = str_replace('0.00',0,$result);
				if(!$result)
					$result = 0;
			break;
			case 'temp':
				$result = $data;
				if($result)
					$result = sprintf('%.2f',$result);
				$result = str_replace('0.00',0,$result);
				if(!$result)
					$result = 0;
			break;			
			case 'vendor':
				$result = $data;
			break;			
			case 'device':
				$result = $data;
			break;			
			case 'offline':
				$result = $this->clear_time($data);
			break;				
			case 'vlanmode':
				$data = $this->clear_time($data);
				$result = $this->vlanmode($data);
			break;			
			case 'eth':
				$result = $data;
				$result = ($result==2?'up':'down');				
			break;			
		}		
		return $result;
	}
	public function clear_time($value){
		$value = str_replace('"','',$value);
		$value = trim($value);
		$value = str_replace('/','-',$value);
		return $value;
	}
	public function savePort($dataPort){
		global $db;
		//Save Vlan
		if(!empty($dataPort['vlan'])){
			foreach($dataPort['vlan'] as $value)
				self::saveVlanSwitch($value);
		}
		//Save Port
		if(!empty($dataPort['port'])){
			foreach($dataPort['port'] as $value)
				self::savePortSwitch($value);
		}
		//Save Pon
		if(!empty($dataPort['pon'])){
			foreach($dataPort['pon'] as $value)
				self::savePonSwitch($value);
		}
	}
	public function saveOnt($new_onus){
		global $db, $config, $lang;
		$save_histor = false;
		$onus = $db->super_query('SELECT * FROM `onus` WHERE keyolt = '.$db->safesql($new_onus['sw_onu']).' AND olt  = '.$db->safesql($new_onus['sw_id']));
		$rx_check = false;
		if($onus){
			$idonu = $onus['idonu'];
			if(!empty($new_onus['dist']))
				$upd[] = 'dist = '.$db->safesql($new_onus['dist']);
			if($new_onus['rx'] && $new_onus['sw_status']==1)
				$upd[] = 'pwr = '.$db->safesql($new_onus['rx']);
			if(!empty($new_onus['reason']))
				$upd[] = 'reason = '.$db->safesql($new_onus['reason']);
			$upd[] = 'portolt = '.$db->safesql($new_onus['zte_idport']);
			$upd[] = 'status = '.$db->safesql($new_onus['sw_status']);
			$upd[] = 'croncheck = '.$db->safesql(NOW());
			if(!empty($new_onus['mac']))
				$upd[] = 'mac = '.$db->safesql($new_onus['mac']);
			if(!empty($new_onus['name']))
				$upd[] = 'name = '.$db->safesql($new_onus['name']);
			if ($new_onus['sw_status']==1){
			$old = 0;
			if(!empty($onus['pwr']))
				$old = signal_onu_minus($onus['pwr']);
			$new = 0;
			if(!empty($new_onus['rx'])){
				$new = signal_onu_minus($new_onus['rx']);
				$rx_check = true;
			}
			if($old < $new && $rx_check){
				$up_dbm = $new - $old;
				if($config['criticsignal']<=$up_dbm && $new_onus['sw_status']==1 && $up_dbm){
					$upd[] = 'rxstatus = '.$db->safesql('up');
					$upd[] = 'last_pwr = '.$db->safesql($onus['pwr']);
					$upd[] = 'change_pwr = '.$db->safesql(NOW());
					//$langrx = '{сигнал збільшився на '.$up_dbm.'}';
					$save_histor = true;
				}
			}
			if($old > $new && $rx_check){
				$down_dbm = $old - $new;
				if($config['criticsignal']<$down_dbm && $new_onus['sw_status']==1 && $down_dbm ){
					$upd[] = 'rxstatus = '.$db->safesql('down');
					$upd[] = 'last_pwr = '.$db->safesql($new_onus['rx']);
					$upd[] = 'change_pwr = '.$db->safesql(NOW());
					//$langrx = '{сигнал зменшився на '.$down_dbm.'}';
					$save_histor = true;
				}
			}
			if($old === $new && $rx_check){
				$upd[] = 'rxstatus = '.$db->safesql('none');
				$save_histor = false;
			}else{
				$upd[] = 'rxstatus = '.$db->safesql('none');
				$save_histor = false;
			}}
			if($new_onus['sw_status']==2 && $onus['status']==1){
				$upd[] = 'offline = '.$db->safesql(NOW());
				onu_log(sprintf($lang['log_onu_26'],$new_onus['inface'],$lang[($new_onus['reason'] ? $new_onus['reason'] : 'err1')]),$idonu,'offline');
				$save_histor = false;
			}
			if($new_onus['sw_status']==1 && $onus['status']==2){
				$upd[] = 'online = '.$db->safesql(NOW());
				onu_log(sprintf($lang['log_onu_27'],$new_onus['inface'],$new_onus['rx']),$idonu,'online');
				$save_histor = true;
			}
			if($upd)
				$db->query('UPDATE onus SET ' . implode(',', $upd).' WHERE idonu = '.$db->safesql($idonu));
		}else{
			$db->query('INSERT INTO onus (name, reason, added, type, portidtext, dist, olt, keyolt, status, pwr, portolt, mac, croncheck) VALUES('.$db->safesql($new_onus['name']).','.$db->safesql($new_onus['reason']).','.$db->safesql(NOW()).','.$db->safesql(mb_strtoupper($new_onus['type'])).','.$db->safesql($new_onus['inface']).','.$db->safesql($new_onus['dist']).','.$db->safesql($new_onus['sw_id']).','.$db->safesql($new_onus['sw_onu']).','.$db->safesql($new_onus['sw_status']).','.$db->safesql(($new_onus['rx']?$new_onus['rx']:0)).','.$db->safesql($new_onus['zte_idport']).','.$db->safesql($new_onus['mac']).','.$db->safesql(NOW()).')');
			$idonu = $db->insert_id();
			$save_histor = true;
			onu_log(sprintf($lang['log_onu_23'],$new_onus['inface'],$new_onus['sn'].$new_onus['mac'],($new_onus['sw_status']==2?$lang['pb_49']:$lang['pb_48'])),$idonu,'new');
		}
		if($config['onugraph']=='on' && $save_histor == true)
			$db->query('INSERT INTO onus_history_rx (oltid, idonu, pwr, datetime) VALUES ('.$db->safesql($new_onus['sw_id']).','.$db->safesql($idonu).','.$db->safesql($new_onus['rx']).','.$db->safesql(NOW()).')');
		$db->query('UPDATE onus_temp SET updates = '.$db->safesql(NOW()).', cron = '.$db->safesql(2).' WHERE id = '.$db->safesql($new_onus['id']));
	}
	public function Port(){
		$data = array();
		$OIdPortEpon = $this->deviceoid[$this->id]['global']['listport']['epon']['oid'];
		$EponListPort = $this->snmp->walk($this->ip,$this->community,$OIdPortEpon,true);		
		$EponListPort = str_replace($OIdPortEpon.'.','',$EponListPort);
		$IndexEponPort = explodeRows($EponListPort);		
		if(is_array($IndexEponPort)){
			$listPort = array();
			foreach ($IndexEponPort as $idPort => $ValuePort) {
                $infPort = explode('=', $ValuePort);
				if(!empty($infPort[0]) && !empty($infPort[1]))
					$listPort[$idPort] = array('id' =>trim($infPort[0]),'typeport' => gettypeport($infPort[1]),'name' => $this->clearData($infPort[1]));
			}
			$data['port'] = $listPort;
		}
		#$OIdSwitchVlan = $this->deviceoid[$this->id]['global']['listport']['epon']['oid'];
		$OIdSwitchVlan = '.1.3.6.1.4.1.3902.1015.20.2.1.2';
		$VlanList = $this->snmp->walk($this->ip,$this->community,$OIdSwitchVlan,true);		
		$VlanList = str_replace($OIdSwitchVlan.'.','',$VlanList);
		$List = explodeRows($VlanList);
		if(is_array($List)){
			foreach ($List as $idVlan => $ValueVlan) {
                $infVl = explode('=', $ValueVlan);
				if(!empty($infVl[0]) && !empty($infVl[1])){
					$listVlan[$idVlan] = array('vlanid' =>trim($infVl[0]), 'vlanname' => $this->clearData($infVl[1]));
				}
			}
			$data['vlan'] = $listVlan;
		}
		if(is_array($data['port'])){
			$id = 1;
			foreach($data['port'] as $idPonport => $valuePon){
				if(preg_match('/pon/i',$valuePon['name'])) {
					preg_match('/([e,g]pon)_(\d+)\/(\d+)\/(\d+)/',$valuePon['name'],$mat);
					$listPon[$idPonport]['name'] = 'EPON '.$mat[2].'/'.$mat[3].'/'.$mat[4].'';
					$listPon[$idPonport]['sort'] = $id;
					$listPon[$idPonport]['sfpid'] = $valuePon['id'];
					$cardType = $this->snmp->get($this->ip,$this->community,vsprintf('1.3.6.1.4.1.3902.1015.2.1.1.3.1.4.0.0.%s',[$mat[3]]),100000,5);
					$listPon[$idPonport]['cardtype'] = str_replace('"','', strtolower(str_replace(' ', '', str_replace('STRING: ', '', trim($cardType)))));
					$listPon[$idPonport]['cardcount'] = getCountOntCard($listPon[$idPonport]['cardtype']);
					$listPon[$idPonport]['sw_shelf'] = $mat[2];
					$listPon[$idPonport]['sw_slot'] = $mat[3];
					$listPon[$idPonport]['sw_port'] = $mat[4];
					$id++;
				}
			}
			usort($listPon, function($arr, $brr){
				return ($arr['sfpid'] - $brr['sfpid']);	
			});
			$data['pon'] = $listPon;
		}
		return $data;
	}
    protected function savePonSwitch($dataPort) {
		global $db;
		$row = $db->super_query('SELECT * FROM `switch_pon` WHERE oltid = '.$db->safesql($this->id).'AND sfpid = '.$db->safesql($dataPort['sfpid']));
		if(!empty($row['sfpid'])){
			$id_sql_port = $row['id'];
		}else{
			$db->query('INSERT INTO switch_pon (cardtype,portonu,sort,oltid,realportname,sfpid,added) VALUES ('.$db->safesql($dataPort['cardtype']).','.$db->safesql($dataPort['cardcount']).','.$db->safesql($dataPort['sort']).','.$db->safesql($this->id).','.$db->safesql($dataPort['name']).','.$db->safesql($dataPort['sfpid']).','.$db->safesql(NOW()).')');
			$id_sql_port = $db->insert_id();
		}
		if(!empty($dataPort['sfpid']))
			$db->query('UPDATE onus_temp SET zte_idport = '.$db->safesql($dataPort['sfpid']).' WHERE sw_slot = '.$db->safesql($dataPort['sw_slot']).' AND sw_port = '.$db->safesql($dataPort['sw_port']).' AND sw_shelf = '.$db->safesql($dataPort['sw_shelf']).' AND sw_id = '.$db->safesql($this->id));
		$all_onu = $db->query('SELECT * FROM `onus_temp` WHERE sw_id = '.$db->safesql($this->id).' AND zte_idport = '.$db->safesql($dataPort['sfpid']));
		$count = $db->num_rows($all_onu);
		if(!empty($dataPort['sfpid']))
			$db->query('UPDATE switch_pon SET portcountonu = '.$db->safesql($count ?? 0).' WHERE oltid = '.$db->safesql($this->id).' AND sfpid = '.$dataPort['sfpid']);

	}
    protected function savePortSwitch($data) {
		global $db;
		if(!empty($data['id'])){	
			$row = $db->super_query('SELECT * FROM `switch_port` WHERE deviceid = '.$db->safesql($this->id).' AND llid = '.$db->safesql($data['id']));
			if(!$row['id'])
				$db->query('INSERT INTO switch_port (deviceid,llid,nameport,typeport,added,operstatus) VALUES ('.$db->safesql($this->id).','.$db->safesql($data['id']).','.$db->safesql($data['name']).','.$db->safesql($data['typeport']).','.$db->safesql(NOW()).','.$db->safesql('none').')');
			$db->query('UPDATE switch SET updates_port = '.$db->safesql(NOW()).' WHERE id = '.$db->safesql($this->id));
		}
	}
    protected function saveVlanSwitch($data) {
		global $db;
		if(!empty($data['vlanid'])){	
			$row = $db->super_query('SELECT * FROM `switch_vlan` WHERE deviceid = '.$db->safesql($this->id).' AND vlanid = '.$db->safesql($data['vlanid']));
			if(!$row['id'])
				$db->query('INSERT INTO switch_vlan (deviceid,vlanid,vlanname,added) VALUES ('.$db->safesql($this->id).','.$db->safesql($data['vlanid']).','.$db->safesql($data['vlanname']).','.$db->safesql(NOW()).')');
		}	
	}
    protected function initsnmp() {
        $this->snmp = new SnmpPmon();
    }  
	public function epon_convert($index) {
		$ifIndex = str_pad(decbin($index), 32, '0', STR_PAD_LEFT);
		$shelf_no  = bindec(substr($ifIndex, 4, 4));
		$slot_no = bindec(substr($ifIndex, 8, 5));
		$port_no = bindec(substr($ifIndex, 13, 3))+1;
		$ont_no = bindec(substr($ifIndex, 16, 8));
		return $shelf_no.'/'.$slot_no.'/'.$port_no.':'.$ont_no;
	}
    private function prepareResult(array $data): array
    {
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
	private function reason($check){
		switch ($check) {
			case 8 : $type = 'err6';break;
			case 2 : $type = 'err0';break;			
			case 0 : $type = 'err0';break;			
			case 3 : $type = 'err30';break;			
			case 4 : $type = 'err31';break;			
			case 5 : $type = 'err32';break;			
			case 6 : $type = 'err33';break;			
			case 7 : $type = 'err34';break;			
			case 9 : $type = 'err1';break;			
			case 255 : $type = 'err0';break;
			default:$type = 'err0';	
		}	
		return ($type ?? 'err0');
	}	
	public function Panel(){
		$res .='<div class="inf-switch-panel">';
		$res .='<a href=""><div class="ic"><img src="/file/onu/eth.png"></div><h3>Всі порти</h3>Eth,GEPO,GPON</a>';
		$res .='<a href=""><div class="ic"><img src="/file/onu/eth_off.png"></div><h3>Помилки</h3>InIfErrors</a>';
		$res .='<a href=""><div class="ic"><img src="/file/onu/output.png"></div><h3>Vlan</h3>Список Vlan</a>';
		$res .='<a href=""><div class="ic"><img src="/file/onu/temp_onu.png"></div><h3>CPU,Temp</h3>Статистика</a>';
		$res .='<a href=""><div class="ic"><img src="/file/onu/signup.png"></div><h3>Журнал</h3>Комутатора</a>';
		$res .='<a href=""><div class="ic"><img src="/file/onu/service_onu.png"></div><h3>Налаштування</h3>Комутатора</a>';
		$res .='</div>';
		return $res;
	}		
	public function tempUpdateSignalCheck(){	
		global $db;
		$db->query('UPDATE onus_temp SET rating = '.$db->safesql(7).' WHERE sw_id = '.$db->safesql($this->id).' AND sw_status = '.$db->safesql(1).'');
	}
	public function tempSaveSignalSaveOnuEpon($dataOnu){	
		global $db, $config, $lang;
		$rx = $this->clear_rx($dataOnu['rx']);
		if(!empty($dataOnu['keyonu']) && !empty($dataOnu['id']) && $rx)
			$db->query('UPDATE onus_temp SET rx = '.$db->safesql($rx).', rating = '.$db->safesql(1).' WHERE sw_id = '.$db->safesql($dataOnu['id']).' AND `sw_onu` = '.$db->safesql($dataOnu['keyonu']).'');
	}	
	public function tempSaveSignalSaveOnuGpon($dataOnu){	
		global $db, $config, $lang;
		$rx = $this->clear_rx($dataOnu['rx']);
		if(!empty($dataOnu['keyonu']) && !empty($dataOnu['id']) && $rx)
			$db->query('UPDATE onus_temp SET rx = '.$db->safesql($rx).', rating = '.$db->safesql(1).' WHERE sw_id = '.$db->safesql($dataOnu['id']).' AND `sw_onu` = '.$db->safesql($dataOnu['keyonu']).'');
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
		$value = str_replace('"', '',$value);
		$value = trim($value);
		$value = str_replace('N/A',0,$value);
		$value = sprintf('%.2f',$value);
		$value = str_replace('0.00',0,$value);
		return $value;
	}
	public function tempSaveOnuEpon($dataOnu){	
		global $db, $config, $lang;
		$dataOnu['status'] = (!empty($dataOnu['status']) ? $dataOnu['status'] : (!empty($dataOnu['dist']) ? 1 : 2));
		if(!empty($dataOnu['keyonu'])){
			$zapros = $db->query('SELECT * FROM `onus_temp` WHERE sw_id = '.$db->safesql($dataOnu['id']).' AND `sw_onu` = '.$db->safesql($dataOnu['keyonu']));
			$checkonu = $db->num_rows($zapros);
			$arr = $db->get_row($zapros);
			if($checkonu){
				if($dataOnu['status']==1 && $arr['sw_status']==2){
					$upd[] = 'rating = '.$db->safesql(7);
				}else{
					$upd[] = 'rating = '.$db->safesql(1);
				}
				$upd[] = 'cron = '.$db->safesql(1);
				$upd[] = 'sw_status = '.$db->safesql($dataOnu['status']);
				$upd[] = 'type = '.$db->safesql($dataOnu['type']);
				if(!empty($dataOnu['dist']))
					$upd[] = 'dist = '.$db->safesql($dataOnu['dist']);
				if(!empty($dataOnu['reason']))
					$upd[] = 'reason = '.$db->safesql($this->reason($dataOnu['reason']));
				if(!empty($dataOnu['mac']))
					$upd[] = 'mac = '.$db->safesql($dataOnu['mac']);
				if(!empty($dataOnu['inface']))
					$upd[] = 'inface = '.$db->safesql($dataOnu['inface']);
				$db->query('UPDATE onus_temp SET '.implode(',',$upd).' WHERE id = '.$db->safesql($arr['id']));
				$id_temp_onu = $arr['id'];
			}else{
				$dataOnu['dist'] = (!empty($dataOnu['dist']) ? $dataOnu['dist'] : 0);
				$db->query('INSERT INTO onus_temp (sw_id, updates, rating, sw_onu, sw_status, mac, inface, dist, type, cron, reason) VALUES ('.$db->safesql($dataOnu['id']).','.$db->safesql(NOW()).','.$db->safesql(1).','.$db->safesql($dataOnu['keyonu']).','.$db->safesql($dataOnu['status']).','.$db->safesql($dataOnu['mac']).','.$db->safesql($dataOnu['inface']).','.$db->safesql($dataOnu['dist']).','.$db->safesql($dataOnu['type']).','.$db->safesql(1).','.$db->safesql($this->reason($dataOnu['reason'])).')');
				$id_temp_onu = $db->insert_id();
			}
			if(!empty($dataOnu['keyonu']) && $id_temp_onu){
				$detali = $this->epon_convert_detali($dataOnu['keyonu']);
				$db->query('UPDATE onus_temp SET sw_shelf = '.$db->safesql($detali['shelf']).',	sw_slot = '.$db->safesql($detali['slot']).', sw_port = '.$db->safesql($detali['port']).', sw_ont = '.$db->safesql($detali['ont']).' WHERE sw_onu = '.$db->safesql($dataOnu['keyonu']));
			}
		}
	}
	public function epon_convert_detali($index) {
		$arr = array();
		$ifIndex = str_pad(decbin($index), 32, '0', STR_PAD_LEFT);
		$shelf_no  = bindec(substr($ifIndex, 4, 4));
		$slot_no = bindec(substr($ifIndex, 8, 5));
		$port_no = bindec(substr($ifIndex, 13, 3))+1;
		$ont_no = bindec(substr($ifIndex, 16, 8));
		$arr['shelf'] = $shelf_no;
		$arr['slot'] = $slot_no;
		$arr['port'] = $port_no;
		$arr['ont'] = $ont_no;
		return $arr;
	}
	public function getListOnuOnline($id = ''){	
		global $db;
		$sql1 = $db->query('SELECT * FROM onus WHERE olt = '.$db->safesql($id).'');
		$count_onu = $db->num_rows($sql1);
		if(!$count_onu){
			$where_sql = 'AND sw_status = '.$db->safesql(1);
		}else{
			$where_sql = 'AND rating = '.$db->safesql(7);
		}
		$sql2 = $db->query('SELECT * FROM onus_temp WHERE sw_id = '.$db->safesql($id).' '.$where_sql);
		$countonu = $db->num_rows($sql2);
		$array = [];
		if($countonu){
			$key = 1;
			while($value = $db->get_row($sql2)){
				$array[$key] = array('id'=>$id,'keyonu'=>$value['sw_onu'],'pon'=>$value['type'],'do'=>'onu','types'=>'rx');
				$key ++;
			}
			return $array;
		}
	}
	public function saveOnuCommands(){
		global $db;
		$sql = $db->query('SELECT * FROM onus_temp WHERE sw_id = '.$db->safesql($this->id).' AND inspector = '.$db->safesql(1).'');
		if($db->num_rows($sql)){
			$db->query('UPDATE onus SET inspector = '.$db->safesql(3).' WHERE olt = '.$db->safesql($this->id));
			while($arr = $db->get_row($sql)){
				$this->saveOnt($arr);
			}
			$this->revizor($this->id);		
		}
	}	
	public function inspector($response) {
		global $db;
		$db->query('UPDATE onus_temp SET inspector = '.$db->safesql(3).' WHERE sw_id = '.$db->safesql($this->id));
		foreach ($response as $key => $value) {
			$inspector = $db->super_query('SELECT * FROM `onus_temp` WHERE sw_id = '.$db->safesql($this->id).' AND `sw_onu` = '.$db->safesql($value['keyonu']).'');
			if(isset($inspector['id']))
				$db->query('UPDATE onus_temp SET inspector = '.$db->safesql(1).' WHERE id = '.$db->safesql($inspector['id']));
		}
	}
	public function revizor($olt){
		global $db;
		$db->query('DELETE FROM `onus_temp` WHERE sw_id = '.$db->safesql($olt).' AND inspector = '.$db->safesql(3));
		$sql = $db->query('SELECT portolt, keyolt, mac, idonu, portidtext FROM onus WHERE olt = '.$db->safesql($olt).' AND inspector = '.$db->safesql(3));
		if($db->num_rows($sql)){
			while($arr = $db->get_row($sql)){
				$check = $db->super_query('SELECT id FROM onus_temp WHERE sw_id = '.$db->safesql($olt).' AND sw_onu = '.$db->safesql($arr['keyolt']).'');
				if(!$check)
					deletonu($arr['idonu'],$olt,$arr['mac'],$arr['portidtext']);
			}
		}
	}	
	public function Load(){	
		$result = array();
		$EponMacOID = $this->deviceoid[$this->id]['global']['listmac']['epon']['oid'];		
		$EponListMac = $this->snmp->walk($this->ip,$this->community,$EponMacOID,true);		
		$EponListMac = str_replace($EponMacOID.'.','',$EponListMac);
		$IndexEpon = explodeRows($EponListMac);		
		if(is_array($IndexEpon)){
			foreach ($IndexEpon as $io => $eachsig) {
                $line = explode('=', $eachsig);
				if (isset($line[0]) && isset($line[1])) {
					$keyonu = trim($line[0]); // device index
					$TempMac['mac'] = $line[1];
					$MAC = $this->prepareResult($TempMac);
					$eachONUMAC = ClearDataMac($MAC['mac']);
					$tmpONUMAC = strtolower(AddMacSeparator(RemoveMacAddressSeparator($eachONUMAC, array(':', '-', '.', ' '))));
					$result[$io] = array('do' => 'onu','id'=>$this->id,'pon'=>'epon','inface'=>self::epon_convert($keyonu),'types'=>$this->primary,'mac'=>$tmpONUMAC,'keyonu'=>$keyonu);
				}	
			}
			if(is_array($result))
				$this->inspector($result);
		}
		return $result;
	}
}
?>