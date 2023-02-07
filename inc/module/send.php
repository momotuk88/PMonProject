<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$insert = null;
$getDataSql = array();
$allowed_types = array(
	'image/pjpeg' => 'jpg',
	'image/jpeg' => 'jpg',
	'image/jpg' => 'jpg',
	'image/png' => 'png'
);
switch($act){
	case 'savedevice': 
		if(!empty($USER['class']) && $USER['class']>=6){	
		if(isset($_POST['name']))
			$getDataSql['name'] = Clean::text($_POST['name']);			
		if(isset($_POST['deviceid']))
			$getDataSql['deviceid'] = Clean::int($_POST['deviceid']);			
		if(isset($_POST['group']))
			$getDataSql['group'] = Clean::int($_POST['group']);		
		if(isset($_POST['ip']))
			$getDataSql['ip'] = Clean::str($_POST['ip']);
		if(isset($_POST['mac']))
			$getDataSql['mac'] = Clean::str($_POST['mac']);
		if(isset($_POST['sn'])) 
			$getDataSql['sn'] = Clean::str($_POST['sn']);		
		if(isset($_POST['community']))
			$getDataSql['community'] = Clean::str($_POST['community']);	
		if(!empty($getDataSql['deviceid']) && !empty($getDataSql['ip']))
			$SQLgetModel = $db->Fast('equipment','*',['id'=>$getDataSql['deviceid']]);
		if(!empty($getDataSql['ip']))
			$SQLgetSwitch = $db->Fast('switch','*',['netip'=>$getDataSql['ip']]);
		if(!$SQLgetSwitch['id']){
			$SQLinsertSklad = array('model' => $SQLgetModel['name'],'mac' => ($getDataSql['mac'] ? $getDataSql['mac'] : null),'sn' => ($getDataSql['sn'] ? $getDataSql['sn'] : null),'ip' => $getDataSql['ip'],'device' => ($SQLgetModel['device']),'added' => $time);
			$db->SQLinsert('sklad_device',$SQLinsertSklad);
			$skladid = $db->getInsertId();
			if(!empty($getDataSql['deviceid']) && !empty($getDataSql['ip']) && !empty($getDataSql['community']) && !empty($SQLgetModel['name'])){
				$SQLinsertSwitch['inf'] = $SQLgetModel['name'];
				$SQLinsertSwitch['model'] = $SQLgetModel['model'];
				$SQLinsertSwitch['netip'] = $getDataSql['ip'];
				$SQLinsertSwitch['snmpro'] = $getDataSql['community'];
				$SQLinsertSwitch['device'] = $SQLgetModel['device'];
				$SQLinsertSwitch['oidid'] = $SQLgetModel['oidid'];
				$SQLinsertSwitch['img'] = $SQLgetModel['photo'];
				$SQLinsertSwitch['added'] = $time;
				$SQLinsertSwitch['updates'] = date('Y-m-d H:i:s', strtotime('- 1 hour'));
				$SQLinsertSwitch['skladid'] = $skladid;
				$SQLinsertSwitch['place'] = $getDataSql['name'];
				$SQLinsertSwitch['monitor'] = 'yes';
				$SQLinsertSwitch['typecheck'] = '1h';
				if(!empty($getDataSql['mac']))
					$SQLinsertSwitch['mac'] = $getDataSql['mac'];
				if(!empty($getDataSql['sn']))
					$SQLinsertSwitch['sn'] = $getDataSql['sn'];			
				if(!empty($SQLgetModel['phpclass']))
					$SQLinsertSwitch['class'] = $SQLgetModel['phpclass'];
				$db->SQLinsert('switch',$SQLinsertSwitch);
				$deviceid = $db->getInsertId();
				switchLog($deviceid,'system',$lang['addnewdevice'].' '. $SQLgetModel['name'].' '.$SQLgetModel['model']);
			}
			if(isset($deviceid) && !empty($getDataSql['ip']))
				saveBaseip($getDataSql['ip'],$deviceid);
		}
		}
		$go->redirect('device');	
	break;	
	case 'saveconfig': // зберігаємо налаштування системи	
		if(!empty($USER['class']) && $USER['class']>=6){
			if(isset($_POST['sklad']))
				$getDataSql['sklad'] = Clean::str($_POST['sklad']);	
			if(isset($_POST['pon']))
				$getDataSql['pon'] = Clean::str($_POST['pon']);	
			if(isset($_POST['tag']))
				$getDataSql['tag'] = Clean::str($_POST['tag']);	
			if(isset($_POST['comment']))
				$getDataSql['comment'] = Clean::str($_POST['comment']);	
			if(isset($_POST['configport']))
				$getDataSql['configport'] = Clean::str($_POST['configport']);	
			if(isset($_POST['unit']))
				$getDataSql['unit'] = Clean::str($_POST['unit']);	
			if(isset($_POST['telegram']))
				$getDataSql['telegram'] = Clean::str($_POST['telegram']);	
			if(isset($_POST['telegramtoken']))
				$getDataSql['telegramtoken'] = Clean::str($_POST['telegramtoken']);	
			if(isset($_POST['telegramchatid']))
				$getDataSql['telegramchatid'] = Clean::str($_POST['telegramchatid']);	
			if(isset($_POST['marker']))
				$getDataSql['marker'] = Clean::str($_POST['marker']);	
			if(isset($_POST['map']))
				$getDataSql['map'] = Clean::str($_POST['map']);			
			if(isset($_POST['geo_lon']))
				$getDataSql['geo_lon'] = Clean::str($_POST['geo_lon']);				
			if(isset($_POST['statusport']))
				$getDataSql['statusport'] = Clean::str($_POST['statusport']);				
			if(isset($_POST['errorport']))
				$getDataSql['errorport'] = Clean::str($_POST['errorport']);				
			if(isset($_POST['geo_lan']))
				$getDataSql['geo_lan'] = Clean::str($_POST['geo_lan']);		
			if(isset($_POST['monitorapi']))
				$getDataSql['monitorapi'] = Clean::str($_POST['monitorapi']);			
			if(isset($_POST['skin']))
				$getDataSql['skin'] = Clean::str($_POST['skin']);			
			if(isset($_POST['root']))
				$getDataSql['root'] = Clean::str($_POST['root']);		
			if(isset($_POST['url']))
				$getDataSql['url'] = Clean::str($_POST['url']);			
			if(isset($_POST['countviewpageonu']))
				$getDataSql['countviewpageonu'] = Clean::int($_POST['countviewpageonu']);
			foreach($config as $val => $data_config){
				if(!empty($getDataSql[$val]) && $getDataSql[$val]!==$config[$val]){
					$db->SQLupdate('config',['value'=>$getDataSql[$val],'update'=>$time],['name'=>$val]);
				}
			}
			if(is_array($getDataSql) && !empty($USER['id']))
				pmonlog(['types'=>'config','userid'=>$USER['id'],'message'=> $lang['edit_config']]);
		}
		$go->redirect('config');	
	break;	
	case 'savepondog': // додаємо в чергу на перевірку комутатор
		if(!empty($USER['class']) && $USER['class']>=6){	
			if(isset($_POST['deviceid']))
				$getDataSql['oltid'] = Clean::int($_POST['deviceid']);	
			if(!empty($getDataSql['oltid']))
				$getDevice = $db->Fast('swcron','*',['oltid'=>$getDataSql['oltid']]);	
			if(!$getDevice['id']){		
				$getDataSql['added'] = $time;
				$getDataSql['status'] = 'yes';
				if(!empty($getDataSql['oltid'])){
					$db->SQLinsert('swcron',$getDataSql);
					$jobid = $db->getInsertId();
					$db->SQLupdate('switch',['jobid'=>$jobid,'status'=>'go'],['id'=>$getDataSql['oltid']]);
				}
			}	
		}
		$go->redirect('pondog');	
	break;	
	case 'newoid':	// додаємо в базу новий оід
		if(isset($_POST['oidid']))
			$getDataSql['oidid'] = Clean::text($_POST['oidid']);
		if(!empty($getDataSql['oidid'])){
			$getOID = $db->Simple("SELECT * FROM `equipment` WHERE `oidid` = '".(int)$getDataSql['oidid']."' LIMIT 1");
			if(!empty($getOID['name'])){
				$getDataSql['model'] = mb_strtolower($getOID['name']);	
				if(isset($_POST['types']))
					$getDataSql['types'] = Clean::text($_POST['types']);		
				if(isset($_POST['inf']))
					$getDataSql['inf'] = Clean::text($_POST['inf']);
				if(isset($_POST['format']))
					$getDataSql['format'] = Clean::text($_POST['format']);
				if(isset($_POST['descr']))
					$getDataSql['descr'] = Clean::text($_POST['descr']);
				if(isset($_POST['oid']))
					$getDataSql['oid'] = Clean::text($_POST['oid']);
				if(preg_match('/epon/i',$_POST['pon'])){
					$getDataSql['pon'] = 'epon';			
				}elseif(preg_match('/gpon/i',$_POST['pon'])){
					$getDataSql['pon'] = 'gpon';
				}else{
					$getDataSql['types'] = Clean::text($_POST['pon']);
				}
				if(!empty($getDataSql['oid']) && !empty($getDataSql['oidid']) && !empty($getDataSql['types']) && !empty($getDataSql['inf']))
					$db->SQLinsert('oid',$getDataSql);
			}
		}
		$go->redirect('oid');
	break;	
	case 'deletuser':	// видалення користувача	
		if(!empty($USER['class']) && $USER['class']>=6){
			if(isset($_POST['id']))
				$getDataSql['id'] = Clean::int($_POST['id']);
			$getus = $db->Fast('users','*',['id'=>$getDataSql['id']]);	
			if(!empty($getus['id']))
				$db->SQLdelete('users',['id' => $getus['id']]);
			$go->redirect('users');		
		}
	break;		
	case 'delgroup':	// видалення групи	
		if(!empty($USER['class']) && $USER['class']>=6){
			if(isset($_POST['id']))
				$getDataSql['id'] = Clean::int($_POST['id']);
			$getgroup = $db->Fast($PMonTables['gr'],'*',['id'=>$getDataSql['id']]);	
			if(!empty($getgroup['id']))
				$db->SQLdelete($PMonTables['gr'],['id' => $getgroup['id']]);
			$go->redirect('group');		
		}
	break;		
	case 'delgroupdev':	// видалення групи	
		if(!empty($USER['class']) && $USER['class']>=6){
			if(isset($_POST['id']))
				$getDataSql['id'] = Clean::int($_POST['id']);
			$getgroupswitch = $db->Fast($PMonTables['switch'],'*',['id'=>$getDataSql['id']]);	
			$db->SQLupdate($PMonTables['switch'],['groups'=>0],['id'=>$getgroupswitch['id']]);
			$go->go('/?do=group&id='.$getgroupswitch['groups']);
		}
		$go->redirect('group');	
	break;		
	case 'deletdevice':	// видалення пристроя	
		if(!empty($USER['class']) && $USER['class']>=6){
			if(isset($_POST['id']))
				$getDataSql['id'] = Clean::int($_POST['id']);
			$getDev = $db->Fast('switch','*',['id'=>$getDataSql['id']]);	
			if(!empty($getDev['id'])){
				$db->SQLdelete('switch',['id' => $getDev['id']]);
				$db->SQLdelete('onus',['olt' => $getDev['id']]);
				$db->SQLdelete('swcron',['oltid' => $getDev['id']]);
				$db->SQLdelete('switch_log',['deviceid' => $getDev['id']]);
				$db->SQLdelete('switch_port_err',['deviceid' => $getDev['id']]);
				$db->SQLdelete('switch_port',['deviceid' => $getDev['id']]);
				$db->SQLdelete('switch_photo',['deviceid' => $getDev['id']]);
				$db->SQLdelete('switch_pon',['oltid' => $getDev['id']]);				
			}
			$go->redirect('main');		
		}
	break;	
	case 'updateuser':	// обновлення інформації про користувача
		if(!empty($USER['class']) && $USER['class']>=6){
			if(isset($_POST['id']))
				$getDataWhere['id'] = Clean::int($_POST['id']);
			$getus = $db->Fast('users','*',['id'=>$getDataWhere['id']]);	
			if(!empty($getus['id'])){
				if(isset($_POST['class']))
					$getDataSql['class'] = Clean::int($_POST['class']);
				if(isset($_POST['username']))
					$getDataSql['username'] = Clean::text($_POST['username']);		
				if(isset($_POST['name']))
					$getDataSql['name'] = Clean::text($_POST['name']);			
				if(isset($_POST['newpassword']) && isset($_POST['editpass']) && $_POST['editpass']=='on'){
					$password = Clean::text($_POST['newpassword']);	
					$getDataSql['password'] = $auth->generationHash($password);	
				}
				if(isset($_POST['setip']) && isset($_POST['onlyip']) && $_POST['onlyip']=='on'){
					$getDataSql['setip'] = Clean::text($_POST['setip']);
					$getDataSql['onlyip'] = 'on';
				}else{
					if(isset($_POST['setip']))
						$getDataSql['setip'] = Clean::text($_POST['setip']);
					$getDataSql['onlyip'] = 'off';	
				}
				if(is_array($getDataSql))	
					$db->SQLupdate('users',$getDataSql,['id'=>$getDataWhere['id']]);					
			}
		}
		$go->redirect('users');	
	break;	
	case 'markponbox':	// додамє координати понбокса
		if(!empty($USER['class']) && $USER['class']>=4){
			if(isset($_POST['unit'])){
				$getWhereSql['id'] = Clean::int($_POST['unit']);
				if(isset($_POST['lan']))
					$getDataSql['lan'] = Clean::text($_POST['lan']);
				if(isset($_POST['lon']))
					$getDataSql['lon'] = Clean::text($_POST['lon']);
				if(!empty($getDataSql['lon']) && !empty($getDataSql['lan'])){
					$getPonBox = $db->Fast($PMonTables['unitponbox'],'*',['id' => $getWhereSql['id']]);
					if(is_array($getDataSql)){	
						$db->SQLupdate($PMonTables['unitponbox'],$getDataSql,['id'=>$getWhereSql['id']]);
					}
					$go->go('/?do=pon&act=maptree&id='.$getPonBox['treeid'].'&t=ponbox');
				}
			}
		}
		$go->redirect('unit');	
	break;	
	case 'addoptik':	// додавання оптики в базу + (початок і кінець кабелю)
		if(isset($_POST['unitid']))
			$getDataSql['unitid'] = Clean::int($_POST['unitid']);		
		if(isset($_POST['locationid']))
			$getDataSql['locationid'] = Clean::int($_POST['locationid']);
		if(isset($_POST['treeid']))
			$getDataSql['treeid'] = Clean::int($_POST['treeid']);
		if(isset($_POST['typesfiber'])){
			$getDataSql['typesfiber'] = 'vol'.Clean::int($_POST['typesfiber']);
			$getDataSql['colorfiber'] = Clean::int($_POST['typesfiber']);
		}
		if(isset($_POST['getconnect']))
			$getDataSql['getconnect'] = Clean::int($_POST['getconnect']);
		if(isset($_POST['getconnectid']))
			$getDataSql['getconnectid'] = Clean::int($_POST['getconnectid']);
		if(isset($_POST['nextconnect']))
			$getDataSql['nextconnect'] = Clean::int($_POST['nextconnect']);
		if(isset($_POST['nextconnectid']))
			$getDataSql['nextconnectid'] = Clean::int($_POST['nextconnectid']);		
		if(isset($_POST['metr']))
			$getDataSql['metr'] = Clean::int($_POST['metr']);
		if(!empty($getDataSql['treeid']) && !empty($getDataSql['locationid']) && !empty($getDataSql['unitid']) && !empty($getDataSql['getconnect']) && !empty($getDataSql['getconnectid']) && !empty($getDataSql['nextconnect']) && !empty($getDataSql['nextconnectid'])){
			$getDataSql['added'] = $time;
			$db->SQLinsert($PMonTables['fiberlist'],$getDataSql);
			$fiberid = $db->getInsertId();
			if($getDataSql['getconnect']==1){
				$getFirstGeo = $db->Fast($PMonTables['unitponbox'],'*',['id' => $getWhereSql['id']]);
			}elseif($getDataSql['getconnect']==2){
				$getFirstGeo = $db->Fast($PMonTables['myfta'],'*',['id' => $getDataSql['getconnectid']]);
			}elseif($getDataSql['getconnect']==3){
				$getFirstGeo = $db->Fast($PMonTables['unitponbox'],'*',['id' => $getDataSql['getconnectid']]);
			}
			if($getDataSql['nextconnect']==1){
				$getTwoGeo = $db->Fast($PMonTables['unitponbox'],'*',['id' => $getWhereSql['id']]);
			}elseif($getDataSql['nextconnect']==2){
				$getTwoGeo = $db->Fast($PMonTables['myfta'],'*',['id' => $getDataSql['nextconnectid']]);
			}elseif($getDataSql['nextconnect']==3){
				$getTwoGeo = $db->Fast($PMonTables['unitponbox'],'*',['id' => $getDataSql['nextconnectid']]);
			}
			if(is_array($getFirstGeo) && is_array($getTwoGeo)){
				$fiberMapSQL['geo'] = '[['.$getFirstGeo['lan'].','.$getFirstGeo['lon'].'],['.$getTwoGeo['lan'].','.$getTwoGeo['lon'].']];';
				$fiberMapSQL['added'] = $time;
				$fiberMapSQL['fiberid'] = $fiberid;
				if(!empty($getDataSql['colorfiber']))
					$fiberMapSQL['color'] = $colorfiber[$getDataSql['colorfiber']];
				$db->SQLinsert($PMonTables['fibermap'],$fiberMapSQL);
				$go->go('/?do=pon&act=maptree&id='.$getDataSql['treeid']);
			}
		}
		$go->redirect('unit');	
	break;	
	case 'delfibermap':	
		if(isset($_POST['fiberid']))
			$getDataSql['fiberid'] = Clean::int($_POST['fiberid']);		
		if(isset($_POST['pon']))
			$getDataSql['pon'] = Clean::int($_POST['pon']);	
		if(!empty($USER['class']) && $USER['class']>=4){
			$db->SQLdelete($PMonTables['fiberlist'],['id'=>$getDataSql['fiberid']]);
			if(!empty($getDataSql['pon'])){
				$go->go('/?do=pon&act=fiberlist&id='.$getDataSql['pon']);
			}			
		}
		$go->go('/?do=pon&act=allfiber');	
	break;	
	case 'saveportmonitor':
		if(!empty($USER['class']) && $USER['class']>=4){
			if(isset($_POST['id']))
				$getDataSql['id'] = Clean::int($_POST['id']);
			if(!empty($getDataSql['id'])){
				$getSw = $db->Fast($PMonTables['switch'],'*',['id'=>$getDataSql['id']]);
				if(!empty($getSw['id'])){
					// monitor
					if(is_array($_POST['monitorport']))
						foreach($_POST['monitorport'] as $key1 => $portid1)
							$configport[$portid1]['id'] = (int)$portid1;
					// error
					if(is_array($_POST['monitortelegram']))
						foreach($_POST['monitortelegram'] as $key2 => $portid2)
							$configsmsport[$portid2]['id'] = (int)$portid2;
						// sms
					if(is_array($_POST['monitorerr']))
						foreach($_POST['monitorerr'] as $key3 => $portid3)
							$configerrport[$portid3]['id'] = (int)$portid3;
					$dataPortSwitch = $db->Multi($PMonTables['switchport'],'id,monitor',['deviceid'=>$getSw['id']]);
					$setupport = array();
					foreach($dataPortSwitch as $idport => $portvalue) {
						$monitor = false;
						if(!empty($configport[$portvalue['id']]['id']) && $configport[$portvalue['id']]['id']==$portvalue['id'])
							$monitor = true;
						$db->SQLupdate($PMonTables['switchport'],['monitor'=>($monitor?'yes':'no')],['deviceid'=>$getSw['id'],'id'=>$portvalue['id']]);
					}					
					foreach($dataPortSwitch as $idport => $portvalue) {
						$error = false;
						if(!empty($configerrport[$portvalue['id']]['id']) && $configerrport[$portvalue['id']]['id']==$portvalue['id'])
							$error = true;
						$db->SQLupdate($PMonTables['switchport'],['error'=>($error?'yes':'no')],['deviceid'=>$getSw['id'],'id'=>$portvalue['id']]);
					}					
					foreach($dataPortSwitch as $idport => $portvalue) {
						$sms = false;
						if(!empty($configsmsport[$portvalue['id']]['id']) && $configsmsport[$portvalue['id']]['id']==$portvalue['id'])
							$sms = true;
						$db->SQLupdate($PMonTables['switchport'],['sms'=>($sms?'yes':'no')],['deviceid'=>$getSw['id'],'id'=>$portvalue['id']]);
					}
				}
				$go->go('/?do=detail&act=olt&page=monitoring&id='.$getSw['id']);
			}
		}	
		$go->go('/?do=main');			
	break;	
	case 'delspliter':
		if(!empty($USER['class']) && $USER['class']>=4){
			if(isset($_POST['id']))
				$getDataSql['id'] = Clean::int($_POST['id']);				
			if(!empty($getDataSql['id'])){
				$getSpliter = $db->Fast($PMonTables['unitbasket'],'*',['id'=>$getDataSql['id']]);
				$db->SQLdelete($PMonTables['unitbasket'],['id'=>$getDataSql['id']]);
				$go->go('/?do=pon&act=view&id='.$getSpliter['ponboxid']);
			}
			$go->redirect('unit');				
		}			
	break;	
	case 'savegeomyfta':		
		if(isset($_POST['id']))
			$getDataWhere['id'] = Clean::int($_POST['id']);
		if(!empty($getDataWhere['id'])){
			$getM = $db->Fast($PMonTables['myfta'],'*',['id'=>$getDataWhere['id']]);
			if(isset($_POST['lan']))
				$getDataSql['lan'] = Clean::text($_POST['lan']);
			if(isset($_POST['lon']))
				$getDataSql['lon'] = Clean::text($_POST['lon']);
			if(!empty($getDataSql['lon']) && !empty($getDataSql['lan']))
				$db->SQLupdate($PMonTables['myfta'],$getDataSql,['id'=>$getM['id']]);
		}
		$go->go('/?do=pon&act=allmyft');	
	break;	
	case 'delmonitor':		
		if(!empty($USER['class']) && $USER['class']>=4){
			if(isset($_POST['idonu']))
				$getDataSql['idonu'] = Clean::int($_POST['idonu']);
			if(!empty($getDataSql['idonu'])){
				$db->SQLdelete($PMonTables['mononu'],['idonu'=>$getDataSql['idonu']]);
				$go->go('/?do=onu&id='.$getDataSql['idonu']);
			}
		}
		$go->go('/?do=mononu');
	break;	
	case 'addmonitor':		
		if(!empty($USER['class']) && $USER['class']>=4){
			if(isset($_POST['name']))
				$getDataSql['name'] = Clean::text($_POST['name']);				
			if(isset($_POST['idonu']))
				$getDataSql['idonu'] = Clean::int($_POST['idonu']);
			if(!empty($getDataSql['name']) && !empty($getDataSql['idonu'])){
				$getDataSql['added'] = $time;
				$db->SQLinsert($PMonTables['mononu'],$getDataSql);
			}
		}
		$go->go('/?do=mononu');		
	break;	
	case 'newmyft':	
		if(!empty($USER['class']) && $USER['class']>=4){
			if(isset($_POST['name']))
				$getDataSql['name'] = Clean::text($_POST['name']);				
			if(isset($_POST['location']))
				$getDataSql['locationid'] = Clean::int($_POST['location']);
			if(!empty($getDataSql['name']) && !empty($getDataSql['locationid'])){
				$getDataSql['added'] = $time;
				$db->SQLinsert($PMonTables['myfta'],$getDataSql);
			}
		}
		$go->go('/?do=pon&act=allmyft');	
	break;	
	case 'addfiber':
		if(!empty($USER['class']) && $USER['class']>=4){
			if(isset($_POST['ponbox']))
				$getDataSql['ponboxid'] = Clean::int($_POST['ponbox']);				
			if(isset($_POST['spliter']))
				$getDataSql['spliter'] = Clean::int($_POST['spliter']);
			if(!empty($getDataSql['ponboxid']) && !empty($getDataSql['spliter'])){
				$getDataSql['added'] = $time;
				$db->SQLinsert('unitbasket',$getDataSql);
				$go->go('/?do=pon&act=view&id='.$getDataSql['ponboxid']);
			}
			$go->redirect('unit');				
		}			
	break;	
	case 'newponbox':
		if(!empty($USER['class']) && $USER['class']>=4){
			if(isset($_POST['tree']))
				$getDataSql['treeid'] = Clean::int($_POST['tree']);			
			if(isset($_POST['name']))
				$getDataSql['name'] = Clean::text($_POST['name']);
			if(!empty($getDataSql['treeid']))
				$getTree = $db->Fast($PMonTables['unitpontree'],'*',['id'=>$getDataSql['treeid']]);
			if(!empty($getTree['id'])){
				$getUnit = $db->Fast($PMonTables['unit'],'*',['id'=>$getTree['unitid']]);
				$getDataSql['added'] = $time;
				$SQLCountDev = $db->Multi($PMonTables['unitponbox'],'*',['treeid'=>$getTree['id']]);
				$getDataSql['sort'] = count($SQLCountDev) + 1;
				$getDataSql['deviceid'] = $getTree['deviceid'];
				$getDataSql['unitid'] = $getTree['unitid'];
				if(!empty($getTree['portid']))
					$getDataSql['portid'] = $getTree['portid'];
				$getDataSql['locationid'] = $getUnit['location'];
				$getDataSql['types'] = 'mdu';
				$db->SQLinsert($PMonTables['unitponbox'],$getDataSql);
				$go->go('/?do=pon&act=tree&id='.$getDataSql['treeid']);	
			}
		}
		$go->redirect('unit');	
	break;	
	case 'editbox':		
		if(!empty($USER['class']) && $USER['class']>=4){
			if(isset($_POST['ponboxid']))
				$getDataWhere['ponboxid'] = Clean::int($_POST['ponboxid']);			
			if(isset($_POST['sort']))
				$getDataSql['sort'] = Clean::int($_POST['sort']);			
			if(isset($_POST['portid']))
				$getDataSql['portid'] = Clean::int($_POST['portid']);			
			if(isset($_POST['treeid']))
				$getDataSql['treeid'] = Clean::int($_POST['treeid']);				
			if(isset($_POST['gpslan']))
				$getDataSql['gpslan'] = Clean::text($_POST['gpslan']);				
			if(isset($_POST['gpslon']))
				$getDataSql['gpslon'] = Clean::text($_POST['gpslon']);			
			if(isset($_POST['name']))
				$getDataSql['name'] = Clean::text($_POST['name']);				
			if(isset($_POST['note']))
				$getDataSql['note'] = Clean::text($_POST['note']);
			if(is_array($getDataSql))	
				$db->SQLupdate($PMonTables['unitponbox'],$getDataSql,['id'=>$getDataWhere['ponboxid']]);			
			if(!empty($getDataWhere['ponboxid']))
				$go->go('/?do=pon&act=view&id='.$getDataWhere['ponboxid']);
		}
		$go->redirect('unit');	
	break;	
	case 'addonu':		
		if(!empty($USER['class']) && $USER['class']>=4){
			if(isset($_POST['ponboxid']))
				$getDataSql['ponboxid'] = Clean::int($_POST['ponboxid']);			
			if(isset($_POST['onuid']))
				$getDataSql['onuid'] = Clean::int($_POST['onuid']);
			if(!empty($getDataSql['ponboxid']) && !empty($getDataSql['onuid'])){
				$getONT = $db->Fast($PMonTables['onus'],'status',['idonu'=>$getDataSql['onuid']]);
				if(!empty($getONT['status'])){
					$getPONBOX = $db->Fast($PMonTables['ponboxonu'],'*',['ponboxid'=>$getDataSql['ponboxid'],'onuid'=>$getDataSql['idonu']]);
					if(!$getPONBOX){
						$getDataSql['added'] = $time;
						$getDataSql['status'] = $getONT['status'];
						$db->SQLinsert($PMonTables['ponboxonu'],$getDataSql);
					}
				}
				$go->go('/?do=pon&act=view&id='.$getDataSql['ponboxid'].'&add=onu');
			}
		}
		$go->redirect('unit');	
	break;	
	case 'delbox':	
		if(!empty($USER['class']) && $USER['class']>=4){
			if(isset($_POST['ponboxid']))
				$getDataSql['ponboxid'] = Clean::int($_POST['ponboxid']);
			if(!empty($getDataSql['ponboxid'])){
				$getPonBox = $db->Fast($PMonTables['unitponbox'],'*',['id' => $getDataSql['ponboxid']]);
				if(!empty($getPonBox['id'])){
					$db->SQLdelete($PMonTables['ponboxonu'],['ponboxid' => $getDataSql['ponboxid']]);
					$db->SQLdelete($PMonTables['unitponbox'],['id' => $getDataSql['ponboxid']]);
				}
				$go->go('/?do=pon&act=tree&id='.$getPonBox['treeid']);
			}
		}
		$go->redirect('unit');	
	break;	
	case 'delonu':	
		if(!empty($USER['class']) && $USER['class']>=4){
			if(isset($_POST['ponboxid']))
				$getDataSql['ponboxid'] = Clean::int($_POST['ponboxid']);			
			if(isset($_POST['onuid']))
				$getDataSql['onuid'] = Clean::int($_POST['onuid']);
			if(!empty($getDataSql['ponboxid']) && !empty($getDataSql['onuid'])){
				$getONT = $db->Fast($PMonTables['onus'],'status',['idonu'=>$getDataSql['onuid']]);
				if(!empty($getONT['status'])){
					$getPONBOX = $db->Fast($PMonTables['ponboxonu'],'*',['ponboxid'=>$getDataSql['ponboxid'],'onuid'=>$getDataSql['idonu']]);
					if(!$getPONBOX){
						$getDataSql['added'] = $time;
						$getDataSql['status'] = $getONT['status'];
						$db->SQLinsert($PMonTables['ponboxonu'],$getDataSql);
					}
				}
				$go->go('/?do=pon&act=view&id='.$getDataSql['ponboxid'].'&add=onu');
			}
		}
		$go->redirect('unit');
	break;	
	case 'newuser':	
		if(!empty($USER['class']) && $USER['class']>=6){
			if(isset($_POST['class']))
				$getDataSql['class'] = Clean::int($_POST['class']);
			if(isset($_POST['username']))
				$getDataSql['username'] = Clean::text($_POST['username']);		
			if(isset($_POST['name']))
				$getDataSql['name'] = Clean::text($_POST['name']);			
			if(isset($_POST['setip']))
				$getDataSql['setip'] = Clean::text($_POST['setip']);
			if(isset($_POST['onlyip']) && $_POST['onlyip']=='on')
				$getDataSql['onlyip'] = 'on';
			if(isset($_POST['password'])){
				$password = Clean::text($_POST['password']);	
				$getDataSql['password'] = $auth->generationHash($password);
			}			
			if(isset($_POST['mail']))
				$getDataSql['email'] = Clean::text($_POST['mail']);
			if(is_array($getDataSql))
				$db->SQLinsert('users',$getDataSql);
		}
		$go->redirect('users');	
	break;	
	case 'saveswitch':	
		if(isset($_POST['devicemodel']))
			$getDataSql['model'] = Clean::text($_POST['devicemodel']);		
		if(isset($_POST['sn']))
			$getDataSql['sn'] = Clean::text($_POST['sn']);
		if(isset($_POST['port']))
			$getDataSql['port'] = Clean::int($_POST['port']);
		$getDataSql['added'] = $time;
		if(!empty($getDataSql['model']) && !empty($getDataSql['port']) && !empty($getDataSql['sn']))
			$db->SQLinsert('sklad_switch',$getDataSql);
	break;		
	case 'unitsavedevice':	
		if(!empty($USER['class']) && $USER['class']>=5){
			if(isset($_POST['deviceid']))
				$getDataSql['deviceid'] = Clean::int($_POST['deviceid']);		
			if(isset($_POST['unit']))
				$getDataSql['unitid'] = Clean::int($_POST['unit']);
			$getDataSql['added'] = $time;
			if(!empty($getDataSql['deviceid']) && !empty($getDataSql['unitid'])){
				$getCurentADD = $db->Fast($PMonTables['unitdevice'],'id',['deviceid'=>$getDataSql['deviceid'],'unitid'=>$getDataSql['unitid']]);
				if(!$getCurentADD['id']){
					$insert = true;
					$SQLCountDev = $db->Multi($PMonTables['unitdevice'],'*',['unitid'=>$getDataSql['unitid']]);
					$getDataSql['sort'] = count($SQLCountDev) + 1;
				}
			}
			if($insert){
				$db->SQLinsert($PMonTables['unitdevice'],$getDataSql);
				pmonlog(['types'=>'users','userid'=>$USER['id'],'message'=> $lang['log_add_device_unit']]);
			}
			$go->go('/?do=pon&act=viewunit&id='.$getDataSql['unitid']);	
		}
		$go->redirect('unit');		
	break;		
	case 'addpontree':	
		if(!empty($USER['class']) && $USER['class']>=5){
			if(isset($_POST['id']))
				$getDataSql['unitid'] = Clean::int($_POST['id']);				
			if(isset($_POST['deviceid']))
				$getDataSql['deviceid'] = Clean::int($_POST['deviceid']);			
			if(isset($_POST['nametree']))
				$getDataSql['name'] = Clean::text($_POST['nametree']);	
			if(!empty($getDataSql['unitid']) && !empty($getDataSql['deviceid']) && !empty($getDataSql['name'])){
				$getDataSql['added'] = $time;
				$SQLCountDev = $db->Multi($PMonTables['unitpontree'],'*',['unitid'=>$getDataSql['unitid']]);
				$getDataSql['sort'] = count($SQLCountDev) + 1;
				$db->SQLinsert($PMonTables['unitpontree'],$getDataSql);
				$go->go('/?do=pon&act=viewunit&id='.$getDataSql['unitid'].'&types=tree');	
			}
		}
		$go->redirect('unit');
	break;		
	case 'delunitswitch':	
		if(!empty($USER['class']) && $USER['class']>=5){
			if(isset($_POST['unitid']))
				$getDataSql['unitid'] = Clean::int($_POST['unitid']);			
			if(isset($_POST['deviceid']))
				$getDataSql['deviceid'] = Clean::int($_POST['deviceid']);		
			if(isset($_POST['type']))
				$getDataSql['type'] = Clean::int($_POST['type']);
			if(!empty($getDataSql['unitid']) && !empty($getDataSql['deviceid']) && !empty($getDataSql['type'])){
				if($getDataSql['type']==3){
					$db->SQLdelete($PMonTables['unitdevice'],['deviceid' => $getDataSql['deviceid'],'unitid'=>$getDataSql['unitid']]);
				}				
				$go->go('/?do=pon&act=viewunit&id='.$getDataSql['unitid']);	
			}
		}
		$go->redirect('unit');	
	break;		
	case 'saveups':	
		if(isset($_POST['devicemodel']))
			$getDataSql['model'] = Clean::text($_POST['devicemodel']);		
		if(isset($_POST['sn']))
			$getDataSql['sn'] = Clean::text($_POST['sn']);
		if(isset($_POST['power']))
			$getDataSql['power'] = Clean::int($_POST['power']);	
		$getDataSql['added'] = $time;	
		if(!empty($getDataSql['model']) && !empty($getDataSql['power']) && !empty($getDataSql['sn']))	
			$db->SQLinsert('sklad_battery',$getDataSql);
	break;		
	case 'savenoteunit':
		if(!empty($USER['class']) && $USER['class']>=6){	
			if(isset($_POST['id']))
				$getDataWhereSql['id'] = Clean::int($_POST['id']);	
			if(isset($_POST['note']))
				$getDataSql['note'] = Clean::text($_POST['note']);	
			if(!empty($getDataWhereSql['id'])){
				$getUnit = $db->Fast($PMonTables['unit'],'*',['id'=>$getDataWhereSql['id']]);
				if(!empty($getUnit['id'])){
					$db->SQLupdate($PMonTables['unit'],$getDataSql,['id'=>$getUnit['id']]);
					$go->go('/?do=pon&act=viewunit&id='.$getUnit['id']);	
				}
			}
		}
		$go->redirect('unit');
	break;		
	case 'editfibermap':
		if(isset($_POST['fiberid']))
			$getDataWhere['fiberid'] = Clean::int($_POST['fiberid']);
		if(isset($_POST['geo']))
			$geo = trim(Clean::text($_POST['geo']), ',');;
		if($geo && $getDataWhere['fiberid']){
			$sqlgeo = '['.$geo.'];';
			$db->SQLupdate($PMonTables['fibermap'],['geo'=>$sqlgeo],['fiberid'=>$getDataWhere['fiberid']]);
		}	
	break;		
	case 'savegeounit':	
		if(isset($_POST['id']))
			$getDataWhere['id'] = Clean::int($_POST['id']);
		if(!empty($getDataWhere['id'])){
			$getLoca = $db->Fast('unit','*',['id'=>$getDataWhere['id']]);
			if(isset($_POST['lan']))
				$getDataSql['lan'] = Clean::text($_POST['lan']);
			if(isset($_POST['lon']))
				$getDataSql['lon'] = Clean::text($_POST['lon']);
			if(!empty($getDataSql['lon']) && !empty($getDataSql['lan'])){
				$db->SQLupdate('unit',$getDataSql,['id'=>$getLoca['id']]);
			}
			$go->go('/?do=pon&act=viewunit&id='.$getDataWhere['id']);	
		}
		$go->redirect('main');
	break;		
	case 'savegeolocation':	
		if(isset($_POST['id']))
			$getDataWhere['id'] = Clean::int($_POST['id']);
		if(!empty($getDataWhere['id'])){
			$getLoca = $db->Fast('location','*',['id'=>$getDataWhere['id']]);
			if(isset($_POST['lan']))
				$getDataSql['lan'] = Clean::text($_POST['lan']);
			if(isset($_POST['lon']))
				$getDataSql['lon'] = Clean::text($_POST['lon']);
			if(!empty($getDataSql['lon']) && !empty($getDataSql['lan'])){
				$db->SQLupdate($PMonTables['location'],$getDataSql,['id'=>$getLoca['id']]);
			}
			$go->go('/?do=location&id='.$getDataWhere['id']);	
		}
		$go->redirect('main');
	break;		
	case 'saveportdescr':
		if(isset($_POST['descrport']))
			$getDataSql['descrport'] = Clean::text($_POST['descrport']);
		if(isset($_POST['id']))
			$getDataSql['id'] = Clean::int($_POST['id']);
		if(!empty($getDataSql['descrport']) && !empty($getDataSql['id'])){
			$db->SQLupdate('switch_port',['descrport'=>$getDataSql['descrport']],['id'=>$getDataSql['id']]);
		}
		if(!empty($getDataSql['id'])){
			$getPort = $db->Fast('switch_port','deviceid',['id'=>$getDataSql['id']]);
			$go->go('/?do=detail&act=olt&page=connect&id='.$getPort['deviceid']);	
		}else{
			$go->redirect('main');
		}
	break;		
	case 'saveportsetup':
		if(!empty($USER['class']) && $USER['class']>=6){	
			$getDataSql['error'] = ($_POST['error']=='yes'?'yes':'no');
			$getDataSql['sms'] = ($_POST['sms']=='yes'?'yes':'no');
			$getDataSql['monitor'] = ($_POST['monitor']=='yes'?'yes':'no');
			if(isset($_POST['id']))
				$getDataSql['id'] = Clean::int($_POST['id']);
			if(!empty($getDataSql['id'])){
				$getPort = $db->Fast('switch_port','id,deviceid',['id'=>$getDataSql['id']]);
				if($getPort['id']==$getDataSql['id']){
					$db->SQLupdate('switch_port',$getDataSql,['id'=>$getDataSql['id']]);
					$go->go('/?do=detail&act=olt&page=connect&id='.$getPort['deviceid']);
				}
			}
		}			
		$go->redirect('main');
	break;		
	case 'newunit':
		if(!empty($USER['class']) && $USER['class']>=5){	
			if(isset($_POST['name'])) 
				$SQLinsert['name'] = Clean::text($_POST['name']);		
			if(isset($_POST['location'])) 
				$SQLinsert['location'] = Clean::int($_POST['location']);
			if(!empty($SQLinsert['location'])){
				$SQLgetLocation = $db->Fast($PMonTables['location'],'*',['id'=>$SQLinsert['location']]);
				if(!empty($SQLgetLocation['name'])){
					$SQLinsert['locationname'] = $SQLgetLocation['name'];
					if(!empty($SQLinsert['name'])){
						$db->SQLinsert($PMonTables['unit'],$SQLinsert);
						pmonlog(['types'=>'users','userid'=>$USER['id'],'message'=> $lang['log_addnew_unit'].$SQLinsert['name']]);
					}
				}			
			}	
		}
		$go->redirect('unit');		
	break;		
	case 'stekport':	
		if(!empty($USER['class']) && $USER['class']>=4){		
			if(isset($_POST['portid'])) 
				$SQLupdate['portid'] = Clean::int($_POST['portid']);			
			if(isset($_POST['id'])) 
				$SQLinsert['id'] = Clean::int($_POST['id']);
			if(!empty($SQLinsert['id']) && !empty($SQLupdate['portid'])){
				$db->SQLupdate($PMonTables['unitpontree'],$SQLupdate,['id'=>$SQLinsert['id']]);
				$go->go('/?do=pon&act=tree&id='.$SQLinsert['id']);
			}
		}
		$go->redirect('unit');	
	break;		
	case 'delconnect':	
		if(!empty($USER['class']) && $USER['class']>=6){
			if(isset($_POST['id'])) 
				$getDataSql['id'] = Clean::int($_POST['id']);			
			$getConnect = $db->Fast('connect_port','*',['id'=>$getDataSql['id']]);
			if(!empty($getConnect['id'])){
				$db->SQLdelete('connect_port',['id' => $getConnect['id']]);
				$go->go('/?do=detail&act=olt&id='.$getConnect['curd']);
			}
			$go->redirect('main');	
		}
	break;		
	case 'saveconnect':	
		if(!empty($USER['class']) && $USER['class']>=6){	
			$getDataSql = array();
			if(isset($_POST['curp'])) 
				$getDataSql['curp'] = Clean::int($_POST['curp']);		
			if(isset($_POST['cursfp'])) 
				$getDataSql['cursfp'] = Clean::int($_POST['cursfp']);			
			if(isset($_POST['connsfp'])) 
				$getDataSql['connsfp'] = Clean::int($_POST['connsfp']);			
			if(isset($_POST['curd'])) 
				$getDataSql['curd'] = Clean::int($_POST['curd']);		
			if(isset($_POST['connp'])) 
				$getDataSql['connp'] = Clean::int($_POST['connp']);			
			if(isset($_POST['connd'])) 
				$getDataSql['connd'] = Clean::int($_POST['connd']);	
			if(!empty($getDataSql['connd']) && !empty($getDataSql['connp']) && !empty($getDataSql['curp']) && !empty($getDataSql['curd'])){
				$db->SQLinsert($PMonTables['connectport'],['cursfp' => $getDataSql['cursfp'],'connsfp' => $getDataSql['connsfp'],'connd' => $getDataSql['connd'],'connp'=>$getDataSql['connp'],'curd'=>$getDataSql['curd'],'curp'=>$getDataSql['curp']]);
				$db->SQLinsert($PMonTables['connectport'],['cursfp' => ($getDataSql['connsfp']??0),'connsfp' => ($getDataSql['cursfp']??0),'connd' => $getDataSql['curd'],'connp'=>$getDataSql['curp'],'curd'=>$getDataSql['connd'],'curp'=>$getDataSql['connp']]);
				$go->go('/?do=detail&act=olt&id='.$getDataSql['curd']);		
			}
		}
		$go->redirect('main');
	break;		
	case 'savesfp':	
		if(!empty($USER['class']) && $USER['class']>=6){	
			if(isset($_POST['dist']))
				$SQLinsert['dist'] = Clean::int($_POST['dist']);			
			if(isset($_POST['speed']))
				$SQLinsert['speed'] = Clean::int($_POST['speed']);		
			if(isset($_POST['wavelength']))
				$SQLinsert['wavelength'] = Clean::int($_POST['wavelength']);	
			if(isset($_POST['model'])) 
				$SQLinsert['model'] = Clean::text($_POST['model']);	
			if(isset($_POST['connector'])) 
				$SQLinsert['connector'] = Clean::text($_POST['connector']);	
			if(isset($_POST['types'])) 
				$SQLinsert['types'] = Clean::text($_POST['types']);	
			if(!empty($SQLinsert['types']) && !empty($SQLinsert['wavelength']) && !empty($SQLinsert['connector']) && !empty($SQLinsert['dist']) && !empty($SQLinsert['speed'])){
				$db->SQLinsert($PMonTables['sfp'],$SQLinsert);
			}
		}
		$go->go('/?do=sklad&act=sfp');
	break;	
	case 'delphoto':
		if(!empty($USER['class']) && $USER['class']>=6){		
			if(isset($_GET['id']))
				$id = Clean::int($_GET['id']);	
			$getPhoto = $db->Fast('switch_photo','*',['id'=>$id]);
			if(!empty($getPhoto['deviceid'])){
				$db->SQLdelete('switch_photo',['id' => $getPhoto['id']]);
				@unlink('file/photo/'.$getPhoto['photo']);
				$go->go('/?do=detail&act=olt&id='.$getPhoto['id'].'&page=photo');
		}
		}
		$go->redirect('main');
	break;	
	case 'savephoto':
		if(!empty($USER['class']) && $USER['class']>=6){	
		$file = true; 
		if(isset($_POST['id']))
			$id = Clean::int($_POST['id']);	
		if(isset($_POST['name'])) 
			$SQLinsert['name'] = Clean::text($_POST['name']);	
		if(isset($_POST['note'])) 
			$SQLinsert['note'] = Clean::text($_POST['note']);	
		if(!empty($_FILES['file']['name']) && $id) {
			if (!array_key_exists($_FILES['file']['type'], $allowed_types) )
				$file = false; 
			if (!preg_match('/^(.+)\.(jpg|jpeg|png)$/si', $_FILES['file']['name']) )
				$file = false; 
			if($file && !empty($SQLinsert['name'])){
				$newname = substr(md5(uniqid(rand(), true)), 0, rand(7, 13)).'.'.$allowed_types[$_FILES['file']['type']];
				$uploaddir = "file/photo/";
				$ifile = $_FILES['file']['tmp_name'];
				$copy = copy($ifile, $uploaddir.$newname);
				if(!$copy){
					
				}else{
					$SQLinsert['deviceid'] = $id;
					$SQLinsert['photo'] = $newname;
					$SQLinsert['added'] = $time;
					$db->SQLinsert('switch_photo',$SQLinsert);
				}
				$go->go('/?do=detail&act=olt&id='.$id.'&page=photo');
			}
		}
		}
		$go->redirect('main');		
	break;	
	case 'deletlocation':
		if(!empty($USER['class']) && $USER['class']>=6){		
			if(isset($_POST['id']))
				$id = Clean::int($_POST['id']);	
			$getLoc = $db->Fast($PMonTables['location'],'*',['id'=>$id]);
			if(!empty($getLoc['id']))
				$db->SQLdelete($PMonTables['location'],['id' => $getLoc['id']]);
		}
		$go->redirect('location');	
	break;	
	case 'addgroup':		
		if(!empty($USER['class']) && $USER['class']>=6){	
			if(isset($_POST['name'])) 
				$SQLinsert['name'] = Clean::text($_POST['name']);
			$SQLinsert['added'] = $time;
			if(!empty($SQLinsert['name']))
				$db->SQLinsert($PMonTables['gr'],$SQLinsert);
		}
		$go->redirect('group');
	break;	
	case 'newlocation':	
		if(!empty($USER['class']) && $USER['class']>=6){	
			if(isset($_POST['name'])) 
				$SQLinsert['name'] = Clean::text($_POST['name']);	
			if(isset($_POST['note'])) 
				$SQLinsert['note'] = Clean::text($_POST['note']);
			if(!empty($_FILES['file']['name']) && $id) {
				if (!array_key_exists($_FILES['file']['type'], $allowed_types) )
					$file = false; 
				if (!preg_match('/^(.+)\.(jpg|jpeg|png)$/si', $_FILES['file']['name']) )
					$file = false; 
				if($file){
					$newname = substr(md5(uniqid(rand(), true)), 0, rand(7, 13)).'.'.$allowed_types[$_FILES['file']['type']];
					$uploaddir = "file/photo/";
					$ifile = $_FILES['file']['tmp_name'];
					$copy = copy($ifile, $uploaddir.$newname);
					if(!$copy){
					
					}else{
						$SQLinsert['photo'] = $newname;
					}
				}
			}
			$SQLinsert['added'] = $time;
			if(!empty($SQLinsert['name']))
				$db->SQLinsert($PMonTables['location'],$SQLinsert);
		}
		$go->redirect('location');	
	break;	
	case 'saveeditlocation':	
		if(!empty($USER['class']) && $USER['class']>=6){		
			if(isset($_POST['id']))
				$id = Clean::int($_POST['id']);	
			$getLoc = $db->Fast('location','*',['id'=>$id]);
			if(!empty($getLoc['id'])){
				if(isset($_POST['name'])) 
					$SQLupdate['name'] = Clean::text($_POST['name']);	
				if(isset($_POST['note'])) 
					$SQLupdate['note'] = Clean::text($_POST['note']);
				if(!empty($_FILES['file']['name']) && $id) {
					if (!array_key_exists($_FILES['file']['type'], $allowed_types) )
						$file = false; 
					if (!preg_match('/^(.+)\.(jpg|jpeg|png)$/si', $_FILES['file']['name']) )
						$file = false; 
					if($file){
						$newname = substr(md5(uniqid(rand(), true)), 0, rand(7, 13)).'.'.$allowed_types[$_FILES['file']['type']];
						$uploaddir = "file/photo/";
						$ifile = $_FILES['file']['tmp_name'];
						$copy = copy($ifile, $uploaddir.$newname);
						if(!$copy){
						
						}else{
							$SQLupdate['photo'] = $newname;
						}
					}
				}
				$db->SQLupdate($PMonTables['location'],$SQLupdate,['id'=>$getLoc['id']]);
				$go->go('/?do=location&id='.$id);
			}
		}
		$go->redirect('location');
	break;	
	case 'deletsfp':
		if(!empty($USER['class']) && $USER['class']>=6){	
			if(isset($_POST['id']))
				$id = Clean::int($_GET['id']);	
		}
	break;		
	case 'saveuid':		
		if(isset($_POST['id']))
			$getDataSql['id'] = Clean::int($_POST['id']);			
		if(isset($_POST['uid']))
			$getDataSql['uid'] = Clean::int($_POST['uid']);	
		if(!empty($getDataSql['uid']) && !empty($getDataSql['id'])){
			$db->SQLupdate($PMonTables['onus'],['uid'=>$getDataSql['uid']],['idonu'=>$getDataSql['id']]);
			$go->go('/?do=onu&id='.$getDataSql['id']);
		}
		$go->redirect('main');
	break;	
	case 'savetag':		
		if(isset($_POST['id']))
			$getDataSql['id'] = Clean::int($_POST['id']);			
		if(isset($_POST['tag']))
			$getDataSql['tag'] = Clean::text($_POST['tag']);	
		if(!empty($getDataSql['tag']) && !empty($getDataSql['id'])){
			$db->SQLupdate($PMonTables['onus'],['tag'=>$getDataSql['tag']],['idonu'=>$getDataSql['id']]);
			$go->go('/?do=onu&id='.$getDataSql['id']);
		}
		$go->redirect('main');
	break;		
	case 'savebilling':	
		if(isset($_POST['billing']))
			$getDataSql['billing'] = Clean::str($_POST['billing']);			
		if(isset($_POST['billingtype']))
			$getDataSql['billingtype'] = Clean::str($_POST['billingtype']);			
		if(isset($_POST['billingurl']))
			$getDataSql['billingurl'] = Clean::str($_POST['billingurl']);			
		if(isset($_POST['billingapikey']))
			$getDataSql['billingapikey'] = Clean::str($_POST['billingapikey']);
		if(is_array($getDataSql)){
			foreach($getDataSql as $conf => $value){
				if($conf){
					$SQLconf = $db->Fast($PMonTables['config'],'id',['name'=>$conf]);
					if(!empty($SQLconf['id'])){
						$db->SQLupdate($PMonTables['config'],['value'=>$value],['id'=>$SQLconf['id']]);
					}
				}
			}
		}
		$go->redirect('billing');	
	break;		
	case 'savesetup':
		if(!empty($USER['class']) && $USER['class']>=6){	
		if(isset($_POST['id']))
			$getDataSql['id'] = Clean::int($_POST['id']);		
		if(isset($_POST['location'])){
			$getDataSql['location'] = Clean::int($_POST['location']);
			$dataLocation = $db->Fast($PMonTables['location'],'*',['id'=>$getDataSql['location']]);
			if(!empty($dataLocation['name'])){
				$getDataSql['locationname'] = $dataLocation['name'];
			}
		}
		$dataSwitch = $db->Fast('switch','*',['id'=>$getDataSql['id']]);
		if(!$dataSwitch['id'])
			$go->redirect('main');
		if(isset($_POST['place']))
			$getDataSql['place'] = Clean::text($_POST['place']);
		if(isset($_POST['mac']))
			$getDataSql['mac'] = Clean::text($_POST['mac']);
		if(isset($_POST['sn']))
			$getDataSql['sn'] = Clean::text($_POST['sn']);
		$getDataSql['groups'] = Clean::int($_POST['group']);
		if($_POST['monitor']=='yes'){
			$getDataSql['monitor'] = 'yes';
			switchLog($getDataSql['id'],'switch',$lang['enable_monitor']);
		}else{
			$getDataSql['monitor'] = 'no';
			switchLog($getDataSql['id'],'switch',$lang['disable_monitor']);
		}		
		if($_POST['connect']=='yes'){
			$getDataSql['connect'] = 'yes';
		}else{
			$getDataSql['connect'] = 'no';
		}		
		if($_POST['gallery']=='yes'){
			$getDataSql['gallery'] = 'yes';
		}else{
			$getDataSql['gallery'] = 'no';
		}
		if(isset($_POST['typecheck'])){
			$getDataSql['typecheck'] = Clean::text($_POST['typecheck']);
			switchLog($getDataSql['id'],'switch',$lang['inserttimemonitor'].' '.$getDataSql['typecheck']);			
		}			
		if(isset($_POST['netip']))
			$getDataSql['netip'] = Clean::text($_POST['netip']);
		if(!empty($dataSwitch['id'])){
			$db->SQLupdate($PMonTables['switch'],$getDataSql,['id'=>$getDataSql['id']]);
			$go->go('/?do=setup&id='.$getDataSql['id']);
		}	
		}		
		$go->redirect('main');
	break;		
	case 'saveaccess':	
		if(!empty($USER['class']) && $USER['class']>=6){
			if(isset($_POST['id']))
				$getDataSql['id'] = Clean::int($_POST['id']);
			$dataSwitch = $db->Fast('switch','*',['id'=>$getDataSql['id']]);
			if(!empty($dataSwitch['id'])){
				if(isset($_POST['netip']))
					$getDataSql['netip'] = Clean::text($_POST['netip']);				
				if(isset($_POST['snmpro']))
					$getDataSql['snmpro'] = Clean::text($_POST['snmpro']);			
				if(isset($_POST['private']))
					$getDataSql['snmprw'] = Clean::text($_POST['private']);			
				if(isset($_POST['username']))
					$getDataSql['username'] = Clean::text($_POST['username']);			
				if(isset($_POST['password']))
					$getDataSql['password'] = Clean::text($_POST['password']);
				if(is_array($getDataSql))
					$db->SQLupdate($PMonTables['switch'],$getDataSql,['id'=>$getDataSql['id']]);
				$go->go('/?do=detail&act='.$dataSwitch['device'].'&id='.$dataSwitch['id']);
			}
		}			
		$go->redirect('main');
	break;		
	case 'savebattery':	
		if(isset($_POST['devicemodel']))
			$getDataSql['model'] = Clean::text($_POST['devicemodel']);		
		if(isset($_POST['sn']))
			$getDataSql['sn'] = Clean::text($_POST['sn']);
		if(isset($_POST['volt']))
			$getDataSql['volt'] = Clean::int($_POST['volt']);
		if(isset($_POST['amper']))
			$getDataSql['amper'] = Clean::int($_POST['amper']);		
		if(isset($_POST['types']))
			$getDataSql['types'] = Clean::int($_POST['types']);
		$getDataSql['added'] = $time;
		if(!empty($getDataSql['model']) && !empty($getDataSql['amper']) && !empty($getDataSql['volt']) && !empty($getDataSql['sn']))	
			$db->SQLinsert('sklad_battery',$getDataSql);		
	break;	
}
die;
?>