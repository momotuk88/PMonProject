<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$act = isset($_POST['act']) ? Clean::text($_POST['act']): null;
$id = isset($_POST['id']) ? Clean::int($_POST['id']): null;
if($id && $act=='first'){
	die('Завантажується інформація ');
}