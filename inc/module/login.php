<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
if(!defined('LOGIN')){
	die('System LOGIN Error Attempt!');
}
if(isset($_POST['login'])){
    $auth->login($_POST['username'],$_POST['password']);
}
if($auth->isLoggedHeader()){
	header('Location: /');
}
$auth->error(); 
echo'<!DOCTYPE html><html lang="en"><head><title>ProjectPMon - PON Device Management Ukranian</title><meta charset="utf-8"><link rel="stylesheet" type="text/css" href="../style/css/login.css"><link rel="shortcut icon" href="../style/favicon.ico" /><link rel="icon" type="image/png" href="../style/favicon-32x32.png" sizes="32x32" /><meta name="generator" content="PMonProject"><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1"><body><div class="limiter"><div class="loginformbody"><form class="loginform" action="/?do=login" method="post"><span class="login"><b>Project<span>PMon</span></b></span><span class="username"><img src="../style/img/username.png"><input class="input" type="text" name="username" placeholder="Login" required></span><span class="password"><img src="../style/img/lock.png"><input class="input" type="password" name="password" placeholder="Password" required></span><span class="send"><input type="submit" value="Login"></span><input type="hidden" name="login" value="login"></form></div></div></body></html>';
die;
?>