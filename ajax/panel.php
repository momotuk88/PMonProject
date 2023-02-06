<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
$getONU = $db->Fast($PMonTables['onus'],'*',['idonu' => $id]);
if(!empty($getONU['idonu'])){
$getSwitch = $db->Fast($PMonTables['switch'],'*',['id' => $getONU['olt']]);
if(!empty($getONU['idonu']) && !empty($getSwitch['username']) && !empty($getSwitch['id']) && $getSwitch['oidid']==1){
echo'<div class="command-panel">';
echo'<div class="sd1"><img src="../style/img/cmd.png">ONU</div>';
echo'<div class="sd2">'.$getONU['type'].' '.$getONU['inface'].'</div>';
echo'<div class="sd3" onclick="ajaxcmdpanel(3,'.$getONU['idonu'].')"><i class="fi fi-rr-data-transfer"></i>'.$lang['allmac'].'</div>';	
echo'<div class="sd4" onclick="ajaxcmdpanel(4,'.$getONU['idonu'].')"><i class="fi fi-rr-power"></i>'.$lang['reboot'].'</div>';		
echo'<div class="sd6" onclick="ajaxcmdpanel(6,'.$getONU['idonu'].')"><i class="fi fi-rr-settings"></i>'.$lang['cfgonu'].'</div>';		
echo'</div>';
}
}


