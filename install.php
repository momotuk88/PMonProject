<?php
$next = false;
$go	= false;
$noinstall = array();
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
if(!$ip)
	$ip = 'http://192.168.1.1';
header('Content-Type: text/html; charset=utf-8');
if(preg_match("/apache/i", $_SERVER['SERVER_SOFTWARE']) || preg_match("/nginx/i", $_SERVER['SERVER_SOFTWARE'])) $go	= true;
if(!function_exists('curl_init')) $noinstall[] = 'curl';
if(!extension_loaded('mbstring')) $noinstall[] = 'mbstring';
if(!function_exists('mb_strtoupper')) $noinstall[] = 'mb_strtoupper';	
if(!extension_loaded('snmp')) $noinstall[] = 'snmp';	
if(!function_exists('json_encode'))	$noinstall[] = 'json_encode';	
if(!function_exists('utf8_decode'))	$noinstall[] = 'utf8';		
if(!function_exists('mysqli_connect'))	$noinstall[] = 'Enable Mysqli support in your PHP installation';		
if(!extension_loaded('gd'))	$noinstall[] = 'gd';	
if(!extension_loaded('sockets')) $noinstall[] = 'sockets';	
if(!extension_loaded('json')) $noinstall[] = 'json';	
if(!extension_loaded('iconv')) $noinstall[] = 'iconv';	
if(!extension_loaded('imagick')) $noinstall[] = 'imagick';	
// end
if($go){
	echo'<html xmlns="http://www.w3.org/1999/xhtml" lang="ru"><meta charset="utf-8"><link rel="shortcut icon" href="https://www.php.net/favicon.ico?v=2"><head><title>PMONProject v.4</title></head><body><STYLE>html{background: #ccd7f70d;font-family: "Open Sans","Noto Sans Arabic",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";}.install{width: 100%;display: block;}.install .okno {width: 50%;display: block;margin: auto;padding: 20px;background-color: #fff;-moz-box-shadow: 0px 10px 34px -15px rgba(0, 0, 0, 0.24);box-shadow: 0 0 5px 3px #00000003;}.oknotitle {font-size: 16px;color: #6d8cb9;margin-bottom: 10px;}.noinstall div {padding: 0 2px 5px 5px;color: #1f45dc;font-size: 13px;}.noinstall span {display: block;padding: 5px 10px;margin-bottom: 10px;font-size: 14px;position: relative; background-color: #faa3a312 !important;border-top-left-radius: 3px;border-bottom-left-radius: 3px;color: red;}.super{color: #48e748;}.super a:hover{background: #9ccfe6;}.code span{display: block; background: #272727; border: 1px solid #222; color: #53ed53; padding: 10px; margin: 10px 0; border-radius: 5px;font-size: 13px;}form label{display: block;padding:3px}form label span {font-size:14px;color:#bbbbbb;width:100px;display:inline-block;}form .in:hover{border:1px solid tomato;}form .in:focus{border:1px solid blue;outline: none;}form .in{border:1px solid #aac1e78f;margin:0 5px;padding:2px;border-radius:3px;width:160px;color:#222;}form .btn{margin: 10px 0;display: inline-block;padding: 1px 15px 3px 15px;width: 83px;background: #0297dc;cursor: pointer;border-radius: 3px;color: #fff;font-size: 14px;text-decoration: none;}.super a{display: inline-block;padding: 1px 15px 3px 15px;background: #0297dc;border-radius: 3px;color: #fff;font-size: 14px;text-decoration: none;}</STYLE>';
	echo'<div class="install"><div class="okno"><div class="oknotitle">System check before installation <b>PMONProject v.4</b></div>';
	#$folder1 = fopen(dirname(__FILE__).'/export/onu/test.test', 'w') or die('<div class="noinstall"><span>You don`t currently have permission to access this folder <b>export/onu/</b></span></div>');
	#@unlink(dirname(__FILE__).'/export/onu/test.test');
	#$folder2 = fopen(dirname(__FILE__).'/export/snmpcache/test.test', 'w') or die('<div class="noinstall"><span>You don`t currently have permission to access this folder <b>export/snmpcache/</b></span></div>');
	#@unlink(dirname(__FILE__).'/export/snmpcache/test.test');	
	#$folder3 = fopen(dirname(__FILE__).'/install/test.test', 'w') or die('<div class="noinstall"><span>You don`t currently have permission to access this folder <b>install/</b></span></div>');
	#@unlink(dirname(__FILE__).'/install/test.test');	
	#$folder4 = fopen(dirname(__FILE__).'/export/switch/test.test', 'w') or die('<div class="noinstall"><span>You don`t currently have permission to access this folder <b>export/switch/</b></span></div>');
	#@unlink(dirname(__FILE__).'/export/switch/test.test');
	if(count($noinstall)){
		foreach($noinstall as $fun)
			echo'<div class="noinstall"><span>Not installed <b>'.$fun.'</b></span></div>';
	}else{
		if(!empty($_GET['act'])){
			
		}else{
			echo'<div class="super"><a href="/install.php?act=mysqli">Install</a></div>';
		}
		$next = true;		
	}
	if($_GET['act']=='mysqli' && $next){
		echo'<form action="/install.php?act=connect" method="post"><input type="hidden" name="hideact" value="connect">';
		echo'<label><span>Database Host</span><input required class="in" type="text" name="dblocal"></label>';
		echo'<label><span>Database Name</span><input required class="in" type="text" name="dbname"></label>';
		echo'<label><span>Database User</span><input required class="in" type="text" name="dbuser"></label>';
		echo'<label><span>Database Pass</span><input required class="in" type="text" name="dbpass"></label>';
		echo'<label><span>Url site</span><input required class="in" type="text" name="url" value="http://'.$ip.'"></label>';
		echo'<input class="btn" type="submit" value="Next">';
		echo'</form>';
	}elseif($_GET['act']=='connect' && $_POST['hideact']=='connect' && $next && !empty($_POST['dblocal']) && !empty($_POST['dbpass']) && !empty($_POST['dbname'])){
		if(isset($_POST['dblocal']) and $_POST['dblocal']) $server = $_POST['dblocal']; else $server = '';
		if(isset($_POST['dbuser']) and $_POST['dbuser']) $user_db = $_POST['dbuser']; else $user_db = '';
		if(isset($_POST['dbpass']) and $_POST['dbpass']) $password_db = $_POST['dbpass']; else $password_db = '';
		if(isset($_POST['dbname']) and $_POST['dbname']) $name_db = $_POST['dbname']; else $name_db = '';
		if(isset($_POST['url']) and $_POST['url']) $url_site = $_POST['url']; else $url_site = '';
		#print_r($_POST);
		$mysqli = new mysqli($server, $user_db, $password_db, $name_db);
		if ($mysqli->connect_errno) {
			printf('<div class="noinstall"><span>Connect failed: %s', $mysqli->connect_error.'</span> <a href="/install.php?act=mysqli">Back</a></div>');
			exit();
		}else{
		$dbconfig = "define('DBHOST', '{$server}');<br>define('DBUSER', '{$user_db}');<br>define('DBPASS', '{$password_db}');<br>define('DBNAME', '{$name_db}');";	
		$fname = '/install/database.sql';
        $file = @file(dirname(__FILE__).'/install/database.sql');
        if (!$file) {
            die('<div class="noinstall"><span>Can`t read mysql dump: <b>'.$fname.'</b></span></div>');
        }
        $total = 0;
        $query = '';
        foreach ($file as $line) {
			if (preg_match("/^\s?#/", $line) || !preg_match("/[^\s]/", $line))
				continue;
			else {
				$query .= $line;
				if (preg_match("/;\s?$/", $query)) {
					$mysqli->query($query);
					$total++;
					$query = '';
				}
			}
        }
		$mysqli->query("UPDATE config SET value = '$url_site' WHERE id = 4");
		$mysqli->query("UPDATE config SET value = '$url_site/api.php' WHERE id = 25");
		$mysqli->query("UPDATE config SET value = '$url_site/api.php' WHERE id = 33");
		if($total)
			echo'SQL insert: '.$total;
			echo'<div class="code">You need to find a file <b>database.php<b> in the directory <b>inc</b> and edit it and insert the following into it:<span>'.$dbconfig.'</span><font color="red">You need to delete the <b>install.php - file, install - directory</b></font><br><a href="/">Run</a></div>';
		}
	}else{
		echo'';
	}
	echo'</div></div></body></html>';
}else{
	die('Server does not have nginx or apache installed');
}
?>
