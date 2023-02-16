<?php
if(!defined('PONMONITOR')){
	die('Hacking attempt!');
}
class Ont{
	public function __construct($id = '',$phpClass){
		$this->id = $id;	
		$this->device = new Equipment($id);	
		$this->getModel = $this->initclass($id,$phpClass,$this->device);
	}
	protected function initclass($id='',$optionClass,$OidDevice) {
        switch ($optionClass) {
			case 'cdataf1616sn':
				return new CDATA_1616sn($id,$OidDevice);				
            break;			
			case 'huawei5608t':
				return new HUAWEI_5608t($id,$OidDevice);				
            break;            
			case 'zte320_2':
				return new ZTE_c320_2($id,$OidDevice);					
            break;				
			case 'zte300_2':
				return new ZTE_c300_2($id,$OidDevice);					
            break;           
			case 'zte320_2':
				return new ZTE_c320_2($id,$OidDevice);					
            break;			
			case 'bdcomepon':
				return new BDCOM_Epon($id,$OidDevice);				
            break;			
			case 'bdcomgpon':
				return new BDCOM_Gpon($id,$OidDevice);					
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
	public function LogOnu(){
		global $db;
		
	}
	public function Support(){
		return $this->getModel->Support('onu');	
	}	
	public function getApi($dataOnu){
		$configApi = $this->getModel->ConfigApiOnu($dataOnu);
		$resultApi = get_curl_api($configApi,true);	
		return $this->getModel->Onu($resultApi,$dataOnu);
	}
}
?>
