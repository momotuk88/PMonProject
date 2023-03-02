<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$column = $column ?? null;
$url_terminal = $url_terminal ?? null;
$oldlinks = $oldlinks ?? null;
$ORDERBY = $ORDERBY ?? null;
$whereoltandpon = $whereoltandpon ?? null;
$id = isset($_GET['id']) ? Clean::int($_GET['id']) : null;
$port = isset($_GET['port']) ? Clean::int($_GET['port']) : null;
$sort = isset($_GET['sort']) ? Clean::int($_GET['sort']) : null;
$type = isset($_GET['type']) ? Clean::text($_GET['type']) : null;
if ($sort && $type) {
switch($sort){
    case '1':
        $column = 'name';
    break;
    case '2':
        $column = 'rx';
    break;       
	case '3':
		$column = 'dist';
    break;		
	case '4':
		$column = 'added';
    break;		
	case '5':
		$column = 'online';
    break;		
	case '6':
		$column = 'offline';
    break;	
	case '7':
		$column = 'uid';
    break;	
	case '8':
		$column = 'tag';
    break;	
    default:
        $column = 'added';
    break;	
}
switch($type){
    case 'asc':
        $ascdesc = 'ASC';
        $linkascdesc = 'asc';
    break;
    case 'desc':
        $ascdesc = 'DESC';
        $linkascdesc = 'desc';

    break;
    default:
        $ascdesc = 'DESC';
        $linkascdesc = 'desc';
    break;
}
if($column && $ascdesc)
	$ORDERBY[$column] =  $ascdesc;
}
if(!$id)
	$go->redirect('main');

$link1 = '';$link2 = '';$link3 = '';$link4 = '';$link5 = '';$link6 = '';$link7 = '';$link8 = '';$sortbtn = '';
$count_get = 0;
	$oldlink = null;
	foreach ($_GET as $get_name => $get_value) {
		$get_name = strip_tags(str_replace(array("\"","'"),array('',''),$get_name));
		$get_value = strip_tags(str_replace(array("\"","'"),array('',''),$get_value));
		if ($get_name != 'sort' && $get_name != 'type') {
			if ($count_get > 0) {
				$oldlink = $oldlink . "&" . $get_name . "=" . $get_value;
			} else {
				$oldlink = $oldlink . $get_name . "=" . $get_value;
			}
			$count_get++;
		}
	}
	if ($count_get > 0) {
        $oldlink = $oldlink . "&";
    }
if ($sort==1) {
	if ($type=='desc') {
		$link1='asc';
	} else {
		$link1='desc';
	}
}else{
	if (!$link1)
		$link1='asc';
}
if ($sort==2) {
	if ($type=='desc') {
		$link2='asc';
	} else {
		$link2='desc';
	}
}else{
	if (!$link2)
		$link2='asc';
}
if ($sort==3) {
	if ($type=='desc') {
		$link3='asc';
	} else {
		$link3='desc';
	}
}else{
	if (!$link3)
		$link3='asc';
}
if ($sort==4) {
	if ($type=='desc') {
		$link4='asc';
	} else {
		$link4='desc';
	}
}else{
	if (!$link4)
		$link4='asc';
}
if ($sort==5) {
	if ($type=='desc') {
		$link5='asc';
	} else {
		$link5='desc';
	}
}else{
	if (!$link5)
		$link5='asc';
}
if ($sort==6) {
	if ($type=='desc') {
		$link6='asc';
	} else {
		$link6='desc';
	}
}else{
	if (!$link6)
		$link6='asc';
}
if ($sort==7) {
	if ($type=='desc') {
		$link7='asc';
	} else {
		$link7='desc';
	}
}else{
	if (!$link7)
		$link7='asc';
}
if ($sort==8) {
	if ($type=='desc') {
		$link8='asc';
	} else {
		$link8='desc';
	}
}else{
	if (!$link8)
		$link8='asc';
}
if($USER['hideonu']=='yes'){
	$whereoltandpon['status'] = 1;
}
$sortbtn .='<a class="sort table1" href="/?'.$oldlink.'&sort=1&type='.$link1.'"><img src="../style/img/sort/'.$link1.'.png">'.$lang['name'].'</a>';
$sortbtn .='<a class="sort table2" href="/?'.$oldlink.'&sort=3&type='.$link3.'"><img src="../style/img/sort/'.$link3.'.png">'.$lang['volokno'].'</a>';
$sortbtn .='<a class="sort table3" href="/?'.$oldlink.'&sort=2&type='.$link2.'"><img src="../style/img/sort/'.$link2.'.png">'.$lang['signal'].'</a>';
#$sortbtn .='<a class="sort table4" href="/?'.$oldlink.'&sort=4&type='.$link4.'"><img src="../style/img/sort/'.$link4.'.png">'.$lang['register'].'</a>';
#$sortbtn .='<a class="sort table5" href="/?'.$oldlink.'&sort=5&type='.$link5.'"><img src="../style/img/sort/'.$link5.'.png">'.$lang['online'].'</a>';
#$sortbtn .='<a class="sort" href="/?'.$oldlink.'&sort=6&type='.$link6.'"><img src="../style/img/sort/'.$link6.'.png">'.$lang['offline'].'</a>';
#$sortbtn .='<a class="sort" href="/?'.$oldlink.'&sort=7&type='.$link7.'"><img src="../style/img/sort/'.$link7.'.png">UID</a>';
#$sortbtn .='<a class="sort" href="/?'.$oldlink.'&sort=8&type='.$link8.'"><img src="../style/img/sort/'.$link8.'.png">'.$lang['marker'].'</a>';
if(!empty($ORDERBY[$column]))
	$oldlinks = '&sort='.(int)$sort.'&type='.$type;
