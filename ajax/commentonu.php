<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
$act = isset($_POST['act']) ? Clean::text($_POST['act']): null;
$name = isset($_POST['name']) ? Clean::text($_POST['name']): null;
if($id){
	if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
	$onus = $db->Fast('onus','*',['idonu' => $id]); 
		if(!empty($onus['idonu']) && $act=='save' && $name && $USER['id']){
			$sqlinsertcomm['added'] = date('Y-m-d H:i:s');
			$sqlinsertcomm['userid'] = $USER['id'];
			$sqlinsertcomm['idonu'] = $onus['idonu'];
			$sqlinsertcomm['comm'] = $name;
			$db->SQLinsert('onus_comm',$sqlinsertcomm);
			$db->SQLupdate($PMonTables['onus'],['comments'=>$name],['idonu'=>$onus['idonu']]);
			echo'<div class="comment"><div id="comment"><div class="c_text" id="comment_text">'.$name.'</div><div class="tfr"><a href="#" onClick="editcomments('.$id.'); return false;"><img src="../style/img/edit.png">'.$lang['edit'].'</a></div></div></div>';
		}elseif(!empty($onus['idonu']) && $act=='list'){
			$getcomm = $db->Multi('onus_comm','*',['idonu' => $id]); 
			if(count($getcomm)){
				foreach($getcomm as $id => $value){
					echo'<div class="c_text" id="commenthistory"><p>Дата: <b>'.$value['added'].'</b> Користувач: <b>'.$value['username'].'</b></p>'.$value['comm'].'</div>';
				}
			}
		}elseif(!empty($onus['idonu']) && $act=='edit'){
			echo'<form name="comment" id="form-comment" action="/"><textarea name="descr" id="commentonu" class="comm">'.$onus["comments"].'</textarea><input type="button" class="btn-comm" onClick="sendcomments('.$id.')"  value="'.$lang['save'].'" id="form-comment"></form>';
		}else{
			
		}		
	}
}
?>
