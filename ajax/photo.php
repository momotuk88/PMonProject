<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
$act = isset($_POST['act']) ? Clean::text($_POST['act']): null;
$type = isset($_POST['type']) ? Clean::text($_POST['type']): null;
switch($act){
	case 'view': 
		$getPhoto = $db->Fast('switch_photo','*',['id'=>$id]);
		if(!empty($getPhoto['name']) && !empty($getPhoto['photo'])){
			okno_title('Юніти');	
				echo'<div class="viewphoto">';			
				echo'<div class="img"><div class="i"><a href="/file/photo/'.$getPhoto['photo'].'" target="_blank"><img src="/file/photo/'.$getPhoto['photo'].'"></a></div></div>';
				echo'<div class="name"><div class="date">'.$getPhoto['added'].'</div>';
				echo'<h2>'.$getPhoto['name'].' <a href="/?do=send&act=delphoto&id='.$getPhoto['id'].'">видалити</a></h2>';
				if(!empty($getPhoto['note']))
					echo'<span class="descr">'.$getPhoto['note'].'</span>';
				echo'</div></div>';	
			okno_end();
		}
	break;		
	case 'del':	
	
	break;		
	case 'add':
		okno_title($lang['addphoto']);
			echo'<form action="/?do=send" method="post" id="formadd" enctype="multipart/form-data">';
			echo'<input name="act" type="hidden" value="savephoto">';
			echo'<input name="id" type="hidden" value="'.$id.'">';
			echo form(['name'=>$lang['name'],'descr'=>'','pole'=>'<input name="name" class="input1" type="text" value="">']);
			echo form(['name'=>$lang['photo'],'descr'=>$lang['photo_info'],'pole'=>'<input type="file" id="file" name="file" multiple>']);
			echo form(['name'=>'','descr'=>'','pole'=>'<textarea name="note" class="textarea"></textarea>']);
			echo'<div class="polebtn"><button type="submit" form="formadd" value="submit">'.$lang['add'].'</button></div>';
			echo'</form>';
		okno_end();
	break;		
}