// SORT
if($port){
	$dataPon = $db->Fast('switch_pon','*',['id'=>$port]);
	$dataPort = $db->Fast('switch_port','*',['deviceid'=>$id,'llid'=>$dataPon['sfpid']]);
	$whereoltandpon['olt'] = $id;
	$whereoltandpon['portolt'] = $dataPon['sfpid'];
	$url_terminal = $config['url'].'/?do=terminal&id='.$id.'&port='.$port.$oldlinks;
	$metatags = array('title'=>$dataPon['pon'].' '.$lang['pt_onu'],'description'=>$lang['pd_onu'],'page'=>'terminal');
	$selectportolt = '<div class="pon-sfp-detail"><div class="sfp-img"><img src="../style/img/pon.png"></div>';
	$selectportolt .= '<div class="inform"><h2>'.$dataPon['pon'].'';
	if(!empty($dataPort['descrport']))
		$selectportolt .= '<span class="descrport-terminal"><img src="../style/img/iconinfo.png">'.$dataPort['descrport'].'</span>';	
	$selectportolt .= '</h2>';
	$selectportolt .= '<div class="pon-sfp-stats">';
	if(!empty($dataPon['online']))
		$selectportolt .= '<span>'.$lang['sfp_1'].'</span><span class="cl1">'.$dataPon['online'].'</span>';
	if(!empty($dataPon['offline']))
		$selectportolt .= '<span>'.$lang['sfp_2'].'</span><span class="cl2">'.$dataPon['offline'].'</span>';
	if(!empty($dataPon['count']))
		$selectportolt .= '<span>'.$lang['sfp_3'].'</span><span class="cl3">'.$dataPon['count'].'</span>';
	if(!empty($dataPon['support']))
		$selectportolt .= '<span>'.$lang['sfp_4'].'</span><span class="cl4">'.$dataPon['support'].'</span>';
	$selectportolt .= '</div>';
	$selectportolt .= '</div></div>';
}else{
	$whereoltandpon['olt'] = $id;
	$url_terminal = $config['url'].'/?do=terminal&id='.$id.$oldlinks;
	$metatags = array('title'=>$lang['pt_onu'],'description'=>$lang['pd_onu'],'page'=>'terminal');
	$selectportolt = '';
}
$dataSwitch = $db->Fast('switch','*',['id'=>$id]);
if(!$dataSwitch['id']){
	$go->redirect('main');		
}
$SQLCount = $db->Multi('onus','idonu',$whereoltandpon);
list($pagertop, $pagerbottom, $limit, $offset) = pager($config['countviewpageonu'],count($SQLCount),$url_terminal);
$SQLTerminal = $db->Multi('onus','*',$whereoltandpon,$ORDERBY,$offset,$limit);
if(count($SQLTerminal)){
	foreach($SQLTerminal as $Terminal){
		$tpl->load_template('terminal/list.tpl');
		$status = statusTermianl($Terminal['status']);
		$tpl->set('{checkadded}',checkWhenAdded($Terminal['added']));
		$tpl->set('{status}',$status['img']);
		$tpl->set('{statuscss}',$status['css']);
		$tpl->set('{signal}',signalTerminal($Terminal['rx']).($Terminal['rxstatus']=='up' || $Terminal['rxstatus']=='down' ? '<span class="signaldown"><i class="fi fi-rr-angle-small-'.$Terminal['rxstatus'].'"></i></span>':''));
		$tpl->set('{dist}',($Terminal['dist']?$Terminal['dist']:''));
		$tpl->set('{metric}',($Terminal['dist']?'м':''));
		$tpl->set('{inface}',$Terminal['inface']);
		$tpl->set('{name}',(!empty($Terminal['name'])?'<span class="name-onu">'.$Terminal['name'].'</span>':''));
		$tpl->set('{type}',$Terminal['type']);
		$tpl->set('{idonu}',$Terminal['idonu']);
		$tpl->set('{reason}',($Terminal['status']==2?(!empty($Terminal['reason'])?'<span class="reason_'.$Terminal['reason'].'"></span>':''):''));
		$tpl->set('{time}',($Terminal['status']==2?(!empty($Terminal['offline'])?''.aftertime($Terminal['offline']).'':''):''));
		$tpl->set('{terminal}',(!empty($Terminal['vendor']) || !empty($Terminal['model'])?'<span class="setterminal">'.$Terminal['model'].''.$Terminal['vendor'].'</span>':''));
		$tpl->set('{uid}',(!empty($Terminal['uid'])?'<span class="terminaluid" onclick="ajaxbillingdata('.$Terminal['idonu'].')"><img src="/style/img/info.png">'.$Terminal['uid'].'</span>':''));
		$tpl->set('{tag}',(!empty($Terminal['tag'])?'<span class="terminaltag">'.$Terminal['tag'].'</span>':''));
		$tpl->set('{mac}',($Terminal['mac']?$Terminal['mac']:'').($Terminal['sn']?$Terminal['sn']:''));
		$tpl->compile('terminal');
		$tpl->clear();			
	}
}else{
	$tpl->load_template('terminal/empty.tpl');
	$tpl->set('{result}','<div class="emlist">'.$lang['emlist'].'</div>');
	$tpl->compile('terminal');
	$tpl->clear();		
}
// module:speedbar
$tpl->load_template('terminal/speedbar.tpl');
$tpl->set('{inface}','');
$tpl->set('{id}',$id);
$tpl->set('{olt_model}',$dataSwitch['inf'].' '.$dataSwitch['model']);
$tpl->set('{olt_place}',$dataSwitch['place']);
if(!empty($dataPon['pon']))
	$tpl->set('{inface}','<span class="brmspan"><i class="fi fi-rr-angle-left"></i>'.$dataPon['pon'].'</span>');
