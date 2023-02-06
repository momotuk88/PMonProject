<?php
define('AJAX',true);
define('ROOT_DIR',substr( dirname( __FILE__),0,-5));
define('ENGINE_DIR',ROOT_DIR.'/inc/');	
require_once ENGINE_DIR.'ajax.php';
$olt = isset($_POST['olt']) ? Clean::int($_POST['olt']): null;
$act = isset($_POST['act']) ? Clean::text($_POST['act']): null;
