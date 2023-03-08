<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$resultdata = false;
if($USER['class']>=2 && !empty($USER['id']) && !empty($config['billingtype'])){
$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
	switch($config['billingtype']){
		// MikBill
		case 'mikbill': 
		
			$resultdata = true;
			break;
		// ABillS
		case 'abills': 
		
			$resultdata = true;
			break;
		// NoDenny
		case 'nodeny': 
		
			$resultdata = true;
			break;
		// UserSide
		case 'userside': 
		
			$resultdata = true;
			break;
	}
}
if($resultdata===true){
echo'<div id="usercard">
<div class="head-user"><b>Картка клієнта</b></div>
<div class="data-user">
<div class="pole-user"><span class="value">UID</span><span class="data">23345</span></div>
<div class="pole-user"><span class="value">П.І.Б.</span><span class="data">Тестовий акуант для б</span></div>
<div class="pole-user"><span class="value">Баланс</span><span class="data">345.00</span></div>

<div>
</div>';	
}
?>