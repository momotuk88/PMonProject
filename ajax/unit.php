<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$unitid = isset($_POST['unitid']) ? Clean::int($_POST['unitid']): null;
$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
$act = isset($_POST['act']) ? Clean::text($_POST['act']): null;
$type = isset($_POST['type']) ? Clean::text($_POST['type']): null;
switch($act){
	case 'addunit': 
		okno_title('Юніти');
		echo'<form action="/?do=send" method="post" id="formadd">';
		echo'<input name="act" type="hidden" value="newunit">';
		echo form(['name'=>'Назва','descr'=>'Короткий опис','pole'=>'<input required name="name" class="input1" type="text" value="">']);
		$SQLlocation = $db->Multi('location');
		if(count($SQLlocation)){
			$select = '<select class="select" name="location" id="location">';
			foreach($SQLlocation as $location){
				$select .= '<option value="'.$location['id'].'">'.$location['name'].'</option>';
			}
			$select .= '</select>';
		}else{
			$select ='<a href="/?do=location">Додати локацію</a>';
		}
		echo form(['name'=>'Локаці','descr'=>'Розмещення обладання','pole'=>$select]);
		echo'</form>';
		echo'<div class="polebtn"><button type="submit" form="formadd" value="submit">Додати</button></div>';
		okno_end();
	break;		
	case 'newmyft': 
		okno_title('Нова муфта');
		echo'<form action="/?do=send" method="post" id="formadd">';
		echo'<input name="act" type="hidden" value="newmyft">';
		echo form(['name'=>'Назва','descr'=>'Короткий опис','pole'=>'<input required name="name" class="input1" type="text" value="">']);
		$SQLlocation = $db->Multi('location');
		if(count($SQLlocation)){
			$select = '<select class="select" name="location" id="location">';
			foreach($SQLlocation as $location){
				$select .= '<option value="'.$location['id'].'">'.$location['name'].'</option>';
			}
			$select .= '</select>';
		}else{
			$select ='----';
		}
		echo form(['name'=>'Локація','descr'=>'Розташування','pole'=>$select]);
		echo'</form>';
		echo'<div class="polebtn"><button type="submit" form="formadd" value="submit">Додати</button></div>';
		okno_end();
	break;		
	case 'selectdevice':
		if($type=='olt'){
			$listDevice = $db->Multi('switch','*',['device' => 'olt']);
		}elseif($type=='switch'){
			$listDevice = $db->Multi('switch','*',['device' => 'switch']);
		}elseif($type=='switchl2'){
			$listDevice = $db->Multi('switch','*',['device' => 'switchl2']);
		}		
		if(count($listDevice)){
			$result = '<select class="select" name="deviceid" id="deviceid">';
			$result .= '<option value="0"></option>';
			foreach($listDevice as $row){
				$result .= '<option value="'.$row['id'].'">'.$row['place'].' ['.$row['netip'].']</option>';
			}
			$result .= '</select>';
		}else{
			$result = 'empty device';
		}
		echo form(['name'=>'Обладання','descr'=>'Активне обладання','pole'=>$result]);
		die;
	break;		
	case 'nametree':
		echo form(['name'=>'Назва Pon дерева','descr'=>'Буде відображати в комутаторі','pole'=>'<input required name="nametree" class="input1" type="text">']);
		die;
	break;	
	case 'getponbox': 
		$lan = isset($_POST['lan']) ? Clean::text($_POST['lan']): null;
		$lon = isset($_POST['lon']) ? Clean::text($_POST['lon']): null;
		$getPonBox = $db->Fast($PMonTables['unitponbox'],'*',['id' => $id]);
		if($id && $lan && $lon){
			echo'<form action="/?do=send" method="post" id="formadd">';
			echo'<input name="act" type="hidden" value="markponbox">';	
			echo'<input name="unit" type="hidden" value="'.$id.'">';
			echo'<input name="lan" type="hidden" value="'.$lan.'">';
			echo'<input name="lon" type="hidden" value="'.$lon.'">';
			echo'<span class="koomap"><b>'.$lang['nameponbox'].'</b>: '.$getPonBox['name'].'</span><br>';
			if(!empty($getPonBox['count']))
				echo'<span class="koomap"><b>К-ть ONU</b>: '.$getPonBox['count'].'</span><br>';
			echo'<span class="koomap"><b>'.$lang['geo'].'</b>: '.$lan.' '.$lon.'</span><br>';
			echo'<button type="submit" class="cssadd">'.$lang['save'].'</button>';
			echo'</form>';
		}
	break;	
	case 'newponbox': 
		okno_title('Додати понбокс');
		echo'<form action="/?do=send" method="post" id="formadd">';
		echo'<input name="act" type="hidden" value="newponbox">';
		echo'<input name="tree" type="hidden" value="'.$id.'">';
		echo form(['name'=>'Назва','descr'=>'','pole'=>'<input required name="name" class="input1" type="text" value="">']);
		echo'</form>';
		echo'<div class="polebtn"><button type="submit" form="formadd" value="submit">Додати</button></div>';
		okno_end();
	break;
	case 'addfiber':
		okno_title('Додати відгалужувачі');
		echo'<form action="/?do=send" method="post" id="formadd">';
		echo'<input name="act" type="hidden" value="addfiber">';
		echo'<input name="ponbox" type="hidden" value="'.$id.'">';
			$spliter = '<select class="select" name="spliter" id="spliter"><option value="0"></option>';
			$spliter .= '<option value="1">'.$lang['spliter1'].'</option>';
			$spliter .= '<option value="2">'.$lang['spliter2'].'</option>';
			$spliter .= '<option value="3">'.$lang['spliter3'].'</option>';
			$spliter .= '<option value="4">'.$lang['spliter4'].'</option>';
			$spliter .= '<option value="5">'.$lang['spliter5'].'</option>';
			$spliter .= '<option value="6">'.$lang['spliter6'].'</option>';
			$spliter .= '<option value="7">'.$lang['spliter7'].'</option>';
			$spliter .= '<option value="8">'.$lang['spliter8'].'</option>';
			$spliter .= '<option value="9">'.$lang['spliter9'].'</option>';
			$spliter .= '<option value="10">'.$lang['spliter10'].'</option>';
			$spliter .= '<option value="11">'.$lang['spliter11'].'</option>';
			$spliter .= '<option value="12">'.$lang['spliter12'].'</option>';
			$spliter .= '<option value="13">'.$lang['spliter13'].'</option>';
			$spliter .= '<option value="14">'.$lang['spliter14'].'</option>';
			$spliter .= '<option value="15">'.$lang['spliter15'].'</option>';
			$spliter .= '<option value="16">'.$lang['spliter16'].'</option>';
			$spliter .= '<option value="17">'.$lang['spliter17'].'</option>';
			$spliter .= '<option value="18">'.$lang['spliter18'].'</option>';
			$spliter .= '<option value="19">'.$lang['spliter19'].'</option>';
			$spliter .='</select>';
		echo form(['name'=>'Тип комплектуючого','descr'=>'','pole'=>$spliter]);
		echo'</form>';
		echo'<div class="polebtn"><button type="submit" form="formadd" value="submit">Додати</button></div>';
		okno_end();	
	break;
	case 'add':
		okno_title('Додати обладання');
		echo'<form action="/?do=send" method="post" id="formadd">';
		echo'<input name="act" type="hidden" value="unitsavedevice">';
		echo'<input name="unit" type="hidden" value="'.$id.'">';
			$pole_device = '<select class="select" name="type" id="type"><option value="0"></option>';
			$pole_device .= '<option value="olt">Концетратор</option>';
			$pole_device .= '<option value="switch">Свіч</option>';
			$pole_device .= '<option value="switchl2">Свіч L2</option>';
			$pole_device .='</select>';
		echo form(['name'=>'Тип обладання','descr'=>'Діючий комутатор','pole'=>$pole_device]);
		echo'<span class="js_replace"></span></form><div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['add'].'</button></div>';
		?>
		<script>
		$('#type').on('change', function() {
			var selected = $(this).val();
			$.post(root+'ajax/unit.php',{act:'selectdevice',type:selected},function(response) { 
			$('.js_replace').html(response);
			$('.btnsend').show();
			},'html');
		});
		</script>
		<?php
		okno_end();
	break;		
	case 'addtree':	
		$listUnitDevice = $db->Multi('unitdevice','*',['unitid' => $id]);
		if(count($listUnitDevice)){
			okno_title('Демонтувати обладнання з вузла зв`язку');
			echo'<form action="/?do=send" method="post" id="formadd"><input name="act" type="hidden" value="addpontree"><input name="id" type="hidden" value="'.$id.'">';
				foreach($listUnitDevice as $row){
					$getSwitch = $db->Fast('switch','*',['id' => $row['deviceid']]);
					$listdevice .= '<option value="'.$getSwitch['id'].'">'.$getSwitch['place'].' '.$getSwitch['inf'].' '.$getSwitch['model'].'</option>';
				}
			echo form(['name'=>'Комутатор','descr'=>'Діючий комутатор','pole'=>'<select class="select" name="deviceid" id="deviceid"><option value="0"></option>'.$listdevice.='</select>']);
			echo'<span class="js_replace_name"></span><div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['add'].'</button></div>';
		?>
		<script>
		$('#deviceid').on('change', function() {
			$.post(root+'ajax/unit.php',{act:'nametree'},function(response) { 
			$('.js_replace_name').html(response);
			$('.btnsend').show();
			},'html');
		});
		</script>
		<?php
			okno_end();
		}
	break;		
	case 'delspliter':	
		if(!empty($USER['class']) && $USER['class']>=4){
			$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;	
			if($id){
				$getSpliter = $db->Fast($PMonTables['unitbasket'],'*',['id'=>$id]);
				if(!empty($getSpliter['id'])){
					okno_title($lang['delet']);
					echo'<form action="/?do=send" method="post" id="formadd"><input name="id" type="hidden" value="'.$id.'"><input name="act" type="hidden" value="delspliter">';
					echo'<div class="redton">'.$lang['delet_spliter'].'</div>';
					echo'</form><div class="polebtn"><button type="submit" form="formadd"  style="background: tomato;" value="submit">'.$lang['delet'].'</button></div>';
					okno_end();	
				}
			}
		}
	break;		
	case 'connectport':	
		if(!empty($USER['class']) && $USER['class']>=4){
			$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;	
			if($id){
				okno_title($lang['stekyvania_descr_dialog']);
				$getTree = $db->Fast($PMonTables['unitpontree'],'*',['id'=>$id]);
				if(!empty($getTree['id'])){
					echo'<form action="/?do=send" method="post" id="formadd"><input name="id" type="hidden" value="'.$id.'"><input name="act" type="hidden" value="stekport">';
					$listPonPort = $db->Multi($PMonTables['switchport'],'nameport,id,llid,typeport',['deviceid' => $getTree['deviceid']]);
					if(isset($listPonPort)){
						$polePOn = '<select class="select" name="portid"><option value="0"></option>';
						foreach($listPonPort as $row){
							if(!empty($row['typeport']) && $row['typeport']=='epon' || $row['typeport']=='gpon')
								$polePOn .= '<option value="'.$row['id'].'" '.($row['id']==$getTree['portid'] ? 'selected':'').'>'.$row['nameport'].'</option>';
						}
						$polePOn .='</select>';
						echo form(['name'=>$lang['portswitch'],'descr'=>'','pole'=>$polePOn]);
					}
					echo'</form><div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['stekyvania'].'</button></div>';
				}else{
					echo infdisplay($lang['noneinfo']);
				}
				okno_end();				
			}
		}
	break;		
	case 'addonu':	
		if(!empty($USER['class']) && $USER['class']>=4){
			$ponboxid = isset($_POST['ponboxid']) ? Clean::int($_POST['ponboxid']): null;	
			$onuid = isset($_POST['onuid']) ? Clean::int($_POST['onuid']): null;	
			if($ponboxid && $onuid){
				okno_title('Підключення клієнта');
				echo'<form action="/?do=send" method="post" id="formadd"><input name="act" type="hidden" value="addonu"><input name="ponboxid" type="hidden" value="'.$ponboxid.'"><input name="onuid" type="hidden" value="'.$onuid.'">';
				echo'<div class="redton">Ви дійсно хочете підключити термінал в цьому Понбоксі?</div>';
				echo'</form><div class="polebtn"><button type="submit" form="formadd" value="submit">Підключити</button></div>';
				okno_end();
			}
		}
	break;	
	case 'editbox':
		if(!empty($USER['class']) && $USER['class']>=4){
			$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
			$getPonBox = $db->Fast($PMonTables['unitponbox'],'*',['id'=>$id]);
			$getTree = $db->Fast($PMonTables['unitpontree'],'*',['id'=>$getPonBox['treeid']]);
			$getUnit = $db->Fast($PMonTables['unit'],'*',['id'=>$getPonBox['unitid']]);
			$getSwitchPort = $db->Fast($PMonTables['switchport'],'*',['id'=>$getPonBox['portid']]);
			okno_title('Налаштування понбокса');
			echo'<form action="/?do=send" method="post" id="formadd"><input name="act" type="hidden" value="editbox"><input name="ponboxid" type="hidden" value="'.$id.'">';
			$pole_name = '<input required name="name" class="input1" type="text" value="'.$getPonBox['name'].'">';
			$pole_sort = '<input required name="sort" class="input1" type="text" value="'.$getPonBox['sort'].'">';
			echo form(['name'=>$lang['nameponbox'],'descr'=>'','pole'=>$pole_name]);
			echo form(['name'=>'порядковий номер','descr'=>'','pole'=>$pole_sort]);
			echo'<div class="subpole">Прив`язка</div>';
			$listport = $db->Multi($PMonTables['switchport'],'*',['deviceid'=>$getPonBox['deviceid']]);
			if(count($listport)){
				$polelistport = '<select class="select" name="portid">';
				foreach($listport as $port)
					$polelistport .= '<option value="'.$port['id'].'" '.($port['id']==$getPonBox['portid']?'selected':'').'>'.$port['nameport'].'</option>';
				$polelistport .='</select>';
			}else{
				$polelistport = '';
			}
			echo form(['name'=>'Порт на комутаторі','descr'=>'','pole'=>$polelistport]);
			$listtree = $db->Multi($PMonTables['unitpontree'],'*',['unitid'=>$getPonBox['unitid']]);
			if(count($listtree)){
				$polelisttree = '<select class="select" name="treeid">';
				foreach($listtree as $tree)
					$polelisttree .= '<option value="'.$tree['id'].'" '.($tree['id']==$getPonBox['unitid']?'selected':'').'>'.$tree['name'].'</option>';
				$polelisttree .='</select>';
			}else{
				$polelisttree = '';
			}
			echo form(['name'=>'Пон гілка вузла зв`язку','descr'=>'','pole'=>$polelisttree]);
			echo'<div class="subpole">Координати</div>';
			echo form(['name'=>'Широта','pole'=>'<input name="gpslan" class="input1" type="text" value="">']);
			echo form(['name'=>'Довгота','pole'=>'<input name="gpslon" class="input1" type="text" value="">']);
			echo'</form><div class="polebtn"><button type="submit" form="formadd" value="submit">Зберегти</button></div>';
			okno_end();
		}
	break;	
	case 'delonu':	
		if(!empty($USER['class']) && $USER['class']>=4){
			$ponboxid = isset($_POST['ponboxid']) ? Clean::int($_POST['ponboxid']): null;	
			$onuid = isset($_POST['onuid']) ? Clean::int($_POST['onuid']): null;	
			if($ponboxid && $onuid){
				okno_title('Видалення');
				echo'<form action="/?do=send" method="post" id="formadd"><input name="act" type="hidden" value="delonu"><input name="ponboxid" type="hidden" value="'.$ponboxid.'"><input name="onuid" type="hidden" value="'.$onuid.'">';
				echo'<div class="redton">Ви дійсно хочете видалити термінал з цього Понбокса?</div>';
				echo'</form><div class="polebtn"><button type="submit" form="formadd" value="submit" style="background: tomato;">Видалити</button></div>';
				okno_end();
			}
		}
	break;	
	case 'deltree':	
		echo'1';	
	break;	
	case 'delbox':	
		if(!empty($USER['class']) && $USER['class']>=4){
			$ponboxid = isset($_POST['id']) ? Clean::int($_POST['id']): null;	
			if($ponboxid){
				okno_title('Видалення');
				echo'<form action="/?do=send" method="post" id="formadd"><input name="act" type="hidden" value="delbox"><input name="ponboxid" type="hidden" value="'.$ponboxid.'">';
				echo'<div class="redton">Ви дійсно хочете видалити Понбокс?</div>';
				echo'</form><div class="polebtn"><button type="submit" form="formadd" value="submit" style="background: tomato;">Видалити</button></div>';
				okno_end();
			}
		}
	break;		
	case 'addoptik':	
		if(!empty($USER['class']) && $USER['class']>=4){
			$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;	
			if($id){
				$getTreeID = $db->Fast($PMonTables['unitpontree'],'*',['id'=>$id]);
				$getUnitData = $db->Fast($PMonTables['unit'],'*',['id'=>$getTreeID['unitid']]);
				okno_title('Додати оптику');
				echo'<form action="/?do=send" method="post" id="formadd"><input name="locationid" type="hidden" value="'.$getUnitData['location'].'"><input name="unitid" type="hidden" value="'.$getTreeID['unitid'].'"><input name="act" type="hidden" value="addoptik"><input name="treeid" type="hidden" value="'.$id.'">';
				$typesfiber = '<select class="select" name="typesfiber"><option value="0"></option>';
				$typesfiber .= '<option value="1">'.$lang['vol1'].'</option>';
				$typesfiber .= '<option value="2">'.$lang['vol2'].'</option>';
				$typesfiber .= '<option value="4">'.$lang['vol4'].'</option>';
				$typesfiber .= '<option value="8">'.$lang['vol8'].'</option>';
				$typesfiber .= '<option value="12">'.$lang['vol12'].'</option>';
				$typesfiber .= '<option value="16">'.$lang['vol16'].'</option>';
				$typesfiber .= '<option value="18">'.$lang['vol18'].'</option>';
				$typesfiber .= '<option value="24">'.$lang['vol24'].'</option>';
				$typesfiber .= '<option value="48">'.$lang['vol48'].'</option>';
				$typesfiber .='</select>';
				echo form(['name'=>'Тип оптики','descr'=>'','pole'=>$typesfiber]);
				// Початкова точка
				$tochka1 .= '<option value="1">Вузол</option>';
				$tochka1 .= '<option value="2">Муфта</option>';
				$tochka1 .= '<option value="3">Понбокс</option>';
				echo form(['name'=>'Тип підключення','descr'=>'Звідки включено оптику','pole'=>'<select class="select" name="getconnect" id="getconnect"><option value="0">Вибрати</option>'.$tochka1.='</select>']);
				echo'<span class="js_replace_name"></span></form><div class="polebtn"><button type="submit" form="formadd" value="submit">Додати</button></div>';
				?><script>
				$('#getconnect').on('change', function() {
					var value = $(this).val();
					var treeid = <?=$id;?>;
					$.post(root+'ajax/unit.php',{act:'getconnect',value:value,treeid:treeid},function(response) { 
					$('.js_replace_name').html(response);
					},'html');
				});
				</script>
				<?php
				okno_end();
			}
		}
	break;		
	case 'getconnect':
		if(!empty($USER['class']) && $USER['class']>=4){
			$value = isset($_POST['value']) ? Clean::int($_POST['value']): null;	
			$treeid = isset($_POST['treeid']) ? Clean::int($_POST['treeid']): null;	
			if($value==1){
			// вузол
			
			}elseif($value==2){
			// муфта	
				$getTreeID = $db->Fast($PMonTables['unitpontree'],'*',['id'=>$treeid]);
				$getUnitID = $db->Fast($PMonTables['unit'],'*',['id'=>$getTreeID['unitid']]);
				$listmyfta = $db->Multi($PMonTables['myfta']);
				if(count($listmyfta)){
					$polelist = '<select class="select" name="getconnectid">';
					$none = null;
					foreach($listmyfta as $pb){
						if(!empty($pb['lan']) && !empty($pb['lon']))
							$none = true;
						$polelist .= '<option value="'.$pb['id'].'" '.($none ? '':'disabled').'>'.$pb['name'].''.($none ? '':'').'</option>';
					}
					$polelist .='</select>';
				}else{
					$polelist = '';
				}
				if(count($listmyfta))
					echo form(['name'=>'Куди заведена оптика','descr'=>'Початкова точка підключення','pole'=>$polelist]);
			}elseif($value==3){
			// понбокс
				$getTreeID = $db->Fast($PMonTables['unitpontree'],'*',['id'=>$treeid]);
				$listponbox = $db->Multi($PMonTables['unitponbox'],'*',['treeid'=>$treeid]);
				if(count($listponbox)){
					$polelist = '<select class="select" name="getconnectid">';
					$none = null;
					foreach($listponbox as $pb){
						if(!empty($pb['lan']) && !empty($pb['lon']))
							$none = true;
						$polelist .= '<option value="'.$pb['id'].'" '.($none ? '':'disabled').'>'.$pb['name'].''.($none ? '':'[nonemap]').'</option>';
					}
					$polelist .='</select>';
				}else{
					$polelist = '';
				}
				if(count($listponbox))
					echo form(['name'=>'Куди заведена оптика','descr'=>'Початкова точка підключення','pole'=>$polelist]);
			}
			//$tochka1 .= '<option value="1">Вузол</option>';
			$tochka1 .= '<option value="2">Муфта</option>';
			$tochka1 .= '<option value="3">Понбокс</option>';
			echo form(['name'=>'Тип підключення','descr'=>'Звідки включено оптику','pole'=>'<select class="select" name="nextconnect" id="nextconnect"><option value="0">Вибрати</option>'.$tochka1.='</select>']);
			?><span class="js_next_name"></span><script>
				$('#nextconnect').on('change', function() {
					var value = $(this).val();
					var treeid = <?=$treeid;?>;
					$.post(root+'ajax/unit.php',{act:'nexconnect',value:value,treeid:treeid},function(response) { 
					$('.js_next_name').html(response);
					},'html');
				});
			</script><?php
		}
	break;		
	case 'nexconnect':
		if(!empty($USER['class']) && $USER['class']>=4){
			$value = isset($_POST['value']) ? Clean::int($_POST['value']): null;	
			$treeid = isset($_POST['treeid']) ? Clean::int($_POST['treeid']): null;	
			if($value==1){
			// вузол
			
			}elseif($value==2){
			// муфта	
				$getTreeID = $db->Fast($PMonTables['unitpontree'],'*',['id'=>$treeid]);
				$getUnitID = $db->Fast($PMonTables['unit'],'*',['id'=>$getTreeID['unitid']]);
				$listmyfta = $db->Multi($PMonTables['myfta']);
				if(count($listmyfta)){
					$polelist = '<select class="select" name="nextconnectid">';
					$none = null;
					foreach($listmyfta as $pb){
						if(!empty($pb['lan']) && !empty($pb['lon']))
							$none = true;
						$polelist .= '<option value="'.$pb['id'].'" '.($none ? '':'disabled').'>'.$pb['name'].''.($none ? '':'').'</option>';
					}
					$polelist .='</select>';
				}else{
					$polelist = '';
				}
				if(count($listmyfta))
					echo form(['name'=>'Куди заведена оптика','descr'=>'Початкова точка підключення','pole'=>$polelist]);
			}elseif($value==3){
				// понбокс
				$listponbox = $db->Multi($PMonTables['unitponbox'],'*',['treeid'=>$treeid]);
				if(count($listponbox)){
					$polelist = '<select class="select" name="nextconnectid">';
					$none = null;
					foreach($listponbox as $pb){
						if(!empty($pb['lan']) && !empty($pb['lon']))
							$none = true;
						$polelist .= '<option value="'.$pb['id'].'" '.($none ? '':'disabled').'>'.$pb['name'].''.($none ? '':'[nonemap]').'</option>';
					}
					$polelist .='</select>';
				}else{
					$polelist = '';
				}
				if(count($listponbox))
					echo form(['name'=>'Підключаємо','descr'=>'Кінцева точка підключення','pole'=>$polelist]);
			}
			echo form(['name'=>'Довжина кабелю','descr'=>'Метрична довжина кабелю','pole'=>'<input name="metr" class="input1" type="text" value="">']);
		}
	break;		
	case 'editfibermap':
		$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
		$getFiber = $db->Fast($PMonTables['fiberlist'],'*',['id'=>$id]);
		if(!empty($getFiber['id'])){
			$SQLFiberMap = $db->Fast($PMonTables['fibermap'],'*',['fiberid'=>$getFiber['id']]);
			$getDataSql = $db->Fast($PMonTables['fiberlist'],'*',['id'=>$SQLFiberMap['id']]);
			if(!empty($getDataSql['getconnect']))
				$getFirstGeo = getTypeConnectFiber($getDataSql['getconnect'],$getDataSql['getconnectid']);
			if(!empty($getDataSql['nextconnect']))
				$getTwoGeo = getTypeConnectFiber($getDataSql['nextconnect'],$getDataSql['nextconnectid']);
			if(!empty($getTwoGeo['id']))
				$marker .='L.marker(['.$getTwoGeo['lan'].','.$getTwoGeo['lon'].'],{icon:cube}).addTo(maps);';
			if(!empty($getFirstGeo['id']))
				$marker .='L.marker(['.$getFirstGeo['lan'].','.$getFirstGeo['lon'].'],{icon:cube}).addTo(maps);';
			$SQLUnit = $db->Fast($PMonTables['unit'],'*',['id'=>$getFiber['unitid']]);
			$geo_lan = ($SQLUnit['lan']?$SQLUnit['lan']:$config['geo_lan']);
			$geo_lon = ($SQLUnit['lon']?$SQLUnit['lon']:$config['geo_lon']);
			$ponbox_geo = trim($SQLFiberMap['geo'],',');
$script = <<<HTML
<script>
var maps = L.map('maps');
maps.setView([{$geo_lan},{$geo_lon}],19);
var googleHybrids = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{maxZoom: 19,subdomains:['mt0','mt1','mt2','mt3']});
var coordinates1 = {$ponbox_geo};
var polyline = L.Polyline.PolylineEditor(coordinates1, {maxMarkers: 100}).addTo(maps);
googleHybrids.addTo(maps);
{$marker}
maps.fitBounds(polyline.getBounds());
var dumpPoints = function() {
var pointsTextArea = '';
maps.getEditablePolylines().forEach(function(polyline) {
var points = polyline.getPoints();
points.forEach(function(point) {
var latLng = point.getLatLng();
pointsTextArea += '[' + latLng.lat + ',' + latLng.lng + '],';
});
});
$.post("/?do=send",{act:'editfibermap',fiberid:{$id},geo:pointsTextArea});
$(location).attr('href');
location.reload(); 
};</script>
HTML;

		echo'<link rel="stylesheet" href="../style/map/leaflet.css" /><script src="../style/map/leaflet.js"></script><script src="../style/map/mymarker.js"></script><script src="../style/map/leaflet-editable-polyline.js"></script><div id="maps" style="height: 500px;width:100%;"></div>'.$script.'<a href="javascript:void(dumpPoints())" id="url">Зберегти зміни</a></form></div>';
		}
	break;		
	case 'delfibermap':	
		$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
		$getFiber = $db->Fast($PMonTables['fiberlist'],'*',['id'=>$id]);
		if(!empty($getFiber['id'])){
			okno_title('Демонтаж оптики');
			echo'<form action="/?do=send" method="post" id="formadd"><input name="act" type="hidden" value="delfibermap"><input name="fiberid" type="hidden" value="'.$getFiber['id'].'">';
			echo'<div class="redton">Ви дійсно хочете зняти оптику?</div>';
			echo'</form><div class="polebtn"><button type="submit" form="formadd" value="submit" style="background: tomato;">Видалити</button></div>';
			okno_end();
		}
	break;		
	case 'delunitswitch':
		okno_title('Демонтувати обладнання з вузла зв`язку');
		echo'<form action="/?do=send" method="post" id="formadd"><input name="act" type="hidden" value="delunitswitch"><input name="unitid" type="hidden" value="'.$unitid.'"><input name="deviceid" type="hidden" value="'.$id.'">';
			$pole_device = '<select class="select" name="type"><option value="0"></option>';
			$pole_device .= '<option value="1">Повернути на склад і очистити свіч</option>';
			$pole_device .= '<option value="2">Викинути і видалити все</option>';
			$pole_device .= '<option value="3">Просто зняти</option>';
			$pole_device .='</select>';
		echo form(['name'=>'Що робити з цим?','descr'=>'','pole'=>$pole_device]);
		echo'</form><div class="polebtn"><button type="submit" form="formadd" value="submit">Виконати</button></div>';
		okno_end();
	break;		
}

