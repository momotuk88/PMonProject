<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$speedbar .='<a class="brmhref" href="/?do=pon&act=unit"><i class="fi fi-rr-network"></i>Вузли зв`язку</a>';
switch($act){
	case 'unit';
		$SQLCount = $db->Multi($PMonTables['unit']);
		$head = 'Unit';
		list($pagertop,$pagerbottom,$limit,$offset) = pager($config['countviewpageonu'],count($SQLCount),'////url');
		$SQLList = $db->Multi($PMonTables['unit'],'*',null,null,$offset,$limit);
		if(count($SQLList)){
			foreach($SQLList as $unit){
				$tpl->load_template('pon/list-unit.tpl');
				$tpl->set('{id}',$unit['id']);
				$tpl->set('{url}','/?do=pon&act=viewunit&id='.$unit['id'].'');
				$tpl->set('{name}',$unit['name']);
				$tpl->set('{locationname}',$unit['locationname']);
				$tpl->set('{locationid}',$unit['location']);
				$photo = ($unit['photo']?'/file/unit/'.$unit['logo']:'/style/img/unit.jpg');
				$tpl->set('{photocss}','style="background-image: url('.$photo.'"));"');
				$tpl->set('{port}',$unit['port']);
				$tpl->set('{inf}','');
				$tpl->compile('pon');
				$tpl->clear();				
			}
		}else{
			$tpl->load_template('pon/empty.tpl');
			$tpl->compile('pon');
			$tpl->clear();	
		}
	break;	
	case 'view';
		$id = isset($_GET['id']) ? Clean::int($_GET['id']) : null;
		$add = isset($_GET['add']) ? Clean::text($_GET['add']) : null;
		$arrayONT = '';
		if(!$id)
			$go->redirect('unit');	
		$dataPonBox = $db->Fast($PMonTables['unitponbox'],'*',['id'=>$id]);
		if(!$dataPonBox['id'])
			$go->redirect('unit');		
		$speedbar .='<a class="brmhref" href="/?do=pon&act=viewunit&id='.$dataPonBox['unitid'].'"><i class="fi fi-rr-angle-left"></i>Вузол</a>';
		$speedbar .='<a class="brmhref" href="/?do=pon&act=tree&id='.$dataPonBox['treeid'].'"><i class="fi fi-rr-angle-left"></i>Гілка</a>';
		$speedbar .='<span class="brmspan"><i class="fi fi-rr-angle-left"></i>'.$dataPonBox['name'].'</span>';	
		$arrayONT = ListOntPonbox($dataPonBox['id']);
		if(is_array($arrayONT)){
			$listonu .='<div class="get-ponbox-list-ont">'.viewONUListPonBox($arrayONT,$dataPonBox['id'],$add).'</div>';
		}else{
			$listonu = infdisplay('В понбоксі відсутні термінали');
		}
		$listonuadd = '';
		if($add=='onu'){
			$listArrayOnu = loadListOnt($dataPonBox['deviceid'],$dataPonBox['portid']);
			if(is_array($listArrayOnu)){
				$listonuadd .='<div class="view-ponbox-list-ont-close"><a href="/?do=pon&act=view&id='.$id.'"><img src="../style/img/close.png">Закрити форму</a></div>';
				$listonuadd .='<div class="view-ponbox-list-ont">';
				foreach($listArrayOnu as $ont){
					$listonuadd .= viewListPonBox($ont,$id,$arrayONT);
				}
				$listonuadd .='</div>';
			}
		}
		// Оптика 
		$listfiberin = '';
		$listfiberout = '';
		$SQLListFiberIn = $db->Multi($PMonTables['fiberlist'],'*',['getconnectid'=>$id]);
		if(count($SQLListFiberIn)){
			foreach($SQLListFiberIn as $fiberin){
				$SQLbox1 = $db->Fast($PMonTables['unitponbox'],'id,name',['id'=>$fiberin['nextconnectid'],'treeid'=>$fiberin['treeid']]);
				$listfiberin .= '<div class="fiber-spliter"><div class="img">';
				$listfiberin .= '<img class="spliter" src="../style/img/pon/optical-fiber.png"></div><a href="/?do=pon&act=view&id='.$fiberin['nextconnectid'].'"><span>'.$lang[$fiberin['typesfiber']].', '.($fiberin['metr'] ? $fiberin['metr'].'м, ':'').''.$SQLbox1['name'].'</span></a></div>';
			}
		}		
		$SQLListFiberOut = $db->Multi($PMonTables['fiberlist'],'*',['nextconnectid'=>$id]);
		if(count($SQLListFiberOut)){
			foreach($SQLListFiberOut as $fiberout){
				$SQLbox2 = $db->Fast($PMonTables['unitponbox'],'id,name',['id'=>$fiberout['getconnectid'],'treeid'=>$fiberout['treeid']]);
				$listfiberout .= '<div class="fiber-spliter"><div class="img">';
				$listfiberout .= '<img class="spliter" src="../style/img/pon/optical-fiber.png"></div><a href="/?do=pon&act=view&id='.$fiberout['getconnectid'].'"><span>'.$lang[$fiberout['typesfiber']].', '.($fiberout['metr'] ? $fiberout['metr'].'м, ':'').''.$SQLbox2['name'].'</span></a></div>';
			}
		}
		// сплітери, дільники
		$spliterlist = '';
		$SQLListSpliter = $db->Multi($PMonTables['unitbasket'],'*',['ponboxid'=>$id]);
		if(count($SQLListSpliter)){
			foreach($SQLListSpliter as $spliter){
				$spliterlist .= '<div class="fiber-spliter"><div class="img">';
				if(!empty($USER['class']) && $USER['class']>=4)
					$spliterlist .= '<span class="del-icon" onclick="ajaxponcore(\'delspliter\','.$spliter['id'].')"><img src="../style/img/close.png"></span>';
				$spliterlist .= '<img class="spliter" src="../style/img/pon/spliter'.$spliter['spliter'].'.png"></div><span>'.$lang['spliter'.$spliter['spliter']].'</span></div>';
			}
		}		
		if(count($SQLListFiberIn) || count($SQLListFiberOut) || count($SQLListSpliter)){
			$fiberlist = '<div class="list-spliter">'.$spliterlist.$listfiberin.$listfiberout.'</div>';
		}else{
			$fiberlist = '';	
		}
		$tpl->load_template('pon/viewponbox.tpl');
		$tpl->set('{id}',$id);
		$tpl->set('{listonu}',$listonu);
		$tpl->set('{listonuadd}',$listonuadd);
		$tpl->set('{spliterlist}',$fiberlist);
		$head = $dataPonBox['name'];
		$tpl->compile('pon');
		$tpl->clear();		
	break;	
	case 'tree';	
		$id = isset($_GET['id']) ? Clean::int($_GET['id']) : null;
		if(!$id)
			$go->redirect('unit');	
		$dataPonTree = $db->Fast($PMonTables['unitpontree'],'*',['id'=>$id]);
		if(!$dataPonTree['id'])
			$go->redirect('unit');
		$dataPonDevice = $db->Fast($PMonTables['switch'],'*',['id'=>$dataPonTree['deviceid']]);
		$dataPonDevicePort = $db->Fast($PMonTables['switchport'],'*',['deviceid'=>$dataPonTree['deviceid'],'id'=>$dataPonTree['portid']]);
		$portinf ='<div class="tree_port">';
		$portinf .='<h1>';
		if(!empty($USER['class']) && $USER['class']>=4){
			$portinf .= '<span class="netip">'.$dataPonDevice['netip'].'</span>'; 
		}
		$portinf .=''.$dataPonDevice['inf'].' '.$dataPonDevice['model'].', '.$dataPonDevicePort['nameport'].', '.$dataPonTree['name'].'<span class="port_status_up">активний</span></h1>';
		$portinf .='<div class="sub_inf_port">';
			$portinf .='<b>Перевірено:</b><time>'.$dataPonDevicePort['updates'].'</time>';
			$portinf .='<b>Активний з:</b><time>'.$dataPonDevicePort['timeup'].'</time>';		
			$portinf .='<b>К-ть терміналів:</b><time>13</time>';		
		$portinf .='</div>';
		$portinf .='</div>';
		
		$speedbar .='<a class="brmhref" href="/?do=pon&act=viewunit&id='.$dataPonTree['unitid'].'"><i class="fi fi-rr-angle-left"></i>Вузол</a>';
		$speedbar .='<span class="brmspan"><i class="fi fi-rr-angle-left"></i>Pon Дерево: '.$dataPonTree['name'].'</span>';		
		$SQLListPonbox = $db->Multi($PMonTables['unitponbox'],'*',['treeid'=>$id]);
		if(count($SQLListPonbox)){
			$list = '<div class="list-tree-object">';
			foreach($SQLListPonbox as $ponbox){
				$ponboxArray = getStatusPonbox($ponbox['id']);
				$list .= '<div class="pon-tree-object">';
					$list .= '<div class="img-tree-object">';
						$list .= '<img src="../style/img/pon/'.(!empty($ponboxArray['statusmdu'])?$ponboxArray['statusmdu']:'mdu'.(!empty($ponbox['lan']) && !empty($ponbox['lan']) ? 'map':'').'.png').'">';	
					$list .= '</div>';	
					$list .= '<div class="name-tree-object">';
						$list .= '<a href="/?do=pon&act=view&id='.$ponbox['id'].'">'.$ponbox['name'].' </a>';
						$list .= '<div class="block_onus">';
						if(isset($ponboxArray[$ponbox['id']]['online']))
							$list .= '<div class="bl_online"><span class="nm">'.$lang['online'].'</span><span class="cout">'.count($ponboxArray[$ponbox['id']]['online']).'</span></div>';	
						if(!empty($ponboxArray[$ponbox['id']]['offline']))
							$list .= '<div class="bl_offline"><span class="nm">'.$lang['offline'].'</span><span class="cout">'.count($ponboxArray[$ponbox['id']]['offline']).'</span></div>';	
						$list .= '</div>';	
					$list .= '</div>';	
				$list .= '</div>';	
			}
			$list .= '</div>';
		}else{
			$list .= infdisplay('Треба щось додати');	
		}	
		if(!empty($USER['class']) && $USER['class']>=4){
			$ponkey .='<div class="btn-tree">';
			$ponkey .='<span onclick="ajaxponcore(\'newponbox\','.$id.')">Новий понбокс</span>';
			$ponkey .='<span onclick="ajaxponcore(\'connectport\','.$id.')">Стекування</span>';
			$ponkey .='<a href="/?do=pon&act=maptree&id='.$id.'">Карта Pon дерева</a>';
			$ponkey .='<a href="/?do=pon&act=fiberlist&id='.$id.'">Список оптики</a>';
			$ponkey .='</div>';
		}
		$head = 'Pon Дерево: '.$dataPonTree['name'];
		$tpl->load_template('pon/viewtree.tpl');
		$tpl->set('{id}',$id);
		$tpl->set('{pon-key}',$ponkey);
		$tpl->set('{list}',$portinf.$list);
		$tpl->compile('pon');
		$tpl->clear();	
	break;	
	case 'maptree';
		# ?do=pon&act=maptree&id=1&t=ponbox
		$id = isset($_GET['id']) ? Clean::int($_GET['id']) : null;
		$t = isset($_GET['t']) ? Clean::text($_GET['t']) : null;
		$SQLListPonbox = $db->Multi($PMonTables['unitponbox'],'*',['treeid'=>$id]);
		$SQLTree = $db->Fast($PMonTables['unitpontree'],'*',['id'=>$id]);
		$SQLUnit = $db->Fast($PMonTables['unit'],'*',['id'=>$SQLTree['unitid']]);
		if(count($SQLListPonbox)){
			foreach($SQLListPonbox as $ponbox){
				$js_mdu .="<span class=\"addmdujs\" onclick=\"ajaxpongeo(".$ponbox['id'].",' + lat + ',' + lon + ');\"><img src=\"../style/img/pon/".(!empty($ponbox['lan']) && !empty($ponbox['lon'])?'mark':'')."mdu.png\"><b>".$ponbox['name']."</b></span>";
				if(!empty($ponbox['lan']) && !empty($ponbox['lon'])){
					$komplekt = komplektmdu($ponbox['id']);
					$marker .='L.marker(['.$ponbox['lan'].','.$ponbox['lon'].'],{icon:ponboxmdu}).bindPopup("<div id=\"det-pon\"><a class=\"m-n-ponbox\" href=\"/?do=pon&act=view&id='.$ponbox['id'].'\">'.$ponbox['name'].'<\/a>'.(!empty($ponbox['count'])?'<br><b>К-ть ONT:</b> '.$ponbox['count']:'').($komplekt?$komplekt:'').'</div>").addTo(map); ';
				}
			}
		}else{
			$js_mdu .="<a href=\"/?do=pon&act=tree&id=".$t."\"><img src=\"../style/img/add.png\" style=\"vertical-align:bottom;margin-right: 5px;\">Додати понбокси</a>";
		}		
		$SQLListFiber = $db->Multi($PMonTables['fibermap']);
		if(count($SQLListFiber)){
			foreach($SQLListFiber as $fiber){
				$sqlFiber = $db->Fast($PMonTables['fiberlist'],'*',['id'=>$fiber['fiberid']]);
				$fiber1 .='var fiber'.$fiber['fiberid'].' = '.$fiber['geo'];				
				$fiber2 .="var polyline = L.polyline(fiber".$fiber['fiberid'].",{color:'".(!empty($fiber['color'])?$fiber['color']:'#fff')."',clickable: 'true'})
				.bindTooltip('<b>Тип оптики: </b>".$lang[$sqlFiber['typesfiber']]."<br>".(!empty($sqlFiber['metr']) ? "<b>Довжина кабелю: </b>".$sqlFiber['metr']."м<br>":"")."<b>Встановлено: </b>".$sqlFiber['added']."')
				.bindPopup('<span onclick=\"viewunit(\'editfibermap\',".$fiber['id'].",\'ajax-list\');\">змінити монтаж</span>')
				.addTo(map);";				
			}
		}
		$marker_myfta = mapmyfta($SQLUnit['location']);
// додавання понбоксів на карту
if($t=='ponbox'){
$typescript = <<<HTML
function onMapClick(e) {
	var lat = e.latlng.lat.toFixed(6);
	var lon = e.latlng.lng.toFixed(6);	
	popup.setLatLng(e.latlng).setContent('<div id="result_box" class="fixed-add-ponbox">{$js_mdu}</div>').openOn(map);
}
map.on('click', onMapClick);
HTML;
}elseif($t=='fiber'){
// додавання волокон між понбоксами
$typescript = <<<HTML
itochka = 1;
map.on('click', function(e) {
marker = new L.Marker(e.latlng, {icon: cube1, draggable:false});
map.addLayer(marker);
markers.push(marker);
var longMarker = markers.length;
var copyCode = new Array();
var geolist = new Array();
if(markers.length > 1 ){
	for (i = 0; i < markers.length; i++) { 
		copyCode.push(markers[i].getLatLng());
	}		
	var polyline = L.polyline(copyCode, { color: '#7bf445', clickable: 'true'}).addTo(map);
}	
var geolist = [e.latlng.lat.toFixed(6),e.latlng.lng.toFixed(6)];
$('#formgeolist').append('<input id="polegeo" type="hidden" name="geo[' + itochka + ']" value="' + geolist + '" />');
$('#formgeo').append('<div class="tochka"><span>' + itochka + '</span>' + geolist + '</div>');
itochka++
});
$(document).ready(function() {
  $(".listfiber").click(function() {
	$.post(pmon_root+'ajax/ajaxrezerv.php',
		function(response) { 
			$("#draggable").html(response);
		}, "html");
  });
});
HTML;
}else{
	
}
$gpsLan = ($SQLUnit['lan']?$SQLUnit['lan']:$config['geo_lan']);
$gpsLon = ($SQLUnit['lon']?$SQLUnit['lon']:$config['geo_lon']);	
$mapjs = <<<HTML
<script>
var markers = new Array();
var marker;
var lat = '{$gpsLan}'; 
var lon = '{$gpsLon}';
var get_myfta = 'get_myfta'; 
var get_ponbox = 'getponbox'; 
var get_camera = 'get_camera'; 
var get_bk = 'get_bk'; 
var get_ubnt = 'get_ubnt'; 
var get_wifi = 'get_wifi'; 
var map = L.map('map');
map.setView([lat, lon], 17);
var googleHybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{
    maxZoom: 19,
    subdomains:['mt0','mt1','mt2','mt3']
});
googleHybrid.addTo(map);
var popup = L.popup();	
{$marker}
{$typescript}
{$fiber1}
{$fiber2}
{$marker_myfta}
</script>
HTML;
		$head = 'Карта покриття';
		$speedbar .='<span class="brmspan"><i class="fi fi-rr-angle-left"></i>Вузол '.$dataUnit['name'].'</span>';
		$tpl->load_template('pon/maptree.tpl');
		if(!empty($USER['class']) && $USER['class']>=4){
			$ponkey .='<div class="btn-tree">';
			$ponkey .='<a href="/?do=pon&act=tree&id='.$id.'">Повернутися</a>';
			$ponkey .='<a href="/?do=pon&act=maptree&id='.$id.'&t=ponbox">Додати на карту понбокс</a>';
			$ponkey .='<span onclick="viewunit(\'addoptik\','.$id.')">Додати на карту оптику</span>';
			$ponkey .='<a href="/?do=pon&act=fiberlist&id='.$id.'">Список оптики</a>';
			$ponkey .='</div>';
		}
		$tpl->set('{mapjs}',$mapjs);
		$tpl->set('{urlmaptree}',$ponkey);
		$tpl->compile('pon');
		$tpl->clear();
	break;	
	case 'maper';
		$id = isset($_GET['id']) ? Clean::int($_GET['id']) : null;
		$head = 'Карта';
		$SQLUnit = $db->Fast($PMonTables['unit'],'*',['id'=>$id]);
		if(!$SQLUnit['id'])
			$go->redirect('unit');
		$speedbar .='<span class="brmspan"><i class="fi fi-rr-angle-left"></i>Вузол '.$dataUnit['name'].'</span>';		
		$fibers = mapfibers($id);
		$marker = mapponbox($id);
		$marker_myfta = mapmyfta($SQLUnit['location']);
		$tpl->load_template('pon/maper.tpl');
