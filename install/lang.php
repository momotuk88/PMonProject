<?php
header('Content-Type: text/html; charset=utf-8');
$noinstall = [];
$total = 0;
$query = '';
if(!function_exists('exec')) $noinstall[] = 'exec';
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
$act = (isset($_GET['act']) ? htmlspecialchars($_GET['act']): null);
$hideact = (isset($_POST['hideact']) ? htmlspecialchars($_POST['hideact']): null);
$lang['title'] = 'PMONProject v.4';
$lang['master'] = 'Привіт';
$lang['install'] = 'Встановлення';
$lang['update'] = 'Обновлення';
$lang['setupserver'] = 'Налаштування Linux';
$lang['delete'] = 'Видалити';
$lang['next'] = 'Далі';
$lang['ok'] = 'все нормально';
$lang['readump'] = 'Неможливо прочитати файл';
$lang['err1'] = 'Відсутні права на створення файлів';
$lang['err2'] = 'Вам потрібно виконати наступнії дії:<br>Перейдіть до розташування <b>inc/</b>, де потрібно створити файл <b>database.php</b>';
$lang['err3'] = 'скопіювати в нього наступне';
$lang['err4'] = 'Видалити папку <b>install</b>';
$lang['err5'] = 'Видалити файл <b>install.php</b>';
$lang['go'] = 'Почати роботу';
function tpl($title,$content){
	global $lang;
	$style = '<html xmlns="http://www.w3.org/1999/xhtml" lang="ru"><meta charset="utf-8"><link rel="shortcut icon" href="https://www.php.net/favicon.ico?v=2"><head><title>PMONProject v.4 '.$title.'</title></head>	<body><STYLE>html{background: #ccd7f70d;font-family: "Open Sans","Noto Sans Arabic",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";}.install{width: 100%;display: block;}
.info h1{padding: 0;margin: 0;font-size: 15px;color: #3a81ce;}
form .btn {margin: 10px 0;display: inline-block;padding: 3px 15px 3px 15px;width: 83px;background: #0297dc;cursor: pointer;border-radius: 3px;color: #fff;font-size: 14px;text-decoration: none; border: 0;}
form .btn:hover{background:#25607b;}
code{background: #fff;padding: 5px;border: blue 1px solid;height: 35px;line-height: 14px;display: block;color: #222;margin: 5px 0;}
.success{margin: 0 0 20px 0;background: #ebffea;border: 1px solid #9bd88c;padding: 5px;}
.success h1{margin: 0;font-size: 14px;padding: 0;color: #9bd88c;}
.error{background: #ff000005; color: tomato; border-left: 2px solid tomato;padding: 5px 10px;}
.info{background: #d0ddec24;border: 1px solid #5087d8;font-size: 14px;padding: 5px;margin: 0 0 20px 0;color: #9ba4ae;}
.install .okno {width: 50%;display: block;margin: auto;padding: 20px;background-color: #fff;-moz-box-shadow: 0px 10px 34px -15px rgba(0, 0, 0, 0.24);box-shadow: 0 0 5px 3px #00000003;}.oknotitle {font-size: 16px;color: #6d8cb9;margin-bottom: 10px;}.noinstall div {padding: 0 2px 5px 5px;color: #1f45dc;font-size: 13px;}.noinstall span {display: block;padding: 5px 10px;margin-bottom: 10px;font-size: 14px;position: relative; background-color: #faa3a312 !important;border-top-left-radius: 3px;border-bottom-left-radius: 3px;color: red;}.super{color: #48e748;}.super a:hover{background: #9ccfe6;}.code span{display: block; background: #272727; border: 1px solid #222; color: #53ed53; padding: 10px; margin: 10px 0; border-radius: 5px;font-size: 13px;}form label{display: block;padding:3px}form label span {font-size:14px;color:#bbbbbb;width:100px;display:inline-block;}form .in:hover{border:1px solid tomato;}form .in:focus{border:1px solid blue;outline: none;}form .in{border:1px solid #aac1e78f;margin:0 5px;padding:2px;border-radius:3px;width:160px;color:#222;}
.super a{margin-right:10px;display: inline-block;padding: 1px 15px 3px 15px;background: #0297dc;border-radius: 3px;color: #fff;font-size: 14px;text-decoration: none;}</STYLE><div class="install"><div class="okno">'.$content.'</div></div></body></html>';	
	return $style;
}
?>
