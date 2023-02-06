<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
class Monitor{
	public function __construct($id = '',$phpClass){
		$this->id = $id;	
		$this->device = new Equipment($id);	
		$this->getModel = $this->initclass($id,$phpClass,$this->device);
	}
	protected function initclass($id='',$optionClass,$OidDevice) {
        switch ($optionClass) {
			case 'huawei5600':
				return new Huawei5600($id,$OidDevice);				
            break;            
			case 'zte320_2':
				return new ZTE_c320_2($id,$OidDevice);					
            break;				
			case 'zte220_2':
				return new ZTE_c220_2($id,$OidDevice);					
            break;				
			case 'zte300_2':
				return new ZTE_c300_2($id,$OidDevice);					
            break;			
			case 'bdcomepon':
				return new BDCOM_Epon($id,$OidDevice);				
            break;				
			case 'dlinkdgs1106':
				return new DlinkDGS1106ME($id,$OidDevice);				
            break;			
			case 'bdcomgpon':
				return new BDCOM_Gpon($id,$OidDevice);
            break;			
			case 'cdata1208sr2dap':
				return new CDATA_1208sr2dap($id,$OidDevice);
            break;			
			case 'cdata1208sr1':
				return new CDATA_1208sr1($id,$OidDevice);
            break;			
			case 'cdataf1216s':
				return new CDATA_1216s($id,$OidDevice);
            break;			
			case 'cdata1108':
				return new CDATA_1108($id,$OidDevice);
            break;
			default:
				die('not support');
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
