<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$code='
<script type="text/javascript" src="../style/leader/leader-line.min.js"></script>


<STYLE>
#svg_contains {
    clear: both;
    position: absolute;
    top: 0;
    left: 0;
    z-index: 999999;
}
#cross-box {
    display: flex;
    flex-direction: column;
    flex-wrap: nowrap;
    align-content: flex-start;
    align-items: flex-start;
}
#cross-olt {
    display: flex;
    background: #eeeeee3d;
    padding: 0;
    float: left;
    margin-bottom: 0px;
    flex-wrap: nowrap;
    justify-content: flex-start;
    align-items: flex-start;
}
#cross {
    display: flex;
    border: 2px solid #eee;
    background: #eeeeee3d;
    padding: 10px 10px 5px 10px;
    float: left;
    overflow: hidden;
    flex-wrap: wrap;
    justify-content: flex-start;
    align-items: flex-start;
    align-content: flex-start;
}
.optical-socket {
    display: block;
    width: 21px;
    height: 26px;
    background: #5e8ed9;
    margin: 0px 10px 10px 0px;
    float: left;
}
.optical-socket .connect {
    position: relative;
    background: #fad04d;
    display: block;
    height: 17px;
    margin: 4px 4px 4px 3px;
    width: 15px;
}
.optical-socket .empty {
    position: relative;
    background: #222;
    display: block;
    height: 17px;
    margin: 4px 4px 4px 3px;
    width: 15px;
}
.optical-port .connect{
    position: relative;
    border: 2px solid #6789b6;
    display: block;
    background: #62ff50;
    float: left;
    height: 18px;
    margin: 4px 4px 4px 3px;
    width: 18px;
}
.giga-port .empty {
    position: relative;
    border: 2px solid #d3bfda;
    display: block;
    background: #eee;
    float: left;
    height: 18px;
    margin: 4px 4px 4px 3px;
    width: 18px;
}
.optical-port .empty {
    position: relative;
    border: 2px solid #6789b6;
    display: block;
    background: #eee;
    float: left;
    height: 18px;
    margin: 4px 4px 4px 3px;
    width: 18px;
}
.optical-port {
    position: relative;
    display: flex;
    float: left;
    height: 40px;
    margin: 0;
    width: 50px;
    flex-direction: column;
    flex-wrap: nowrap;
    justify-content: center;
    align-items: center;
}
.giga-port {
    position: relative;
    display: flex;
    float: left;
    height: 40px;
    margin: 0;
    width: 50px;
    flex-direction: column;
    flex-wrap: nowrap;
    justify-content: center;
    align-items: center;
}
.optical-port .conn {
    background-image: url(../../style/img/port-conn.png);
    position: absolute;
    height: 34px;
    width: 20px;
    margin: -22px 0 0 0;
}
.giga-port .conn {
    background-image: url(../../style/img/port-conn.png);
    position: absolute;
    height: 34px;
    width: 20px;
    margin: -22px 0 0 0;
}
.giga-port .nam {
    font-size: 9px;
    line-height: 9px;
    color: #222;
}
.optical-port .nam {
    font-size: 9px;
    line-height: 9px;
    color: #222;
}
.organizer {
    height: 50px;
    display: block;
}
.block-model h1 {
    font-size: 13px;
    font-weight: 300;
    color: #7399b4;
}
.block-model h2 {
    font-size: 13px;
    font-weight: 300;
    color: #2a90d8;
    text-decoration: underline;
}
.block-model {
    display: block;
    float: left;
    line-height: 13px;
    background: #fff;
    padding: 5px;
    width: 200px;
    margin: 5px 5px;
    border: 1px solid #aec7e3;
}
.pon-port {
    padding: 5px 0;
    display: block;
    float: left;
    border: 1px solid #4161ed;
}
.sfp-port{
	    padding:5px 0;
    display: block;
    float: left;
}
.leader-line {
    z-index: 9999;
}
.container-line {
    position: relative;
    top: 0;
    z-index: 9999;
}
</STYLE>


<div id="cross-box">
<div class="container-line" style="position: absolute;top: 0;z-index: 9999;margin-left: -15px;"></div>
<div id="cross">




