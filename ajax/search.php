<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$zapros = Clean::text(trim(strip_tags(stripcslashes($_POST["zapros"]))));
if(!empty($USER['id'])){
$SQLDevice = $db->Multi($PMonTables['switch'],'id,place,model,inf',['device'=>'olt']);
if(count($SQLDevice)){
	foreach($SQLDevice as $Device){
		$select_olt_list = '<option value="'.$Device['id'].'"';
		if($select_olt && $Device['id']==$select_olt)
			$select_olt_list .= 'selected';
		$select_olt_list .= '>'.$Device['place'].''.($USER['class']>=4 ? ' '.$Device['inf'].''.$Device['model'].'':'').'</option>';
		$DATAolt[$Device['id']]['swid'] = $Device['id'];
		$DATAolt[$Device['id']]['place'] = $Device['place'];
		$DATAolt[$Device['id']]['model'] = $Device['inf'].' '.$Device['model'];
	}
}
	$orderby = " ORDER BY idonu ASC";
	$limit = " LIMIT 30";
	$where = "WHERE name LIKE '%".$zapros."%' OR sn LIKE '%".$zapros."%' OR mac LIKE '%".$zapros."%' OR descr LIKE '%".$zapros."%'";
	$sqlonus = $db->SimpleWhile("SELECT * FROM onus $where $orderby $limit");
	if(!empty($USER['id'])){
		$tplresult = '<div id="form_result">';
		if(count($sqlonus)){
			$tplresult .= '<table cellspacing="0" cellpadding="3" width="100%" id="page_seacrh">';
			foreach($sqlonus as $ontid => $ont){
				$inface = $ont['type'].' '.$ont['inface'].'<br>'.($zapros ? highlight_word($ont['mac'].$ont['sn'],$zapros):$ont['mac'].$ont['sn']);
				$tplresult .= '<tr id="terminals">';
				// olt
				$tplresult .= '<td class="device_icon '.($ont['status']==1?'green':'red').'"><div class="site-nav-dropdown-icon-container">';
				if($ont['status']==1){
					$tplresult .= '<img src="../style/img/online.png">';
				}else{
					$tplresult .= '<img src="../style/img/offline.png">';
				}
				$tplresult .= '</div></td>';
				$tplresult .= '<td width="15%" class="device_olt" align="left">';
				$tplresult .= '<a href="/?do=onu&id='.$ont['idonu'].'"><img src="../style/img/link.png">'.$inface.'</a>';
				$tplresult .= '</td>';	
				// olt
				$tplresult .= '<td width="15%" class="device_olt" align="left">';
				$tplresult .= '<a href="/?do=detail&act=olt&id='.$DATAolt[$ont['olt']]['swid'].'">'.$DATAolt[$ont['olt']]['place'].'<br>'.$DATAolt[$ont['olt']]['model'].'</a>';
				$tplresult .= '</td>';
				// ont
				$tplresult .= '<td width="5%" class="device_rx" align="left">';
				$tplresult .= ''.styleRxMap($ont['rx']).'';
				$tplresult .= '</td>';	
				// сигнал
				$tplresult .= '<td width="5%" class="device_dist" align="left">';
				$tplresult .= '<span>'.$ont['dist'].'м</span>';
				$tplresult .= '</td>';
				// статус
				$tplresult .= '<td width="15%" class="device_active '.($ont['status']==1?'geton':'getoff').'" align="left">';
				#if($ont['status']==1 && !empty($ont['online']))
					#$tplresult .= '<span class="ont-online-serach"><img src="../style/img/uptime.png">онлайн з<br>'.$ont['online'].'</span>';				
				if($ont['status']==2 && !empty($ont['offline']))
					$tplresult .= '<span class="ont-offline-serach"><img src="../style/img/uptime.png">оффлайн <br>'.aftertime($ont['offline']).'</span>';
				$tplresult .= '</td>';				
				$tplresult .= '<td class="bl-all">';
					if(!empty($ont['name']))
						$tplresult .= '<div class="search-tag">'.$ont['name'].'</div>';
					if(!empty($ont['tag']))
						$tplresult .= '<div class="search-tag">'.$ont['tag'].'</div>';					
					if(!empty($ont['uid']))
						$tplresult .= '<div class="search-uid">'.$ont['uid'].'</div>';
				$tplresult .= '</td>';
				#$tplresult .= '<td class="function" width="2%">';
					#$tplresult .= '<input type="checkbox" name="" value=""/>';
				#$tplresult .= '</td>';
				$tplresult .= '</tr>';
			}
			$tplresult .= '</table>';
		}else{
			$tplresult .= '<div class="empty_search">'.$lang['empty_search'].': <b>'.$zapros.'</b></div>';
		}
		$tplresult .= '</div>';
	}
	echo $tplresult;
}