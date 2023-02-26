<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$result = '';
$metatags = array('title'=>'Project PMon4','description'=>'Project PMon4','page'=>'main');
$userinf = '<div class="main-user"><h2>'.$lang['class_'.(!isset($USER['class'])?1:$USER['class'])].'</h2></div>';
$result .='<div class="mainblocksearch"><form id="form"><input type="hidden" name="do" value="search"><input type="hidden" name="act" value="search"><input class="query" type="search" id="query" name="search" placeholder="mac,sn,tag..." autocomplete="off"><button>'.$lang['lang_search'].'</button></form>'.$userinf.'</div>';
$countall = $db->Multi($PMonTables['onus']);
if(count($countall)){
	$countallonu = $db->Multi($PMonTables['onus'],'*',['status'=>1]);
	$result .='<div id="mainstats"><div class="blockstats">';
	$sqlnewonutoday = $db->SimpleWhile('SELECT idonu FROM `'.$PMonTables['onus'].'` WHERE added  >= curdate()');
	if(count($sqlnewonutoday)){
		$result .='<a href="/?do=search&search=&selectolt=0&selectpon=all&selectdist=all&selectsignal=all&act=search" class="blockminstats space-x-3 transition-transform"><span class="blmsicon"><img src="../style/img/mainnew.png"></span><span class="blmscon"><span class="textmin">'.$lang['newonu'].'</span><span class="textmax" style="color:#41e541;">'.count($sqlnewonutoday).'</span></span></a>';
	}
	$result .='<a href="/?do=search&search=&selectolt=0&selectpon=all&selectdist=all&selectsignal=all&act=search" class="blockminstats space-x-3 transition-transform"><span class="blmsicon"><img src="../style/img/mainall.png"></span><span class="blmscon"><span class="textmin">'.$lang['allonus'].'</span><span class="textmax">'.count($countall).'</span></span></a>';
	if(count($countallonu)){
		$result .='<a href="/?do=search&search=&selectolt=0&selectpon=all&selectdist=all&selectsignal=all&act=search&onlyactive=on" class="blockminstats space-x-3 transition-transform"><span class="blmsicon"><img src="../style/img/mainon.png"></span><span class="blmscon"><span class="textmin">'.$lang['allonile'].'</span><span class="textmax">'.count($countallonu).'</span></span></a>';
	}	
	$countofflineonu = (int)count($countall)-count($countallonu);
	if(count($countall)){	
		$result .='<a href="/" class="blockminstats space-x-3 transition-transform"><span class="blmsicon"><img src="../style/img/mainoff.png"></span><span class="blmscon"><span class="textmin">'.$lang['alloffile'].'</span><span class="textmax">'.$countofflineonu.'</span></span></a>';	
	}
	$countbadrxonu = $db->SimpleWhile("SELECT idonu FROM ".$PMonTables['onus']." WHERE rx BETWEEN '-".$config['badsignalstart']."' AND '-".$config['badsignalend']."' AND status = 1");
	if(count($countbadrxonu)){
		$result .='<a href="/?do=search&search=&selectolt=0&selectpon=all&selectdist=all&selectsignal=small&act=search&onlyactive=on" class="blockminstats space-x-3 transition-transform"><span class="blmsicon"><img src="../style/img/mainrx.png"></span><span class="blmscon"><span class="textmin">'.$lang['allbadrx'].'</span><span class="textmax">'.count($countbadrxonu).'</span></span></a>';
	}
	if(count($checkLicenseSwitch)){
		$result .='<a href="/?do=device" class="blockminstats space-x-3 transition-transform"><span class="blmsicon"><img src="../style/img/switch.png"></span><span class="blmscon"><span class="textmin">'.$lang['allsw'].'</span><span class="textmax">'.count($checkLicenseSwitch).'</span></span></a>';	
	}
	$counttodayerror = $db->Simple('SELECT SUM(newin) as countin FROM `'.$PMonTables['porterror'].'` WHERE added  >= curdate()');
	if(!empty($counttodayerror['countin'])){
		$result .='<a href="/?do=porterror" class="blockminstats space-x-3 transition-transform"><span class="blmsicon"><img src="../style/img/sfperr.png"></span><span class="blmscon"><span class="textmin">'.$lang['allerr'].'</span><span class="textmax" style="color: tomato;">+'.$counttodayerror['countin'].'</span></span></a>';
	}
	$result .='</div></div>';
}
//////////////
if(count($checkLicenseSwitch)){
$switchid  = $switchid  ?? null;
$switchid = (isset($_GET['switchid']) ? Clean::int($_GET['switchid']) : null);
$result .='<div class="main-stats-tab">';
foreach($checkLicenseSwitch as $deviceoid){
	$result .='<a href="/?switchid='.$deviceoid['id'].'" class="'.($switchid==$deviceoid['id']?'tab-active':'tab-url').'">'.($deviceoid['ping']=='down'?'<i class="fi fi-rr-info" style="color: red;"></i>':'<i class="fi fi-rr-settings"></i>').''.$deviceoid['place'].'</a>';
}
$result .='</div>';
if(is_numeric($switchid)){
	$switchdata = $db->Fast('switch','*',['id'=>$switchid]);
	if(!empty($switchdata['id'])){
		$result .='<div class="card"><div class="mainblockadmin"><div class="block1">';
		#$result .= blockStatsONU($switchdata['id']);
		$sqlpingtoday = $db->SimpleWhile('SELECT * FROM `'.$PMonTables['pingstats'].'` WHERE system = '.$switchdata['id'].' AND datetime  >= curdate() ORDER BY datetime ASC');
		if(count($sqlpingtoday)>3){
			$result .= '<h2>Статистика пінгування SNMP</h2><img src="../?do=graphping&id='.$switchdata['id'].'">';	
		}
		if($switchdata['ping']=='down'){
			$result .='<div>'.$lang['empty_snmp_ping'].' <b>'.$switchdata['timeping'].'</b></div>';
		}
		$result .='</div><div class="block1">';
		$sqlalldevice = $db->SimpleWhile('SELECT * FROM `monitoring` WHERE deviceid = '.$switchdata['id'].' AND datetime  >= curdate() ORDER BY datetime ASC');
		if(count($sqlalldevice)){
			$result .= '<h2>Пареметри комутатора CPU,Temp</h2><img src="../?do=graphhealth&id='.$switchdata['id'].'">';	
		}
		$result .='</div>
		</div></div>';
	}
}}
//////////////
$stats = (isset($_GET['stats']) ? Clean::str($_GET['stats']) : null);
if($stats=='yesterday'){
	$sqlgraph = $db->SimpleWhile("SELECT * FROM ".$PMonTables['pmonstats']." WHERE datetime > DATE_ADD(NOW(), INTERVAL -1 DAY)");
}elseif($stats=='week'){
	$sqlgraph = $db->SimpleWhile("SELECT * FROM ".$PMonTables['pmonstats']." WHERE datetime > DATE_ADD(NOW(), INTERVAL -7 DAY)");	
}elseif($stats=='clear'){	
	$db->query('TRUNCATE '.$PMonTables['pmonstats']);
	header('Location: /');
}elseif($stats=='month'){	
	$sqlgraph = $db->SimpleWhile("SELECT * FROM ".$PMonTables['pmonstats']." WHERE datetime > DATE_ADD(NOW(), INTERVAL -30 DAY)");
}else{
	$sqlgraph = $db->SimpleWhile("SELECT * FROM ".$PMonTables['pmonstats']." WHERE datetime >= curdate()");
	$stats = 'curday';	
}
if(count($sqlgraph)){
$js_array_time = $js_array_time ?? null;
$js_array_online = $js_array_online ?? null;
$js_array_offline = $js_array_offline ?? null;
$arr_badrx = $arr_badrx ?? null;
foreach($sqlgraph as $phar){
	$js_array_time .= '"'.str_replace('2023-','',$phar['datetime']).'",';
	$js_array_online .= '"'.(!empty($phar['online'])?$phar['online']:0).'",';
	$js_array_offline .= '"'.(!empty($phar['offline'])?$phar['offline']:0).'",';
	$arr_badrx .= '"'.(!empty($phar['badsignal'])?$phar['badsignal']:0).'",';
}
$result .='<div class="card"><div class="main-stats-tab">';
$result .='<a href="/?do=main" class="'.($stats=='curday'?'tab-active':'tab-url').'"><i class="fi fi-rr-time-twenty-four"></i>'.$lang['day_curent'].'</a>';
$result .='<a href="/?do=main&stats=yesterday" class="'.($stats=='yesterday'?'tab-active':'tab-url').'"><i class="fi fi-rr-clock"></i>'.$lang['day_later'].'</a>';
$result .='<a href="/?do=main&stats=week" class="'.($stats=='week'?'tab-active':'tab-url').'"><i class="fi fi-rr-clock"></i>'.$lang['day_week'].'</a>';
$result .='<a href="/?do=main&stats=month" class="'.($stats=='month'?'tab-active':'tab-url').'"><i class="fi fi-rr-clock"></i>'.$lang['day_m'].'</a>';
if(checkAccess(4)){
	$result .='<a href="/?do=main&stats=clear" class="tab-url" style="color:red;"><i class="fi fi-rr-delete"></i>'.$lang['clears'].'</a>';
}
$result .='</div>';
$result .='<script src="../style/js/Chart.min.js"></script><div style="width:99%;margin: auto;"><canvas id="myChart" style="height: 180px; width: 600px;"></canvas></div><script>';
$result .='var ctx = document.getElementById("myChart").getContext("2d");';
$result .='var myChart = new Chart(ctx,{type:"bar",data:{labels:['. trim($js_array_time, ',').'],datasets:[{data: ['.trim($js_array_online, ',').'],label:"'.$lang['online'].'",borderColor: "#3cba9f",backgroundColor:"#1ced1c"},{data: ['.trim($js_array_offline, ',').'],label: "'.$lang['offline'].'",borderColor: "#c45850",backgroundColor:"rgb(115 139 179 / 77%)"},{data: ['.trim($arr_badrx,',').'],label: "'.$lang['bad_signal'].'",borderColor: "#c45850",backgroundColor:"tomato"}]},});';
$result .='</script></div>';
}
$tpl->load_template('main/main.tpl');
$tpl->set('{block-main}','<div class="mainadmin">'.$result.'</div>');
$tpl->compile('content');
$tpl->clear();
?>