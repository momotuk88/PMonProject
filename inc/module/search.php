<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$metatags = array('title'=>$lang['lang_page_search'],'description'=>$lang['lang_page_search'],'page'=>'search');
$SQL_Zaput = '';
$pagertop = '';
$orderby_data = array();
$where_data = array();
$SQLonus = null;
$select_signal = '';
if(isset($_GET['act']) && $_GET['act']=='search'){
$select_search = Clean::text(trim(strip_tags(stripcslashes(SQLclear($_GET['search']))))); // що будемо шукати
if($select_search){
	$select_search = $select_search;
}else{
	$select_search = '';
}
$select_pon = checkSearch($_GET['selectpon']); // тип технології
$select_dist = checkSearch($_GET['selectdist']); // довжина волокна
$select_signal = checkSearch($_GET['selectsignal']); // якість сигналу
$select_onlyactive = checkSearch($_GET['onlyactive']); // якість сигналу
$select_olt = Clean::int($_GET['selectolt']); // Комутатор
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
}
if($select_pon=='gpon'){
	$where_data[] = "type = 'gpon'";
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
if(count($where_data)){
	$where = implode(' AND ', $where_data);
	if (!empty($where))
	   $SQLwhere = 'WHERE '.$where;
}
if(count($orderby_data)){
	$order_by = implode(', ', $orderby_data);
	if (!empty($order_by))
	   $SQLOrderBy = ' ORDER BY '.$order_by;
}
$SQL_Zaput = 'SELECT * FROM onus '.$SQLwhere.''.$SQLOrderBy.'';
}
$select_olt_list = '';
$SQLDevice = $db->Multi($PMonTables['switch'],'id,place,model,inf',['device'=>'olt']);
if(count($SQLDevice)){
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
$searchform .= '<div class="search-page" id="filtersForm">';
$searchform .= '<form action="/" method="get"><input type="hidden" name="do" value="search">';
$searchform .= '<div class="search-block" id="filtersForm">';
$searchform .= '<div class="input-search"><input type="text" name="search" class="search" placeholder="MAC (xx:xx:xx:xx:xx), Тег, Маркер, SN" autocomplete="off" value="'.($select_search?$select_search:'').'"></div>';
$searchform .= '<div class="item-search block-select">';
if($select_olt_list){
	$searchform .= '<div class="block"><h3>Комутатор</h3><div><select name="selectolt" class="sort icon-arowDown open"><option value="0">Всі комутатори</option>'.$select_olt_list.'</select></div></div>';
}
	$searchform .= '<div class="block">
		<h3>Тип технології</h3>
		<div>
			<select name="selectpon" class="sort icon-arowDown open" > 
				<option value="all" '.($select_pon=='all'?'selected':'').'>Всі</option>
				<option value="gpon" '.($select_pon=='gpon'?'selected':'').'>GPON</option>
				<option value="epon" '.($select_pon=='epon'?'selected':'').'>EPON</option>
			</select>
		</div>
	</div>
	<div class="block">
		<h3>Довжина волокна</h3>
		<div>
			<select name="selectdist" class="sort icon-arowDown open" > 
				<option value="all" '.($select_dist=='all'?'selected':'').'>Як буде</option>
				<option value="big" '.($select_dist=='big'?'selected':'').'>Спочатку Довга</option>
				<option value="small" '.($select_dist=='small'?'selected':'').'>Спочатку Коротка</option>
			</select>
		</div>
	</div>
	<div class="block">
		<h3>Сигнал ONT</h3>
		<div>
			<select name="selectsignal" class="sort icon-arowDown open" > 
				<option value="all" '.($select_signal=='all'?'selected':'').'>Як буде</option>
				<option value="big" '.($select_signal=='big'?'selected':'').'>Поганий</option>
				<option value="small" '.($select_signal=='small'?'selected':'').'>Хороший</option>
			</select>
		</div>
	</div>
</div>
<div class="search-footer">
	<input class="go" type="submit" value="Шукати"><input type="hidden" name="act" value="search">
	<div class="items-search">
			<div><input type="checkbox" name="onlyactive" class="check_trailer_lock" '.($select_onlyactive=='on'?'checked':'').'>
			<label class="item__check icon-chekR">Тільки активні</label></div>
	</div>
</div>
</form></div>';	
if($SQL_Zaput){
	$SQLonusCount = $db->SimpleWhile($SQL_Zaput);
	if(count($SQLonusCount)){		
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
		list($pagertop, $pagerbottom, $limit, $offset) = pager(30,count($SQLonusCount),'/?'.$oldlink);
		$SQLLIMIT = $SQL_Zaput.' LIMIT '.$limit.','.$offset;
		$SQLonus = $db->SimpleWhile($SQLLIMIT);
		$searchform_result .= '<div id="form_result">';
		if(count($SQLonus)){
			$searchform_result .= '<table cellspacing="0" cellpadding="3" width="100%" id="page_seacrh">';
			foreach($SQLonus as $ontid => $ont){
				$inface = $ont['type'].' '.$ont['inface'].'<br>'.($select_search ? highlight_word($ont['mac'].$ont['sn'],$select_search):$ont['mac'].$ont['sn']);
				$searchform_result .= '<tr id="ont-'.$ont['idonu'].'">';
				// olt
				$searchform_result .= '<td class="device_icon '.($ont['status']==1?'green':'red').'"><div class="site-nav-dropdown-icon-container">';
				if($ont['status']==1){
					$searchform_result .= $svg_work;
				}else{
					$searchform_result .= $svg_cof;
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
				if($ont['status']==1 && !empty($ont['online']))
					$searchform_result .= '<span class="ont-online-serach"><i class="fi fi-rr-clock"></i>онлайн з<br>'.$ont['online'].'</span>';				
				if($ont['status']==2 && !empty($ont['offline']))
					$searchform_result .= '<span class="ont-offline-serach"><i class="fi fi-rr-info"></i>оффлайн з<br>'.$ont['offline'].'</span>';
				$searchform_result .= '</td>';				
				$searchform_result .= '<td class="bl-all">';
					if(!empty($ont['name']))
						$searchform_result .= '<div class="search-tag">'.$ont['name'].'</div>';
					if(!empty($ont['tag']))
						$searchform_result .= '<div class="search-tag"><i class="fi fi-rr-label"></i>'.$ont['tag'].'</div>';					
					if(!empty($ont['uid']))
						$searchform_result .= '<div class="search-uid"><i class="fi fi-rr-user"></i>'.$ont['uid'].'</div>';
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
$tpl->set('{pagerbottom}',($SQLonus>30 ? $pagertop :''));
$tpl->set('{name}','Пошук');
$tpl->set('{result}',$SQL);
$tpl->compile('content');
$tpl->clear();
?>