$gpsLan = ($SQLUnit['lan']?$SQLUnit['lan']:$config['geo_lan']);
$gpsLon = ($SQLUnit['lon']?$SQLUnit['lon']:$config['geo_lon']);
$mapjs = <<<HTML
<script>
var markers = new Array();
var marker;
var lat = '{$gpsLan}'; 
var lon = '{$gpsLon}';
var map = L.map('map');
map.setView([lat, lon], 17);
var googleHybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{maxZoom: 19,subdomains:['mt0','mt1','mt2','mt3']});
googleHybrid.addTo(map);
{$marker}
{$typescript}
{$fibers['var']}
{$fibers['fiber']}
{$marker_myfta}
</script>
HTML;
		$tpl->set('{id}',$id);
		$tpl->set('{mapjs}',$mapjs);
		$tpl->set('{urlmaptree}',$ponkey);
		$tpl->compile('pon');
		$tpl->clear();
	break;	
	case 'allfiber';
		$head = 'Вся оптика';
		$speedbar .='<span class="brmspan"><i class="fi fi-rr-angle-left"></i>Оптика мережі</span>';
		$SQLListFiber = $db->Multi($PMonTables['fiberlist']);
		if(count($SQLListFiber)){
			$list = '';
			foreach($SQLListFiber as $fiber){
				$list .= '<div class="fiber">';
				$SQLConn1 = $db->Fast($PMonTables['unitponbox'],'id,name',['id'=>$fiber['getconnectid']]);
				$list .= '<span class="type_first"><img class="ml" src="../style/img/map-cable.png">'.$SQLConn1['name'].'<div class="arrow"></div></span>';
				$list .= '<span style="opacity:0.4;margin-right:10px;"><img src="../style/img/conn1.png"></span>';
				$list .= '<span class="color_fiber fiber_color_'.$fiber['colorfiber'].'">оптика '.$fiber['colorfiber'].'</span>';
				$list .= '<span class="type_fiber">'.$lang[$fiber['typesfiber']].',</span>';
				$list .= '<span class="type_metr">'.$fiber['metr'].'м,</span>';
				$list .= '<span style="opacity:0.4;margin-right:10px;"><img src="../style/img/conn2.png"></span>';
				$SQLConn2 = $db->Fast($PMonTables['unitponbox'],'id,name',['id'=>$fiber['nextconnectid']]);
				$list .= '<span class="type_end"><img class="ml" src="../style/img/map-cable.png">'.$SQLConn2['name'].'</span>';
				$list .= '<span class="type_panel">
				<span onclick="editfibermap(\'editfibermap\','.$fiber['id'].',\'ajaxlist\')">Змінити монтаж</span>
				<span onclick="viewunit(\'delfibermap\','.$fiber['id'].')">Демонтувати</span>
				</span>';
				$list .= '</div>';
			}
		}else{
			$list = infdisplay('нема оптики');
		}
		$tpl->load_template('pon/allfiber.tpl');
		$tpl->set('{list}',$list);
		$tpl->compile('pon');
		$tpl->clear();
	break;	
	case 'myfta';	
	
	break;	
	case 'allmyft';	
		$head = 'Муфти';
		$speedbar .='<span class="brmspan"><i class="fi fi-rr-angle-left"></i>Муфти</span>';
		if(!empty($USER['class']) && $USER['class']>=4){
			$ponkey .='<div class="btn-tree">';
			$ponkey .='<span onclick="ajaxponcore(\'newmyft\',1)">Нова муфта</span>';
			#$ponkey .='<a href="/?do=pon&act=maptree&id='.$id.'">Ромістити на карті</a>';
			$ponkey .='</div>';
		}
		$tpl->load_template('pon/allmyft.tpl');
		$SQLListFiber = $db->Multi($PMonTables['myfta']);
		$list = '';
		if(count($SQLListFiber)){
			$list .= '<div class="list-myft">';
			foreach($SQLListFiber as $myfta){
				$list .= '<div id="myfta_'.$myfta['id'].'" class="myfta">';
				$list .= '<div class="img"><img class="ml" src="../style/ponmap/myfta.png"></div>';
				$list .= '<div class="dec">';
				if(!empty($myfta['lan']) && !empty($myfta['lon'])){
					$list .= '<a class="link" href="/?do=pon&act=myfta&id='.$myfta['id'].'">'.$myfta['name'].'</a>';
				}else{
					$list .= '<span class="link" onclick="ajaxcore(\'mapmyfta\','.$myfta['id'].')">'.$myfta['name'].'</span>';
				}
				$list .= '</div>';
				$list .= '</div>';
			}
			$list .= '</div>';
		}else{
			$list = infdisplay('Муфти відсутні');
		}
		$tpl->set('{ponkey}',$ponkey);
		$tpl->set('{list}',$list);
		$tpl->compile('pon');
		$tpl->clear();	
	break;	
	case 'fiberlist';	
		$id = isset($_GET['id']) ? Clean::int($_GET['id']) : null;
		$head = 'Список оптики яка використовується';
		$speedbar .='<span class="brmspan"><i class="fi fi-rr-angle-left"></i>Вузол '.$dataUnit['name'].'</span>';
		$tpl->load_template('pon/maptree.tpl');
		if(!empty($USER['class']) && $USER['class']>=4){
			$ponkey .='<div class="btn-tree">';
			$ponkey .='<a href="/?do=pon&act=tree&id='.$id.'">Повернутися на вузол</a>';
			$ponkey .='<a href="/?do=pon&act=maptree&id='.$id.'">Повернутися на карту дерева</a>';
			$ponkey .='</div>';
		}
		$SQLListFiber = $db->Multi($PMonTables['fiberlist'],'*',['treeid'=>$id]);
		if(count($SQLListFiber)){
			$list = '';
			foreach($SQLListFiber as $fiber){
				$list .= '<div class="fiber">';
				if($fiber['getconnect']==3){
					$SQLConn1 = $db->Fast($PMonTables['unitponbox'],'id,name',['id'=>$fiber['getconnectid'],'treeid'=>$id]);
				}elseif($fiber['getconnect']==2){
					$SQLConn1 = $db->Fast($PMonTables['myfta'],'*',['id'=>$fiber['getconnectid']]);
				}elseif($fiber['getconnect']==1){
					
				}
				$list .= '<span class="type_first"><img class="ml" src="../style/img/map-cable.png">'.$SQLConn1['name'].'<div class="arrow"></div></span>';
				$list .= '<span style="opacity:0.4;margin-right:10px;"><img src="../style/img/conn1.png"></span>';
				$list .= '<span class="color_fiber fiber_color_'.$fiber['colorfiber'].'">оптика '.$fiber['colorfiber'].'</span>';
				$list .= '<span class="type_fiber">'.$lang[$fiber['typesfiber']].',</span>';
				$list .= '<span class="type_metr">'.$fiber['metr'].'м,</span>';
				$list .= '<span style="opacity:0.4;margin-right:10px;"><img src="../style/img/conn2.png"></span>';
				if($fiber['nextconnect']==3){
					$SQLConn2 = $db->Fast($PMonTables['unitponbox'],'id,name',['id'=>$fiber['nextconnectid'],'treeid'=>$id]);
				}elseif($fiber['nextconnect']==2){
					$SQLConn2 = $db->Fast($PMonTables['myfta'],'*',['id'=>$fiber['nextconnectid']]);
				}elseif($fiber['nextconnect']==1){
					
				}
				$list .= '<span class="type_end"><img class="ml" src="../style/img/map-cable.png">'.$SQLConn2['name'].'</span>';
				$list .= '<span class="type_panel">
				<span onclick="editfibermap(\'editfibermap\','.$fiber['id'].',\'ajaxlist\')">Змінити монтаж</span>
				<span onclick="viewunit(\'delfibermap\','.$fiber['id'].')">Демонтувати</span>
				</span>';
				$list .= '</div>';
			}
		}else{
			$list = infdisplay('нема оптики');
		}
		$tpl->load_template('pon/fiberlist.tpl');
		$tpl->set('{urllist}',$ponkey);
		$tpl->set('{list}',$list);
		$tpl->compile('pon');
		$tpl->clear();
	break;	
	case 'viewunit';
		$id = isset($_GET['id']) ? Clean::int($_GET['id']) : null;
		$types = isset($_GET['types']) ? Clean::text($_GET['types']) : null;
		$tree = isset($_GET['tree']) ? Clean::text($_GET['tree']) : null;
		if(!$id)
			$go->redirect('main');	
		$dataUnit = $db->Fast($PMonTables['unit'],'*',['id'=>$id]);
		if(!$dataUnit['id'])
			$go->redirect('main');	
		$metatags = array('title'=>$dataUnit['name'],'description'=>$dataUnit['name'],'page'=>'pondetail');
		$speedbar .='<span class="brmspan"><i class="fi fi-rr-angle-left"></i>Вузол '.$dataUnit['name'].'</span>';
		$device ='';
		if(!$dataUnit['lon'] && !$dataUnit['lan'])
			$information = infdisplay('Необхідно встановити координати розташування вузла! <span style="color:red;" onclick="ajaxcore(\'mapunit\','.$id.');">Розмістити на карті</span>');
		$SQLListDev = $db->Multi($PMonTables['unitdevice'],'*',['unitid'=>$dataUnit['id']]);
		if(count($SQLListDev) && !empty($dataUnit['lon']) && !empty($dataUnit['lan'])){
			$list = '<div class="unit-switch">';
			foreach($SQLListDev as $Dev){
				$SQLSwitch = $db->Fast($PMonTables['switch'],'*',['id'=>$Dev['deviceid']]);
				// model switch
				$modelswitch ='<div class="model-style"><span class="m">'.$SQLSwitch['inf'].'</span><span class="b">'.$SQLSwitch['model'].'</span></div>';
				// moder panel switch
				if($types=='port')
					$moderswitch ='<div class="moder-panel-switch"><span class="unmount" onclick="ajaxponmodule(\'delunitswitch\','.$SQLSwitch['id'].','.$id.')"><i class="fi fi-rr-box"></i>Зняти</span><span class="replace"><i class="fi fi-rr-shuffle"></i>Замінити</span></div>';
				// head border
				$list .= '<div class="nameswitch"><div class="poles">'.$modelswitch.'<b>'.$SQLSwitch['place'].'</b><div class="ip">'.$SQLSwitch['netip'].'</div>'.$moderswitch.'</div></div>';
				$list .= '<div class="portswitch">';	
				#if($types=='port'){
					$SQLListPortAll = $db->Multi($PMonTables['switchport'],'*',['deviceid'=>$SQLSwitch['id']]);
					$resData = getAllPortDevice($SQLListPortAll);	
					foreach($resData as $portNAMEtype => $listsPort){
						if($portNAMEtype=='gigaethernet' || $portNAMEtype=='tgigaethernet' || $portNAMEtype=='gpon' || $portNAMEtype=='epon'){
							$list .='<div class="portswitchsfp '.$portNAMEtype.'">';
							foreach($listsPort as $portid => $valuePort){
								$list .='<div class="sfpswitch" id="port-'.$valuePort['id'].'">
								<span><img src="../style/img/unit/'.$portNAMEtype.'_'.$valuePort['operstatus'].'.png">
								<div class="numberport">'.$valuePort['idport'].'</div>
								'.($valuePort['operstatus']=='up'?'<div class="sfpswitchup"></div>':'').'</span>
								<h3>'.$valuePort['name'].'</h3>
								</div>';
							}
							$list .= '</div>';
						}
					}
				#}
				#if(!$tree=='tree'){
					$SQLLisTree = $db->Multi($PMonTables['unitpontree'],'*',['unitid'=>$id,'deviceid'=>$Dev['deviceid']]);
					if(count($SQLLisTree)){	
					$list .= '<div class="list-pon-tree">';
						foreach($SQLLisTree as $pontree){
							if(!empty($pontree['portid']))
								$SQLPortData = $db->Fast($PMonTables['switchport'],'*',['id'=>$pontree['portid']]);
							$list .= '<div class="pon-tree"><div class="img-tree"><img src="../style/img/pon-tree'.(!empty($SQLPortData['id']) && !empty($pontree['portid'])?'':'-none').'.png"></div><div class="name-tree">';
							$list .= '<a href="/?do=pon&act=tree&id='.$pontree['id'].'"><h2>'.$pontree['name'].'</h2></a>';
							if(!empty($SQLPortData['id']) && !empty($pontree['portid']))
								$list .= '<span class="sub-port-tree"><b>'.$SQLPortData['nameport'].'</b></span>';
							$list .= '<span class="sub-port-sub-port-inf">onu: <b>45</b></span>';
							$list .= '</div></div>';
						}
						$list .= '</div>';
					}else{
						$list .= '';
					}
				#}
				$list .= '</div>';
			}
			$list .= '</div>';
		}else{
			$list .= '-----';	
		}
		$head = 'Вузол зв`язку';
		$tpl->load_template('pon/viewunit.tpl');
		$tpl->set('{id}',$dataUnit['id']);
		$tpl->set('{list}',$information.$list);
			$vyzolurl ='<span class="key-vyzol" onclick="ajaxponcore(\'add\','.$id.'});"><img src="../style/img/m11.png"><span>Встановити обладнання</span></span>';	
			if(!empty($dataUnit['lon']) && !empty($dataUnit['lan'])){
				$vyzolurl .='<span class="key-vyzol" onclick="ajaxponcore(\'addtree\','.$id.');"><img src="../style/img/technology.png"><span>Створити Pon дерево</span></span>';	
				$vyzolurl .='<a href="/?do=pon&act=maper&id='.$id.'" class="key-vyzol"><img src="../style/img/m6.png"><span>Карта вузла</span></a>';
				$vyzolurl .='<a href="/?do=pon&act=myfta&id='.$id.'" class="key-vyzol"><img src="../style/ponmap/myfta.png"><span>Муфти</span></a>';	
			}
			$vyzolurl .='<span class="key-vyzol" onclick="ajaxponcore(\'addto\','.$id.');"><img src="../style/img/addconnect.png"><span>Створити звіт ТО</span></span>';
			$vyzolurl .='<span class="key-vyzol" onclick="ajaxcore(\'addnote\','.$id.');"><img src="../style/img/img3.png"><span>Нотатки</span></span>';
			$vyzolurl .='<span class="key-vyzol"><img src="../style/img/setting.png"><span>Налаштування</span></span>';
		$tpl->set('{vyzolurl}',$vyzolurl);
		$tpl->set('{zvit}',$zvit);
		$tpl->set('{device}',$device);
		$tpl->set('{insert}',inserUnitDevice($dataUnit['id']));
		$tpl->compile('pon');
		$tpl->clear();	
	break;
	case 'addonu';

	break;
}
$tpl->load_template('pon/'.($act=='unit'?'unit_':'').'main.tpl');
$tpl->set('{speedbar}','<div id="onu-speedbar">'.$speedbar.'</div>');
$tpl->set('{name}',$head);
$tpl->set('{result}',$tpl->result['pon']);
$tpl->set('{pagerbottom}',$pagertop);
$tpl->compile('content');
$tpl->clear();
?>