$tpl->compile('block-speedbar');
$tpl->clear();	
// module:speedbar
$viewbtn = '<div class="switch-btn-right">
<label class="checkbox-ios">'.($USER['hideonu']=='no'?$lang['hide']:$lang['show']).' '.$lang['onuonlinelist'].'
<input id="hideonu" name="hideonu" type="checkbox" '.($USER['hideonu']=='no'?'checked="checked"':'').'><span class="checkbox-ios-switch" onclick="ajaxhideonu();"></span></label>
</div>';
if(!empty($dataPon['pon'])){
	$tpl->load_template('olt/right.tpl');
	$tpl->set('{viewbtn}',$viewbtn);
	$tpl->set('{listpon}',getlistPonTpl(['deviceid'=>$id,'ponid'=>$dataPon['id']]));
	$tpl->compile('block-right');
	$tpl->clear();	
}else{
	$tpl->load_template('olt/right-all.tpl');
	$tpl->set('{viewbtn}',$viewbtn);
	$tpl->set('{listpon}',getlistPonTpl(['deviceid'=>$id]));
	$tpl->compile('block-right');
	$tpl->clear();	
}
$tpl->load_template('terminal/main.tpl');
$tpl->set('{name}','Список терміналів');
$tpl->set('{sort}',$selectportolt.$sortbtn);
$tpl->set('{result}',$tpl->result['terminal']);
$tpl->set('{block-content}',$tpl->result['block-speedbar']);
$tpl->set('{pagerbottom}',$pagertop);
$tpl->compile('content');
$tpl->clear();
?>