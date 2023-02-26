<?php
mysqli_report(MYSQLI_REPORT_OFF);
error_reporting(0);
/////////////////////////////////////////
/////////////////////////////////////////
date_default_timezone_set('Europe/Kiev');
define('PONMONITOR',true);
define('ROOT_DIR',dirname(__FILE__));
define('ENGINE_DIR',ROOT_DIR.'/install/');
require_once ENGINE_DIR.'lang.php';
// install
if($act=='install'){
	$tpl = '<form action="/install.php?act=connect" method="post"><input type="hidden" name="hideact" value="connect">';
	$tpl .= '<label><span>Database Host</span><input required class="in" type="text" name="dblocal"></label>';
	$tpl .= '<label><span>Database Name</span><input required class="in" type="text" name="dbname"></label>';
	$tpl .= '<label><span>Database User</span><input required class="in" type="text" name="dbuser"></label>';
	$tpl .= '<label><span>Database Pass</span><input required class="in" type="text" name="dbpass"></label>';
	$tpl .= '<label><span>Url site</span><input required class="in" type="text" name="url"></label>';
	$tpl .= '<input class="btn" type="submit" value="'.$lang['next'].'"></form>';
	echo tpl($lang['master'],$tpl);	
}elseif($act=='connect' && $hideact=='connect'){
	if(isset($_POST['dblocal']) and $_POST['dblocal']) $server = $_POST['dblocal']; else $server = '';
	if(isset($_POST['dbuser']) and $_POST['dbuser']) $user_db = $_POST['dbuser']; else $user_db = '';
	if(isset($_POST['dbpass']) and $_POST['dbpass']) $password_db = $_POST['dbpass']; else $password_db = '';
	if(isset($_POST['dbname']) and $_POST['dbname']) $name_db = $_POST['dbname']; else $name_db = '';
	if(isset($_POST['url']) and $_POST['url']) $url_site = $_POST['url']; else $url_site = '';
	if(isset($_POST['dblocal']) && isset($_POST['dbuser']) && isset($_POST['dbpass']) && isset($_POST['dbname'])){
		$mysqli = @new mysqli($server, $user_db, $password_db, $name_db);
		if ($mysqli->connect_errno) {
			header('refresh:10;url=install.php?act=install');
			echo tpl($lang['master'],'<div class="error">Mysql connection error: <b>' . $mysqli->connect_error.'</b></div>');
			exit;			
		}else{
			$fname = '/install/database.sql';
			$file = @file(dirname(__FILE__).$fname);
			if (!$file){
				header('refresh:5;url=install.php');
				echo tpl($lang['master'],'<div class="error">'.$lang['readump'].': <b>'.$fname.'</b></div>');
				exit;	
			}
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
				if($total){
					$cfg = "<?php if(!defined('PONMONITOR')){die('Access is denied.');}define('DBHOST','".$server."');define('DBUSER','".$user_db."');define('DBPASS','".$password_db."');define('DBNAME','".$name_db."');?>";
					$fp = @fopen(ROOT_DIR.'/inc/database.php', "w");
					if(!$fp){
						$html = '<div class="error">'.$lang['err1'].': <b>'.$lang['ok'].'</b><br>'.$lang['err2'].'<br>'.$lang['err3'].':<br><code>'.htmlspecialchars($cfg).'</code><br>'.$lang['err4'].'</br>'.$lang['err5'].'</div>';
						$html .= '<br><div class="super"><a href="/index.php">'.$lang['go'].'</a></div>';
						echo tpl($lang['master'],$html);
						die();
					}
					fwrite($fp,$cfg);	 
					fclose($fp);
				}
				header('refresh:0;url=install.php?act=okinstall');
				exit;
		}
	}else{
		header('refresh:1;url=install.php');
		exit;
	}
}elseif($act=='okinstall'){
	@unlink(dirname(__FILE__).'/install/database.sql');
	@unlink(dirname(__FILE__).'/install/update.sql');
	@unlink(dirname(__FILE__).'/install.php');
	$tpl = '<div class="success"><h1>Система встановлена і готова до роботи.</div><div class="super"><a href="/install.php?act=delinstall">'.$lang['delete'].'</a></div>';
	echo tpl($lang['master'],$tpl);	
}elseif($act=='delinstall'){
	header('refresh:0;url=index.php');
	exit;
}elseif($act=='faqlinux'){
		$tpl = '<div class="info"><b>Потрібні модулі для коректної роботи системи</b><br>sudo apt install php8.2-mysql -y<br>sudo apt install php8.2-{bcmath,snmp,xml,mysql,zip,intl,ldap,gd,cli,imagick,curl,mbstring,pgsql,opcache,soap,cgi} -y</div></div>';
	echo tpl($lang['master'],$tpl);	
}elseif($act=='update'){
	if (!@fopen(ROOT_DIR.'/inc/database.php','r')){
		header('Location: /install.php');
		exit();
	}
	require_once ROOT_DIR.'/inc/database.php';
	if(DBHOST && DBUSER && DBPASS && DBNAME){
		$mysqli = @new mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
		if ($mysqli->connect_errno) {
			header('refresh:10;url=install.php');
			echo tpl($lang['master'],'<div class="error">Mysql connection error: <b>' . $mysqli->connect_error.'</b></div>');
			exit;	
		}
		$fname = '/install/update.sql';
		$file = @file(dirname(__FILE__).$fname);
		if (!$file){
			header('refresh:5;url=index.php');
			echo tpl($lang['master'],'<div class="error">'.$lang['readump'].': <b>'.$fname.'</b></div>');
			exit;	
		}
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
		if($total){
			$html = '<div class="info"><h1>Обновлення '.$lang['title'].' - успішно</h1>'.$lang['err4'].'</br>'.$lang['err5'].'</div>';
			$html .= '<div class="super"><a href="/index.php">'.$lang['go'].'</a></div>';
			echo tpl($lang['master'],$html);
		}
	}
}else{
	if(count($noinstall)){
		$tpl = '';
		foreach($noinstall as $fun){
			$tpl .= '<div class="noinstall"><span>Not installed <b>'.$fun.'</b></span></div>';
		}
		echo tpl($lang['master'],$tpl);	
	}else{
		$tpl = '<div class="info"><h1>'.$lang['title'].'</h1>Керуйте всіма мережевими комутаторами з однієї платформи.<br><b>OLT:</b><br>- Моніторинг поганих сигналів на ONU<br>- Моніторинг температури, завантаження процесора, uptime<br>- Моніторинг статусу портів з сповіщенням в телеграм<br>- Моніторинг помилок на комутаторі<br>- Моніторинг завантаженості PON портів, к-ть ONU онлайн, оффлайн<br>- Можливість робити комутацію між обладання, підписування портів<br><b>ONU:</b><br>- Статус, довжина волокна, сигнал RX, TX<br>- Моніторинг сигналу RX з логуванням<br>- Перезавантаження, видалення, деативація, реєстрація, зміна опису, зміна VLAN*<br><b>Підтримує обладання:</b><br>- BDCOM P3310B,P3310C,P3310D,P3616-2TE,P3608-2TE,GP3600-08,GP3600-16,P3608B,3608E,3616E<br>- ZTE C220,C300,C320<br>- HUAWEI 56xx<br>- C-DATA FD1104,FD1108,FD1216,FD1208,FD1616SN <br></div><div class="info"><b>На розвиток Проекта  "ПриватБанк"</b><br>💰ГРН - 4149499140363803<br>💰USD - 4149499371431055<br>💰USDT - 3PvqeXrCbacKhHBZSRF463ewboBS1QDmf7</div><div class="super"><a href="/install.php?act=install">'.$lang['install'].'</a><a href="/install.php?act=update">'.$lang['update'].'</a><a href="/install.php?act=faqlinux">'.$lang['setupserver'].'</a></div>';
		echo tpl($lang['master'],$tpl);	
	}
}
die();
?>
