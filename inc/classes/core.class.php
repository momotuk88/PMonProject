<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
class Monitor{
	public function __construct($id, $phpclass){
		if(is_numeric($id)){
			$this->id = $id;	
			$this->device = new Equipment($id);	
			$this->getModel = $this->initclass($id, $phpclass, $this->device);
		}
	}
	protected function initclass($id, $optionclass, $oiddevice) {
		if(is_numeric($id)){
			switch ($optionclass) {
				case 'huawei5608t':
					return new HUAWEI_5608t($id, $oiddevice);				
				break;            
				case 'zte320_2':
					return new ZTE_c320_2($id, $oiddevice);					
				break;				
				case 'zte220_2':
					return new ZTE_c220_2($id, $oiddevice);					
				break;				
				case 'zte300_2':
					return new ZTE_c300_2($id, $oiddevice);					
				break;			
				case 'bdcomepon':
					return new BDCOM_Epon($id, $oiddevice);				
				break;				
				case 'dlinkdgs1106':
					return new DlinkDGS1106ME($id, $oiddevice);				
				break;			
				case 'bdcomgpon':
					return new BDCOM_Gpon($id, $oiddevice);
				break;	
				case 'cdataf1616sn':
					return new CDATA_1616sn($id, $oiddevice);				
				break;	
				case 'cdata1208sr2dap':
					return new CDATA_1208sr2dap($id, $oiddevice);
				break;			
				case 'cdataf1216s':
					return new CDATA_1216s($id, $oiddevice);
				break;			
				case 'cdata1108':
					return new CDATA_1108($id,$oiddevice);
				break;
				default:
					die('not support');
			}
		}else{
			die('empty id');
		}
    }
	public function start(){		
		$globalIndex  = $this->getModel->Load();		
		return $globalIndex;
	}	
	public function tempSaveEpon($dataOnu){
		$this->getModel->tempSaveOnuEpon($dataOnu);	
	}		
	public function savePort($dataPort){
		$this->getModel->savePort($dataPort);	
	}	
	public function tempSaveGpon($dataOnu){
		$this->getModel->tempSaveOnuGpon($dataOnu);	
	}	
	public function updateSignalCheck(){
		$this->getModel->tempUpdateSignalCheck($this->id);	
	}		
	public function tempSaveSignalEpon($dataOnu){
		$this->getModel->tempSaveSignalSaveOnuEpon($dataOnu);	
	}	
	public function tempSaveSignalGpon($dataOnu){
		$this->getModel->tempSaveSignalSaveOnuGpon($dataOnu);	
	}	
	public function getSupportPort(){
		return $this->getModel->Support('port');
	}	
	public function getSupportOnu(){
		return $this->getModel->Support('onu');
	}	
	public function getSupportSaveOnu(){
		return $this->getModel->Support('saveonu');
	}		
	public function saveOnuPmon(){
		return $this->getModel->saveOnuCommands();
	}	
	public function getPort(){
		return $this->getModel->Port();
	}
	public function getListSignal(){
		return $this->getModel->getListOnuOnline();	
	}	
	public function UpdateInformationOlt(){
		return $this->getModel->StatisticOLT();	
	}	
}
?>
