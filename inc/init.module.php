<?php
if (!defined('PONMONITOR')){
	header('HTTP/1.1 403 Forbidden');
	header('Location: ../');
	die('Hacking attempt!');
}	
if(!isset($do) AND isset ($_REQUEST['do'])) $do = totranslit ($_REQUEST['do']); elseif(isset($do)) $do = totranslit($do); else $do = '';
if(!isset($act) AND isset ($_REQUEST['act'])) $act = totranslit ($_REQUEST['act']); elseif(isset($act)) $act = totranslit($act); else $act = '';
switch($do){
	case 'login': 
		define('LOGIN',true);
		define('DEBUG',false);
		require MODULE.'login.php';	
	break;		
	case 'exit': 
		$auth->logout(); 	
	break;	
	case 'send':
		$auth->isLoggedLogin();	
		define('DEBUG',false);		
		require MODULE.'send.php';	
	break;		
	case 'signal':
		$auth->isLoggedLogin();		
		require MODULE.'signal.php';	
	break;	
	case 'telnet':
		$auth->isLoggedLogin();		
		require MODULE.'telnet.php';	
	break;		
	case 'regonu':
		$auth->isLoggedLogin();		
		require MODULE.'regonu.php';	
	break;	
	case 'users':
		$auth->isLoggedLogin();		
		require MODULE.'users.php';	
	break;		
	case 'statusport':
		$auth->isLoggedLogin();		
		require MODULE.'statusport.php';	
	break;		
	case 'switchlog':
		$auth->isLoggedLogin();		
		require MODULE.'switchlog.php';	
	break;		
	case 'location':
		$auth->isLoggedLogin();		
		require MODULE.'location.php';	
	break;	
	case 'search':
		$auth->isLoggedLogin();		
		require MODULE.'search.php';	
	break;	
	case 'detail': 
		$auth->isLoggedLogin();	
		if($act=='olt')
			require MODULE.'olt.php';		
		if($act=='switch')
			require MODULE.'olt.php';	
	break;		
	case 'pondog': 
		$auth->isLoggedLogin();	
		require MODULE.'pondog.php';	
	break;		
	case 'onu': 
		$auth->isLoggedLogin();	
		require MODULE.'onu.php';	
	break;		
	case 'group': 
		$auth->isLoggedLogin();	
		require MODULE.'group.php';	
	break;	
	case 'terminal': 
		$auth->isLoggedLogin();	
		require MODULE.'terminal.php';	
	break;		
	case 'device': 
		$auth->isLoggedLogin();	
		require MODULE.'device.php';	
	break;		
	case 'setup': 
		$auth->isLoggedLogin();	
		require MODULE.'setup.php';	
	break;	
	case 'config': 
		$auth->isLoggedLogin();	
		require MODULE.'config.php';	
	break;		
	case 'billing': 
		$auth->isLoggedLogin();	
		require MODULE.'billing.php';	
	break;	
	case 'add':	
		$auth->isLoggedLogin();	
		require MODULE.'add.php';	
	break;		
	case 'map':	
		$auth->isLoggedLogin();	
		require MODULE.'map.php';	
	break;		
	case 'oid':	
		$auth->isLoggedLogin();	
		require MODULE.'oid.php';	
	break;	
	case 'mononu':	
		$auth->isLoggedLogin();	
		require MODULE.'mononu.php';	
	break;		
	case 'sklad':	
		$auth->isLoggedLogin();	
		if($config['sklad']=='on'){
			require ENGINE_DIR.'functions/sklad.php';
			require MODULE.'sklad.php';
		}			
	break;	
	case 'pon':	
		$auth->isLoggedLogin();	
		if($config['pon']=='on'){
			require ENGINE_DIR.'functions/pon.php';
			require MODULE.'pon.php';
		}else{
			
		}		
	break;	
	default:
		$auth->isLoggedLogin();		
		require MODULE.'main.php';	
}
?>
