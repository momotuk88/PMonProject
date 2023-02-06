<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$act = isset($_POST['act']) ? Clean::text($_POST['act']): null;
$types = isset($_POST['types']) ? Clean::text($_POST['types']): null;
$city = isset($_POST['city']) ? Clean::int($_POST['city']): null;
if($act=='object'){
	if($types=='mdu'){
		echo'
		<div class="setmap">
		<div class="namemap">MDU на карті</div>
		<div class="data">
			<div><span class="namemappole">Назва бокса</span><input required="" name="name" class="inputmap" type="text"></div>
			<div><span class="namemappole">Довгота </span><input id="ajaxlan" name="lan" class="inputmap" type="text"></div>
			<div><span class="namemappole">Широта </span><input id="ajaxlon" name="lon" class="inputmap" type="text"></div>
		</div></div>';
	}
}