<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$metatags = array('title'=>$lang['lang_page_search'],'description'=>$lang['lang_page_search'],'page'=>'search');
$sql_zaput_v_bazy = $sql_zaput_v_bazy ?? null;
$sqlorderby  = $sqlorderby ?? null;
$sqlwhere = $sqlwhere ?? null;
$pagertop = $pagertop ?? null;
$orderby_data = $orderby_data ?? null;
$where_data = $where_data ?? null;
$select_olt = $select_olt ?? null;
$sqlonus = $sqlonus ?? null;
$select_dist = $select_dist ?? null;
$select_search = $select_search ?? null;
$select_pon = $select_pon ?? null;
$select_signal = $select_signal ?? null;
$select_onlyactive = $select_onlyactive ?? null;
if(isset($_GET['act']) && $_GET['act']=='search'){
$select_search = Clean::text(trim(strip_tags(stripcslashes(SQLclear($_GET['search']))))); // що будемо шукати
if($select_search){
	$select_search = $select_search;
}else{
	$select_search = '';
}
$select_onlyactive = (isset($_GET['onlyactive']) ? checkSearch($_GET['onlyactive']) : null);
$select_pon = (isset($_GET['selectpon']) ? checkSearch($_GET['selectpon']) : null);
$select_dist = (isset($_GET['selectdist']) ? checkSearch($_GET['selectdist']) : null); 
$select_signal = (isset($_GET['selectsignal']) ? checkSearch($_GET['selectsignal']) : null); 
$select_olt = (isset($_GET['selectolt']) ? checkSearch($_GET['selectolt']) : null); 
if($select_search){
	$where_data[] = " sn LIKE '%".$select_search."%' 
	OR mac LIKE '%".$select_search."%' 
	OR sn LIKE '%".$select_search."%' 
	OR model LIKE '%".$select_search."%' 
	OR vendor LIKE '%".$select_search."%' 
	OR name LIKE '%".$select_search."%' 
	OR tag LIKE '%".$select_search."%'";
}
if($select_olt){
	$where_data[] = 'olt = '.(int)$select_olt;
}
if($select_pon=='epon'){
	$where_data[] = "type = 'epon'";
}elseif($select_pon=='gpon'){
	$where_data[] = "type = 'gpon'";
}else{
	
}
if($select_onlyactive=='on'){
	$where_data[] = 'status = 1';
}
if($select_signal=='big'){
	$orderby_data[] = '`rx` ASC';
}
if($select_signal=='small'){
	$orderby_data[] = '`rx` DESC';
}
if($select_dist=='big'){
	$orderby_data[] = '`dist` DESC';
}
if($select_dist=='small'){
	$orderby_data[] = '`dist` ASC';
}
if(is_array($where_data)){
	$where = implode(' AND ', $where_data);
	if (!empty($where))
	   $sqlwhere = 'WHERE '.$where;
}
if(is_array($orderby_data)){
	$order_by = implode(', ', $orderby_data);
	if (!empty($order_by))
	   $sqlorderby = ' ORDER BY '.$order_by;
}
$sql_zaput_v_bazy = 'SELECT * FROM onus '.$sqlwhere.''.$sqlorderby.'';
}
$SQLDevice = $db->Multi($PMonTables['switch'],'id,place,model,inf',['device'=>'olt']);
if(count($SQLDevice)){
	$select_olt_list = '';
	foreach($SQLDevice as $Device){
		$select_olt_list .= '<option value="'.$Device['id'].'"';
		if($select_olt && $Device['id']==$select_olt)
			$select_olt_list .= 'selected';
		$select_olt_list .= '>'.$Device['place'].''.($USER['class']>=4 ? ' '.$Device['inf'].''.$Device['model'].'':'').'</option>';
		$DATAolt[$Device['id']]['swid'] = $Device['id'];
		$DATAolt[$Device['id']]['place'] = $Device['place'];
		$DATAolt[$Device['id']]['model'] = $Device['inf'].' '.$Device['model'];
	}
}
$searchform = '<div class="search-page" id="filtersForm">';
$searchform .= '<form action="/" method="get"><input type="hidden" name="do" value="search">';
$searchform .= '<div class="search-block" id="filtersForm"><div class="input-search"><input type="text" name="search" class="search" placeholder="MAC (xx:xx:xx:xx:xx), SN, Tag, Mark..." autocomplete="off" value="'.($select_search?$select_search:'').'"></div><div class="item-search block-select">';
if($select_olt_list){
	$searchform .= '<div class="block"><h3>'.$lang['device'].'</h3><div><select name="selectolt" class="sort icon-arowDown open"><option value="0">'.$lang['allswitch'].'</option>'.$select_olt_list.'</select></div></div>';
}
	$searchform .= '<div class="block">
		<h3>'.$lang['typepon'].'</h3>
		<div>
			<select name="selectpon" class="sort icon-arowDown open" > 
				<option value="all" '.($select_pon=='all'?'selected':'').'>'.$lang['all'].'</option>
				<option value="gpon" '.($select_pon=='gpon'?'selected':'').'>GPON</option>
				<option value="epon" '.($select_pon=='epon'?'selected':'').'>EPON</option>
				<option value="xgpon" '.($select_pon=='xgpon'?'selected':'').'>XG-PON</option>
			</select>
		</div>
	</div>
	<div class="block">
		<h3>'.$lang['dist'].'</h3>
		<div>
			<select name="selectdist" class="sort icon-arowDown open" > 
				<option value="all" '.($select_dist=='all'?'selected':'').'>Як буде</option>
				<option value="big" '.($select_dist=='big'?'selected':'').'>Спочатку Довга</option>
				<option value="small" '.($select_dist=='small'?'selected':'').'>Спочатку Коротка</option>
			</select>
		</div>
	</div>
	<div class="block">
		<h3>'.$lang['rxsignal'].'</h3>
		<div>
			<select name="selectsignal" class="sort icon-arowDown open" > 
				<option value="all" '.($select_signal=='all'?'selected':'').'>Як буде</option>
				<option value="big" '.($select_signal=='big'?'selected':'').'>Хороший</option>
				<option value="small" '.($select_signal=='small'?'selected':'').'>Поганий</option>
			</select>
		</div>
	</div>
