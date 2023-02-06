<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$result = '';
$metatags = array('title'=>'PMON Project','description'=>'PMON Project','page'=>'main');
// search form
$userinf = '<div class="main-user"><h2>'.$lang['class_'.(!isset($USER['class'])?1:$USER['class'])].'</h2></div>';
$result .='<div class="mainblocksearch"><form id="form"><input type="hidden" name="do" value="search"><input type="hidden" name="act" value="search"><input class="query" type="search" id="query" name="search" placeholder="mac,sn,tag..."><button>'.$lang['lang_search'].'</button></form>'.$userinf.'</div>';
// stats block
$dataCountallONU = $db->Multi('onus');
if(count($dataCountallONU)){
	$dataCountonlineONU = $db->Multi('onus','*',['status'=>1]);
	$result .='<div id="mainstats"><div class="blockstats">';
	$result .='<a href="/" class="blockminstats space-x-3 transition-transform"><span class="blmsicon"><i class="fi fi-rr-computer" style="color: #c1a43c;"></i></span><span class="blmscon"><span class="textmin">'.$lang['allonus'].'</span><span class="textmax">'.count($dataCountallONU).'</span></span></a>';
	if(count($dataCountonlineONU)){
		$result .='<a href="/" class="blockminstats space-x-3 transition-transform"><span class="blmsicon"><i class="fi fi-rr-users" style="color: #1ced1c;"></i></span><span class="blmscon"><span class="textmin">'.$lang['allonile'].'</span><span class="textmax">'.count($dataCountonlineONU).'</span></span></a>';
	}	
	$dataCountofflineONU = (int)count($dataCountallONU)-count($dataCountonlineONU);
	if(count($dataCountallONU)){	
		$result .='<a href="/" class="blockminstats space-x-3 transition-transform"><span class="blmsicon"><i class="fi fi-rr-power" style="color:red;"></i></span><span class="blmscon"><span class="textmin">'.$lang['alloffile'].'</span><span class="textmax">'.$dataCountofflineONU.'</span></span></a>';	
	}
	$dataCountBadSignalONU = $db->SimpleWhile("SELECT idonu FROM onus WHERE rx BETWEEN '-28' AND '-50' AND status = 1");
	if(count($dataCountBadSignalONU)){
		$result .='<a href="/" class="blockminstats space-x-3 transition-transform"><span class="blmsicon"><i class="fi fi-rr-time-fast" style="color: grey;"></i></span><span class="blmscon"><span class="textmin">'.$lang['allbadrx'].'</span><span class="textmax">'.count($dataCountBadSignalONU).'</span></span></a>';
	}
	if(count($checkLicenseSwitch)){
		$result .='<a href="/" class="blockminstats space-x-3 transition-transform"><span class="blmsicon"><i class="fi fi-rr-credit-card" style="color: #24b9dd;"></i></span><span class="blmscon"><span class="textmin">'.$lang['allsw'].'</span><span class="textmax">'.count($checkLicenseSwitch).'</span></span></a>';	
	}
	$SQLonusCount = $db->Simple('SELECT SUM(newin) as countin FROM `switch_port_err` WHERE added  >= curdate()');
	if(!empty($SQLonusCount['countin'])){
		$result .='<a href="/" class="blockminstats space-x-3 transition-transform"><span class="blmsicon"><i class="fi fi-rr-shuffle" style="color: tomato;"></i></span><span class="blmscon"><span class="textmin">'.$lang['allerr'].'</span><span class="textmax" style="color: tomato;">+'.$SQLonusCount['countin'].'</span></span></a>';
	}
	$result .='</div></div>';
}
// end stats block
// start group block
	$SQLListlocation = $db->Multi($PMonTables['gr']);
	if(count($SQLListlocation)){
	$result .='<div class="card"><div class="group_list">';
		foreach($SQLListlocation as $group){
			$result .='<div class="group_url"><a href="/?do=device&group='.$group['id'].'"><i class="fi fi-rr-folder"></i>'.$group['name'].'</a></div>';
		}
	$result .='</div>';
	$result .='</div>';
	}
// end group block
// Graph online/offline block
// Graph online/offline block
	$SQLGrSt = $db->Multi($PMonTables['pmonstats']);
	if(count($SQLGrSt)){
		foreach($SQLGrSt as $phar){
			$js_array_time .= '"'.str_replace('2023-','',$phar['datetime']).'",';
			$js_array_online .= '"'.$phar['online'].'",';
			$js_array_offline .= '"'.$phar['offline'].'",';
		}
		$js_array_time = trim($js_array_time, ',');
		$js_array_online = trim($js_array_online, ',');
		$js_array_offline = trim($js_array_offline, ',');
		$result .='<div class="card">';
		$result .= '
		<script src="../style/js/Chart.min.js"></script>
		<div style="width: 99%;margin: auto;"><canvas id="myChart" style="height: 300px; width: 600px;"></canvas></div>

    <script>
		var ctx = document.getElementById("myChart").getContext("2d");
		var myChart = new Chart(ctx, {
          type: "line",
          data: {
            labels: ['.$js_array_time.'],
            datasets: [
				{data: ['.$js_array_online.'],label: "Онлайн",borderColor: "#3cba9f",backgroundColor:"rgb(202 233 217 / 77%)"},
				{data: ['.$js_array_offline.'],label: "Оффлайн",borderColor: "#c45850",backgroundColor:"#f4baba"}
            ]
          },
        });
    </script>';
		$result .='</div>';
	}
// Graph online/offline block


$tpl->load_template('main/main.tpl');
$tpl->set('{block-main}','<div class="mainadmin">'.$result.'</div>');
$tpl->compile('content');
$tpl->clear();
?>