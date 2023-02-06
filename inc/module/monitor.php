<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}

$SQLDevMon = $db->Multi('monitor');
if(count($SQLDevMon)){
	$tplRes .='<div class="monitor">';
	foreach($SQLDevMon as $MonData){
		$tplRes .='<div class="device"><div class="name">';
		$tplRes .='<div class="delmonitor"><i class="fi fi-rr-cross"></i></div>';		
		$tplRes .='<h2><div class="status"><div class="st'.$MonData['status'].'">'.$MonData['status'].'</div></div>'.$MonData['name'].'</h2>';		
		$tplRes .='<div class="types"><i class="fi fi-rr-copy"></i><b>пристрій:</b><span>'.$MonData['types'].'</span></div>';
		$tplRes .='<div class="online"><i class="fi fi-rr-globe"></i><b>онлайн:</b><span>4 год 34 хв</span></div>';
		$tplRes .='<div class="check"><i class="fi fi-rr-clock"></i><b>перевірено:</b><span>5 хв назад</span></div>';
		$tplRes .='<div class="subs">sdgfhsjdufkyiglo</div>';
		$tplRes .='</div></div>';			
		
		/////////////
		$tplRes .='<div class="device"><div class="name">';
		$tplRes .='<div class="delmonitor"><i class="fi fi-rr-cross"></i></div>';		
		$tplRes .='<h2><div class="status"><div class="st'.$MonData['status'].'">'.$MonData['status'].'</div></div>'.$MonData['name'].'</h2>';		
		$tplRes .='<div class="types"><i class="fi fi-rr-copy"></i><b>пристрій:</b><span>'.$MonData['types'].'</span></div>';
		$tplRes .='<div class="online"><i class="fi fi-rr-globe"></i><b>онлайн:</b><span>4 год 34 хв</span></div>';
		$tplRes .='<div class="check"><i class="fi fi-rr-clock"></i><b>перевірено:</b><span>5 хв назад</span></div>';
		if($MonData['types']=='onu'){
			$tplRes .='<div class="subs">';			
			$tplRes .='<span><b>Комутатор</b>Подяна квасова</span>';
			$tplRes .='<span><b>Порт</b>EPON 0/1</span>';
			$tplRes .='<span><b>Сигнал:</b>-21 dbm</span>';
			$tplRes .='</div>';
		}
		$tplRes .='</div></div>';		
		
		
	}
	$tplRes .='</div>';
}else{
	$tplRes .='<div class="empty_connect"><i class="fi fi-rr-comment-info"></i>Прикольно</div>';
}	
$tpl->load_template('monitor/main.tpl');
$tpl->set('{block-main}',$tplRes);
$tpl->compile('content');
$tpl->clear();
?>