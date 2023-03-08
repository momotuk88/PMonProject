<?php
if(!defined('PONMONITOR')){
	die('Hacking attempt!');
}
class Cron{
	public function InsertCron($id){
		global $db;
		if(is_numeric($id)){
			$db->SQLinsert('swcron',['oltid' => $id,'status' => 'yes','priority' => 3,'added' => date("Y-m-d H:i:s")]);
			$jobid = $db->getInsertId();
			$db->SQLupdate('switch',['jobid'=>$jobid],['id'=>$id]);
			return $jobid;
		}
	}
	public function convert_tim_check($time){
		if($time=='1h'){
			$timecheck = '1 hour';
		}elseif($time=='1h'){
			$timecheck = '1 hour';
		}elseif($time=='2h'){
			$timecheck = '2 hour';
		}elseif($time=='3h'){
			$timecheck = '3 hour';
		}elseif($time=='15min'){
			$timecheck = '15 min';
		}elseif($time=='30min'){
			$timecheck = '30 min';
		}else{
			$timecheck = '3 hour';	
		}
		return $timecheck;
	}
	public function start(){
		global $db, $config;
		$check = null;
		$first = false;
		$res = array();
		$data = array();
		$SQLSelectSwitch = $db->Multi('swcron');
		if(count($SQLSelectSwitch)){
			foreach($SQLSelectSwitch as $arr){
				if($arr['status']=='yes' || $arr['status']=='go')
					$res[] = $arr;
				if($arr['status']=='no')
					$wtf[] = $arr; 				
			}	
		}
		if (is_array($res)){
			$data = $this->recheck_job($res);
		}
		if(is_array($data)) {
			$check = self::foreachList($data,1,2);
			$first = false;
		}else{
			$first = true;
			$data = $this->Restart();
		}
		$relist = $this->check_switch();
		if($first && !$data){
			if(is_array($relist))
				$check = self::foreachList($relist,1,2);
		}else{

		}
		return $check ?? null;
	}
    public function check_switch(){
		global $db, $PMonTables;
		$SQLSelectSwitch = $db->Multi('switch','monitor,id,typecheck,updates,updates_rx,status',['monitor'=>'yes']);
		if(count($SQLSelectSwitch)){
			foreach($SQLSelectSwitch as $arr){
				$timecheck = self::convert_tim_check($arr['typecheck']);
				if(strtotime($arr['updates']) < strtotime(date("Y-m-d H:i:s").' -'.$timecheck)){
					$checkCronList = $db->Fast('swcron','*',['oltid' => $arr['id']]);
					if(!empty($checkCronList['id'])){
						
					}else{
						$jobid = self::InsertCron($arr['id']);
						$data[$jobid]['olt'] = $arr['id'];
						$data[$jobid]['jobid'] = $jobid;
					}
				}
			}
		}
		return (isset($data) ? $data : null);
	}
	public function Restart(){
		
	}
	public function foreachList($dataSQL,$startlist,$currentdevice){
		foreach($dataSQL as $tempDATA){
			$data[$tempDATA['jobid']]['olt'] = $tempDATA['olt'];
			$data[$tempDATA['jobid']]['jobid'] = $tempDATA['jobid'];
			$startlist++;
			if($startlist==($currentdevice+1)){
				break;
			}
		}
		return (isset($data) ? $data : null);
	}
    public function recheck_job($row){
		global $db;
		foreach ($row as $key => $value){
			$switch = $db->Fast('switch','id,jobid,monitor,status',['id' => $value['oltid']]);
			if($switch['monitor']=='yes'){
				if($switch['jobid']==$value['id']){
					$datamonitor[$value['id']]['olt'] = $switch['id'];
					$datamonitor[$value['id']]['jobid'] = $value['id'];
				}				
			}
		}
		return (isset($datamonitor) ? $datamonitor : null);
	}
	public function check_case_time($type,$added){
		if(!$added)
			return true;
		$added_job = date_parse_from_format('Y-m-d h:i:s',$added);
		$data = [];
		$curent_day = intval(date('d'));
		$curent_h = intval(date('H'));
		$curent_i = date('i');
		switch($type){
			case '30m': 
				$check_time = $added_job['minute']+30;
				if($curent_day==$added_job['day'] && $curent_h==$added_job['hour'] && $curent_i>=$check_time){
					return true;
				}		
			break;			
			case '1h': 
				$check_time = $added_job['hour']+1;
				if($check_time==24)
					$check_time = 00;
				if($curent_day==$added_job['day'] && $curent_h==$check_time){
					return true;
				}elseif($curent_day==$added_job['day'] && $curent_h>$check_time){
					return true;
				}
			break;			
			case '2h': 
				$check_time = $added_job['hour']+2;
				if($check_time==24)
					$check_time = 00;
				$check_time;
				if($curent_day==$added_job['day'] && $curent_h==$check_time){
					return true;
				}elseif($curent_day==$added_job['day'] && $curent_h>$check_time){
					return true;
				}		
			break;
		}
		if(strtotime($added)<strtotime(NOW().' -3 hour')){
			return true;
		}	
    }
    public function endCron($id){
		global $db;
		$SQLSelectCron = $db->Multi('swcron');
		if(count($SQLSelectCron)){
			foreach($SQLSelectCron as $cron){
				
			}			
		}
	} 
	public function StartJobMonitor($jobid,$oltid){
		global $db;
		$this->RunMonitor(['olt'=>$oltid,'jobid'=>$jobid]);
		$db->SQLupdate('swcron',['status'=>'go'],['id'=>$jobid,'oltid'=>$oltid]);
	}	
	public function RunMonitor($params = array()){
		if(!empty($params['olt']) && !empty($params['jobid'])){
			#print_r('post'.$params['jobid'].'-'.$params['olt']);
			post_system($params['jobid'],$params['olt']);
		}
	}
}
?>
