<?php
define('AJAX',true);
define('GRAPH',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
if($id){
$select = $db->Multi($PMonTables['historyrx'],'*',['onu' => $id]);
if(count($select)){
	foreach($select as $arr){
		$onudata = date_parse_from_format('Y-m-d h:i:s',$arr['datetime']);
		$js_array_full .= '[Date.UTC('.$onudata['year'].','.$onudata['month'].','.$onudata['day'].','.$onudata['hour'].','.$onudata['minute'].','.$onudata['second'].'),'.$arr['signal'].'],';
	}
	#$js_array_full = trim($js_array, ',');
}
$graph = <<<HTML
<div id="container" style="height: 400px; min-width: 600px"></div>
<script language="javascript" type="text/javascript" src="../../style/source/jquery.js"></script>
<script src="http://code.highcharts.com/stock/highstock.js"></script>
<script src="http://code.highcharts.com/stock/modules/exporting.js"></script>
<script>
var signallist = [
	{$js_array_full}
];
$(function() {
	$('#container').highcharts('StockChart',{
	    chart: {
	        plotBorderWidth: 1
	    },
	    rangeSelector: {
	    	selected: 1
	    },
		navigator: {
	    	series: {
            type: 'line'
	    	}
	    },	    
	    yAxis: {
	    	startOnTick: true,
	    	endOnTick: false
	    },
	    series: [{
	        name: 'Signal',
	        data: signallist
	    }]
	});
});
</script>
HTML;


echo $graph;
}