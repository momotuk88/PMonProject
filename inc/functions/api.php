<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
function check_api_key($key,$ip=null) {
	global $db;
	$access = false;
	$dataAPI = $db->Fast('apikey','*',['apikey'=>$key]);
	if(!empty($dataAPI['id'])){
		if($dataAPI['types']=='monitor'){
			
		}
		$access = true;
		$db->Simple('UPDATE `apikey` SET `count` = (count+1) WHERE `id` = '.$dataAPI['id']);
	}
	return $access;
}
function getApikeyMonitor() {
	global $db;
	$dataAPI = $db->Fast('apikey','*',['types'=>'monitor']);
	return $dataAPI['apikey'];
}
function result_ont_api($idonu) {
	global $db;
	$result = array();
	$dataONT = $db->Fast('onus','*',['idonu'=>$idonu]);
	if(!empty($dataONT['idonu'])){
		$dataSwitch = $db->Fast('switch','*',['id'=>$dataONT['olt']]);
		$result['status'] = ($dataONT['status']==1?'up':'down');
		$result['inface'] = $dataONT['type'].' '.$dataONT['inface'];
		$result['dist'] = $dataONT['dist'];
		$result['pon'] = $dataONT['type'];
		$result['olt']['place'] = $dataSwitch['place'];
		if(!empty($dataSwitch['updates']))
			$result['olt']['updates'] = $dataSwitch['updates'];
		if($dataONT['model'] || $dataONT['vendor'])
			$result['model'] = $dataONT['model'].' '.$dataONT['vendor'];
		if(!empty($dataONT['mac']))
			$result['mac'] = $dataONT['mac'];	
		if(!empty($dataONT['uid']))
			$result['uid'] = $dataONT['uid'];
		if(!empty($dataONT['sn']))
			$result['sn'] = $dataONT['sn'];	
		if($dataONT['status']==1 && !empty($dataONT['rx']))
			$result['rx'] = $dataONT['rx'];	
		if($dataONT['status']==1 && !empty($dataONT['tx']))
			$result['tx'] = $dataONT['tx'];
	}else{
		$result['error'] = 'missing_ont_data';
	}
	return $result;
}
function api_clear_text($text) {
	$quotes = array("php", "script", "\x60", "\t", "\n", "\r", ";", "[", "]", "{", "}", "=", "*", "^", "%", "$", "<", ">" , "\n" , "\'" );
	$goodquotes = array("#", "'", '"' );
	$repquotes = array("\#", "\'", '\"' );
	$text = str_replace(array("%","_"), array("\\%","\\_"), $text );
	$text = stripslashes($text);
	$text = trim(strip_tags($text));
	$text = str_replace($quotes,'',$text );
	$text = str_replace($goodquotes,$repquotes,$text);
	return $text;
}
function totranslit($var, $lower = true, $punkt = true) {
	global $langtranslit;	
	if ( is_array($var) ) return "";
	$var = str_replace(chr(0), '', $var);
	if (!is_array ( $langtranslit ) OR !count( $langtranslit ) ) {
		$var = trim( strip_tags( $var ) );
		if ( $punkt ) $var = preg_replace( "/[^a-z0-9\_\-.]+/mi", "", $var );
		else $var = preg_replace( "/[^a-z0-9\_\-]+/mi", "", $var );
		$var = preg_replace( '#[.]+#i', '.', $var );
		$var = str_ireplace( ".php", ".ppp", $var );
		if ( $lower ) $var = strtolower( $var );
		return $var;
	}	
	$var = trim( strip_tags( $var ) );
	$var = preg_replace( "/\s+/ms", "-", $var );
	$var = str_replace( "/", "-", $var );
	$var = strtr($var, $langtranslit);	
	if ( $punkt ) $var = preg_replace( "/[^a-z0-9\_\-.]+/mi", "", $var );
	else $var = preg_replace( "/[^a-z0-9\_\-]+/mi", "", $var );
	$var = preg_replace( '#[\-]+#i', '-', $var );
	$var = preg_replace( '#[.]+#i', '.', $var );
	if ( $lower ) $var = strtolower( $var );
	$var = str_ireplace( ".php", "", $var );
	$var = str_ireplace( ".php", ".ppp", $var );	
	if( strlen( $var ) > 200 ) {		
		$var = substr( $var, 0, 200 );		
		if( ($temp_max = strrpos( $var, '-' )) ) $var = substr( $var, 0, $temp_max );	
	}	
	return $var;
}
