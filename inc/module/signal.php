<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$id = isset($_GET['id']) ? Clean::int($_GET['id']) : null;
if(!$id)
	$go->redirect('main');	
$dataONT = $db->Fast('onus','*',['idonu'=>$id]);
$metatags = array('title'=>$dataONT['type'].' '.$dataONT['inface'].' '.$lang['pt_onu'],'description'=>$lang['pd_onu'],'page'=>'onu');
if(!$dataONT['idonu'])
	$go->redirect('main');	
$dataOLT = $db->Fast('switch','*',['id'=>$dataONT['olt']]);
if(!$dataOLT['id'])
	$go->redirect('main');	
$tpl->load_template('onu/mainsignal.tpl');
$tpl->set('{id}',$id);
$select = $db->SimpleWhile('SELECT * FROM `'.$PMonTables['historyrx'].'` WHERE onu = '.$id.' ORDER BY datetime ASC');
if(count($select)){
	foreach($select as $arr){
		$js_array .= '['.(strtotime($arr['datetime'].' + 2 hour')*1000).','.$arr['signal'].'],';
	}
	$js_array_full = trim($js_array, ',');
}
$graph = <<<HTML
<script>
var signalarray = [$js_array_full];
$(function() {
	$('#container').highcharts('StockChart',{
	    chart: {plotBorderWidth: 1 },
	    rangeSelector: {selected: 1 },
		navigator: {series: {type: 'line'}},	    
	    yAxis: {startOnTick: true,endOnTick: false},
	    series: [{name: 'Signal',data: signalarray}]
	});
});
</script>
HTML;
if(!empty($dataONT['mac']))
	$mac_ont = '<span class="n">MAC</span><span class="m">'.$dataONT['mac'].'</span>';
if(!empty($dataONT['sn']))
	$sn_ont = '<span class="n">SN</span><span class="m">'.$dataONT['sn'].'</span>';
$tpl->set('{number_ont}',$mac_ont.$sn_ont);
$tpl->set('{olt_id}',$dataOLT['id']);
$tpl->set('{olt_model}',trim($dataOLT['inf']).' '.$dataOLT['model']);
$tpl->set('{olt_place}',$dataOLT['place']);
$dataONTPort = $db->Fast('switch_pon','*',['sfpid'=>$dataONT['portolt'],'oltid'=>$dataONT['olt']]);
$tpl->set('{port_id}',$dataONTPort['id']);
$tpl->set('{inface_ont}',mb_strtoupper(trim($dataONT['type']).' '.$dataONT['inface']));
$tpl->set('{type_ont}',trim($dataONT['type']));
$tpl->set('{inface}',$dataONT['inface']);
$tpl->set('{olt_port_ont}',mb_strtoupper($dataONT['type'].' '.cl_inface($dataONT['inface'])));
$tpl->set('{jsgraph}',$graph);
$tpl->compile('content');
$tpl->clear();
?>