</div>
<div class="search-footer">
	<input class="go" type="submit" value="'.$lang['search'].'"><input type="hidden" name="act" value="search">
	<div class="items-search">
			<div><input type="checkbox" name="onlyactive" class="check_trailer_lock" '.($select_onlyactive=='on'?'checked':'').'>
			<label class="item__check icon-chekR">'.$lang['onlyonline'].'</label></div>
	</div>
</div>
</form></div>';	
$searchform_result = '';
if($sql_zaput_v_bazy){
	$sqlonusCount = $db->SimpleWhile($sql_zaput_v_bazy);
	if(count($sqlonusCount)){		
		$count_get = 0;
		$oldlink = null;
		foreach ($_GET as $get_name => $get_value) {
			$get_name = strip_tags(str_replace(array("\"","'"),array('',''),$get_name));
			$get_value = strip_tags(str_replace(array("\"","'"),array('',''),$get_value));
			if ($get_name != 'sort' && $get_name != 'type'&& $get_name != 'page') {
				if ($count_get > 0) {
					$oldlink = $oldlink . "&" . $get_name . "=" . $get_value;
				} else {
					$oldlink = $oldlink . $get_name . "=" . $get_value;
				}
				$count_get++;
			}
		}
		if($count_get > 0)
			$oldlink = $oldlink . "";
		list($pagertop, $pagerbottom, $limit, $offset) = pager(30,count($sqlonusCount),'/?'.$oldlink);
		$SQLLIMIT = $sql_zaput_v_bazy.' LIMIT '.$limit.','.$offset;
		$sqlonus = $db->SimpleWhile($SQLLIMIT);
		$searchform_result .= '<div id="form_result">';
		if(count($sqlonus)){
			$searchform_result .= '<table cellspacing="0" cellpadding="3" width="100%" id="page_seacrh">';
			foreach($sqlonus as $ontid => $ont){
				$inface = $ont['type'].' '.$ont['inface'].'<br>'.($select_search ? highlight_word($ont['mac'].$ont['sn'],$select_search):$ont['mac'].$ont['sn']);
				$searchform_result .= '<tr id="ont-'.$ont['idonu'].'">';
				// olt
				$searchform_result .= '<td class="device_icon '.($ont['status']==1?'green':'red').'"><div class="site-nav-dropdown-icon-container">';
				if($ont['status']==1){
					$searchform_result .= '<img src="../style/img/online.png">';
				}else{
					$searchform_result .= '<img src="../style/img/offline.png">';
				}
				$searchform_result .= '</div></td>';
				$searchform_result .= '<td width="15%" class="device_olt" align="left">';
				$searchform_result .= '<a href="/?do=onu&id='.$ont['idonu'].'"><img src="../style/img/link.png">'.$inface.'</a>';
				$searchform_result .= '</td>';	
				// olt
				$searchform_result .= '<td width="15%" class="device_olt" align="left">';
				$searchform_result .= '<a href="/?do=detail&act=olt&id='.$DATAolt[$ont['olt']]['swid'].'">'.$DATAolt[$ont['olt']]['place'].'<br>'.$DATAolt[$ont['olt']]['model'].'</a>';
				$searchform_result .= '</td>';
				// ont
				$searchform_result .= '<td width="5%" class="device_rx" align="left">';
				$searchform_result .= ''.styleRxMap($ont['rx']).'';
				$searchform_result .= '</td>';	
				// сигнал
				$searchform_result .= '<td width="5%" class="device_dist" align="left">';
				$searchform_result .= '<span>'.$ont['dist'].'м</span>';
				$searchform_result .= '</td>';
				// статус
				$searchform_result .= '<td width="15%" class="device_active '.($ont['status']==1?'geton':'getoff').'" align="left">';
				#if($ont['status']==1 && !empty($ont['online']))
					#$searchform_result .= '<span class="ont-online-serach"><img src="../style/img/uptime.png">онлайн з<br>'.$ont['online'].'</span>';				
				if($ont['status']==2 && !empty($ont['offline']))
					$searchform_result .= '<span class="ont-offline-serach"><img src="../style/img/uptime.png">'.$lang['offline'].' <br>'.aftertime($ont['offline']).'</span>';
				$searchform_result .= '</td>';				
				$searchform_result .= '<td class="bl-all">';
					if(!empty($ont['name']))
						$searchform_result .= '<div class="search-tag">'.$ont['name'].'</div>';
					if(!empty($ont['tag']))
						$searchform_result .= '<div class="search-tag">'.$ont['tag'].'</div>';					
					if(!empty($ont['uid']))
						$searchform_result .= '<div class="search-uid">'.$ont['uid'].'</div>';
				$searchform_result .= '</td>';
				#$searchform_result .= '<td class="function" width="2%">';
					#$searchform_result .= '<input type="checkbox" name="" value=""/>';
				#$searchform_result .= '</td>';
				$searchform_result .= '</tr>';
			}
			$searchform_result .= '</table>';
		}else{
			
		}
		$searchform_result .= '</div>';
	}else{
		$searchform_result .= '';
	}
}	
$tpl->load_template('terminal/searchmain.tpl');
$tpl->set('{sort}',$searchform.$searchform_result);
$tpl->set('{pagerbottom}',($sqlonus>30 ? $pagertop :''));
$tpl->set('{name}','');
$tpl->set('{result}','');
$tpl->compile('content');
$tpl->clear();
?>