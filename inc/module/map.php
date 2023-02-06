<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$id = isset($_GET['id']) ? Clean::int($_GET['id']) : null;
if($act=='vyzol' && $id){
	$SQLListFiber = $db->Multi($PMonTables['fibermap']);
	if(count($SQLListFiber)){
		foreach($SQLListFiber as $fiber){
			$sqlFiber = $db->Fast($PMonTables['fiberlist'],'*',['id'=>$fiber['fiberid']]);
			$fiber1 .='var fiber'.$fiber['fiberid'].' = '.$fiber['geo'];				
			$fiber2 .="var polyline = L.polyline(fiber".$fiber['fiberid'].",{color:'".(!empty($fiber['color'])?$fiber['color']:'#fff')."',opacity: 0.9})
			.bindTooltip('<b>Тип оптики: </b>".$lang[$sqlFiber['typesfiber']]."<br>".(!empty($sqlFiber['metr']) ? "<b>Довжина кабелю: </b>".$sqlFiber['metr']."м<br>":"")."<b>Встановлено: </b>".$sqlFiber['added']."')";
			#$fiber2 .=".bindPopup('<a href=\"#\" onclick=\"viewunit(\'editfibermap\',".$fiber['id'].",ajaxmenu);\">змінити монтаж</a>')";
			$fiber2 .=".addTo(map);";				
		}
	}
	$SQLListPonbox = $db->Multi($PMonTables['unitponbox'],'*',['unitid'=>$id]);
	if(count($SQLListPonbox)){
		foreach($SQLListPonbox as $ponbox){
			if(!empty($ponbox['lan']) && !empty($ponbox['lon'])){
				$komplekt = komplektmdu($ponbox['id']);
				$listFiber = listfibermdu($ponbox['id']);
				$listONU = listonumdu($ponbox['id']);
				$marker .='L.marker(['.$ponbox['lan'].','.$ponbox['lon'].'],{icon:'.imgmdu($ponbox).'}).bindPopup("<a class=fontmap href=\"/?do=pon&act=view&id='.$ponbox['id'].'\">'.$ponbox['name'].'<img src=\"../style/img/link.png\"><\/a>'.($listONU?$listONU:'').($komplekt?$komplekt:'').($listFiber?$listFiber:'').'").addTo(map); ';
			}
		}
	}
}elseif($act=='switch'){
	
}else{
	
}
$gpsLan = ($SQLUnit['lan']?$SQLUnit['lan']:$config['geo_lan']);
$gpsLon = ($SQLUnit['lon']?$SQLUnit['lon']:$config['geo_lon']);
if($act=='vyzol')
	$polygon = 'map.fitBounds(polyline.getBounds());';
$mapjs = <<<HTML
<script>
var markers = new Array();
var items = new Array();
var marker;
var lat = '{$gpsLan}'; 
var lon = '{$gpsLon}';
var map = L.map('map');
map.setView([lat, lon], 16);
var googleHybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{maxZoom: 16,subdomains:['mt0','mt1','mt2','mt3']});
googleHybrid.addTo(map);
{$marker}{$typescript}{$fiber1}{$fiber2}
{$polygon}
map.on('click', function(e) {
	$('#ajaxmenu').removeClass('hide'); 
	$('#markerPanel').removeClass('hide'); 
});
</script>
HTML;
$blockurl ='
<STYLE>
.hide{
  display:none;
}
.marker_panel{
  position: fixed;
}
.panelmap span:hover {
    cursor: pointer;
    background: #fff;
	border: 1px solid tomato;
}
.panelmap span {
	    border: 1px solid #fff;
    width: 100%;
    margin: 2px;
    padding: 3px 10px 3px 4px;
    background: #fff;
    border-radius: 3px;
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    align-content: center;
    justify-content: flex-start;
    transition: all 0.3s cubic-bezier(0.26, 0.46, 0.45, 0.95) 0s;
    font-size: 13px;
    align-items: center;
}
.panelmap span img {
    width: 30px;
    height: 30px;
    margin-right: 5px;
    padding: 2px;
}
.panelmap b a{
    font-weight: 600;
    color: #6472ab;
}
.panelmap {
    display: flex;
    flex-wrap: wrap;
    align-content: center;
    justify-content: flex-start;
    align-items: flex-start;
    min-width: 600px;
    text-align: center;
}
.ponmap-dropdown {
    position: relative;
    display: inline-block;
}
.ponmap-dropdown-content {
    display: none;
    position: absolute;
    z-index: 1;
}
.ponmap-dropdown:hover .ponmap-dropdown-content {display: block;}
.ponmap-dropdown:hover .ponmap-dropbtn {
    background-color: #fff;
    cursor: pointer;
    box-shadow: 0px 8px 7px 0px rgb(0 0 0 / 20%);
}
.ponmap-dropbtn {
    background-color: #ffffff;
    color: #6d87b7;
    padding: 3px 5px 3px 5px;
	border-radius: 3px;
    min-width: 70px;
    height: 36px;
    line-height: 29px;
    font-size: 13px;
    font-weight: 600;
    margin: 0 15px 5px 0px;
}
.panelmap .ponmap-dropdown .ponmap-dropbtn img {
    position: relative;
    vertical-align: middle;
    margin-right: 5px;
    height: 30px;
}
.ponmap-dropdown-content-flex {
    display: flex;
}
</STYLE>';
$mapdiv = '<div id="map" style="height:600px;width: 100%;"></div><div id="markerPanel" class="marker_panel hide"><div class="panelmap"><div class="ponmap-dropdown"><div class="ponmap-dropbtn">Вузли</div><div class="ponmap-dropdown-content"><div class="ponmap-dropdown-content-flex">';
$SQLListUni = $db->Multi($PMonTables['unit']);
	if(count($SQLListUni)){
		foreach($SQLListUni as $unit){
			$mapdiv .= '<span id="new_marker"><img src="../style/img/graph_claw.png"><b><a href="/?do=map&act=vyzol&id='.$unit['id'].'">'.$unit['name'].'</a></b></span>';
		}
	}		
$mapdiv .= '</div></div></div></div></div>';
#$mapdiv = '<div id="map" style="height:600px;width: 100%;margin-left: 255px;"></div>';
$tpl->load_template('map/main.tpl');
$tpl->set('{blockurl}',$blockurl);
$tpl->set('{mapjs}',$mapjs);
$tpl->set('{mapdiv}',$mapdiv);
$tpl->set('{result}',$tpl->result['map'].'<div id="maps"></div>');
$tpl->set('{speedbar}',$speedbar);
$tpl->compile('content');
$tpl->clear();