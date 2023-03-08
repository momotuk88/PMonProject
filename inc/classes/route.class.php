<?php
if (!defined('PONMONITOR')){
	die('System Error Attempt!');
}
class Route{
    public function redirect($type,$page=false){
		$act = $this->type($type);
		switch($act){
			case 'unit': 
				$this->go('/?do=pon&act=unit');
			break;				
			case 'location': 
				$this->go('/?do=location');
			break;				
			case 'config': 
				$this->go('/?do=config');
			break;				
			case 'group': 
				$this->go('/?do=group');
			break;				
			case 'sklad': 
				$this->go('/?do=sklad');
			break;			
			case 'device': 
				$this->go('/?do=device');
			break;			
			case 'users': 
				$this->go('/?do=users');
			break;			
			case 'billing': 
				$this->go('/?do=billing');
			break;			
			case 'pondog': 
				$this->go('/?do=pondog');
			break;			
			case 'oid': 
				$this->go('/?do=oid');
			break;				
			case 'main': 
				$this->go('/index.php');
			break;				
		}
	}	    
	public function type($type){
		return $type;
	}	
	public function url($url){
		header('location: /?do='.$url);
		exit;
	}		
	public function go($url){
		header_remove(); // видаляємо будь-які заголовки, які можуть бути встановлені раніше
		header('Location: '.$url);
		exit;
	}	
}