<div class="optical-socket"><span class="empty" id="rozetka-1"></span></div>
<div class="optical-socket"><span class="connect" id="rozetka-2"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-3"></span></div>
<div class="optical-socket" ><span class="empty" id="rozetka-4"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-5"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-6"></span></div>
<div class="optical-socket"><span class="connect" id="rozetka-7"></span></div>
<div class="optical-socket" ><span class="connect" id="rozetka-9"></span></div>
<div class="optical-socket" ><span class="connect" id="rozetka-10"></span></div>
<div class="optical-socket"><span class="connect" id="rozetka-11"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-12"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-13"></span></div>
<div class="optical-socket"><span class="empty" id="rozetka-14"></span></div>
<div class="optical-socket"><span class="connect" id="rozetka-15"></span></div>
</div>
<div class="organizer"></div>';
$id = 1;
$switchport = $db->Multi('switch_port','*',['deviceid'=>$id]);
if(count($switchport)){
	$tpls .='<div id="cross-olt"><div class="pon-port">';
	foreach($switchport as $keyport => $port){
		if(preg_match('/PON/i',mb_strtolower($port['nameport']))) {
			$tpls .='<div class="optical-port"><span class="empty" id="pon-'.$port['id'].'"></span><span class="nam">'.$port['nameport'].'</span></div>';
		}elseif(preg_match('/fastethernet/i',mb_strtolower($port['nameport']))){
			$tpls .='';
		}else{
			$nameport = str_replace('0/', '',str_replace('GigaEthernet', 'G',$port['nameport']));
			$tpls .='<div class="giga-port"><span class="empty" id="port-'.$port['id'].'"></span><span class="nam">'.$nameport.'</span></div>';
		}
	}
	$tpls .='</div></div><div class="organizer"></div>';
}
$code .= $tpls;

$code .='</div>

<script type="text/javascript">

var pachkord = "#ffd054";

var rozetka2 = document.getElementById("rozetka-2");
var rozetka7 = document.getElementById("rozetka-7");
var rozetka9 = document.getElementById("rozetka-9");
var rozetka10 = document.getElementById("rozetka-10");
var rozetka11 = document.getElementById("rozetka-11");
var rozetka15 = document.getElementById("rozetka-15");


var port12 = document.getElementById("pon-12");
var port13 = document.getElementById("pon-13");
var port14 = document.getElementById("pon-14");
var port15 = document.getElementById("pon-15");
var port16 = document.getElementById("pon-16");
var port17 = document.getElementById("pon-17");


new LeaderLine(rozetka2, port12, {color: pachkord, size: 3, path: "fluid", startSocket: "bottom", endSocket: "top", startPlug: "disc", endPlug: "disc"});
new LeaderLine(rozetka7, port13, {color: pachkord, size: 3, path: "fluid", startSocket: "bottom", endSocket: "top", startPlug: "disc", endPlug: "disc"});
new LeaderLine(rozetka15, port14, {color: pachkord, size: 3, path: "fluid", startSocket: "bottom", endSocket: "top", startPlug: "disc", endPlug: "disc"});
new LeaderLine(rozetka9, port15, {color: pachkord, size: 3, path: "fluid", startSocket: "bottom", endSocket: "top", startPlug: "disc", endPlug: "disc"});
new LeaderLine(rozetka10, port16, {color: pachkord, size: 3, path: "fluid", startSocket: "bottom", endSocket: "top", startPlug: "disc", endPlug: "disc"});
new LeaderLine(rozetka11, port17, {color: pachkord, size: 3, path: "fluid", startSocket: "bottom", endSocket: "top", startPlug: "disc", endPlug: "disc"});

let lines = $(".leader-line");
$(".container-line").append(lines);
</script>

';
$metatags = array('title'=>$lang['log'].' '.$dataSwitch['place'],'description'=>$dataSwitch['place'].' '.$dataSwitch['inf'].' '.$dataSwitch['model'],'page'=>'switchlog');
$tpl->load_template('log/page.tpl');
$tpl->set('{result}',$code);
$tpl->set('{url}','/?do=detail&act='.$dataSwitch['device'].'&id='.$dataSwitch['id']);
$tpl->set('{pager}',$pagertop);
$tpl->set('{logdevice}',$dataSwitch['place'].' '.$dataSwitch['inf'].' '.$dataSwitch['model']);
$tpl->set('{logname}',$lang['log']);
$tpl->compile('content');
$tpl->clear();	
?>