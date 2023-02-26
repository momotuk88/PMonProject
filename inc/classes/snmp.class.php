<?php
if(!defined('PONMONITOR')){
	die('Hacking attempt!');
}
class SnmpMonitor { 
    protected $background = false;
    protected $cachetime = 60;	
    protected $snmptimeout = 1000000;
	protected $snmpretries = 5;
	protected $pathwalk = '';
	protected $pathget = '';
    const CACHE_FOLDER = 'export/snmpcache/'; 
    const LOG_OID = 'export/snmpcache/snmpdebug_oid.log';
    const LOG_COMMAND = 'export/snmpcache/snmpdebug_command.log';
    protected function loadConfig() {
        global $config;
        $this->configpmon = $config;
    }
    protected function configSnmpPmon() {
        if (!empty($this->configpmon)) {
            $this->pathwalk = $this->configpmon['pathwalk'];
            $this->pathget = $this->configpmon['pathget'];
            $this->mode = $this->configpmon['snmpmode'];
            $this->background = ($this->configpmon['background']) ? true : false;
            $this->cachetime = ($this->configpmon['cachetime'] * 60); 
            if($this->configpmon['debug']){
                $this->debug(true);
            }
        }
    }
    public function __construct($debug = false) {
        $this->loadConfig();
        $this->configSnmpPmon();
		if ($this->configpmon['debug']){
			$this->debug($debug);
		}
    }
    protected function debug($value) {
        if ($value)
            $this->debug = $value;
    }
    public function walk($ip, $comm, $oid, $cache = true) {
        switch ($this->mode) {
            case 'native':
                $result = $this->snmp_walk_native($ip,$comm,$oid,$cache);
            break;
            case 'exec':
                $result = $this->snmp_walk_exec($ip,$comm,$oid,$cache);
            break;
            case 'class':
                $result = $this->snmp_walk_class($ip,$comm,$oid,$cache);
			break;
        }
		if($this->configpmon['debug']=='true')
			$this->log_oid($ip, $comm, $oid);
        return ($result);
    }    
	public function get($ip, $comm, $oid) {	
        switch ($this->mode) {
            case 'native':
                $result = $this->snmp_get_native($ip,$comm,$oid);
            break;
            case 'exec':
                $result = $this->snmp_get_exec($ip,$comm,$oid);
            break;
            case 'class':
                $result = $this->snmp_get_class($ip,$comm,$oid);
			break;
        }
		if($this->configpmon['debug']=='true')
			$this->log_oid($ip, $comm, $oid, 'get');
        return ($result);
    }
    protected function snmp_walk_native($ip, $community, $oid, $cache = true) {
        $cachetime = time() - $this->cachetime;
        $cachepath = self::CACHE_FOLDER;
        $cacheFile = $cachepath . md5($ip.$oid);
        $result = '';
        if (file_exists($cacheFile)) {
            if ((filemtime($cacheFile) > $cachetime) AND ( $cache == true)) {
                $result = file_get_contents($cacheFile);
            } else {
                snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
                @$raw = snmpwalkoid($ip, $community, $oid, $this->snmptimeout, $this->snmpretries);
                if (!empty($raw)) {
                    foreach ($raw as $oid => $value) {
                        $result .= $oid . ' = ' . $value . "\n\n";
                    }
                } else {
                    @$value = snmp2_get($ip, $community, $oid, $this->snmptimeout, $this->snmpretries);
                    $result = $oid . ' = ' . $value;
                }
                file_put_contents($cacheFile, $result);
            }
        } else {
            snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
            @$raw = snmp2_real_walk($ip, $community, $oid, $this->snmptimeout, $this->snmpretries);
		   if (!empty($raw)) {
                foreach ($raw as $oid => $value) {
                    $result .= $oid . ' = ' . $value . "\n\n";
                }
            } else {
                @$value = snmp2_get($ip, $community, $oid, $this->snmptimeout, $this->snmpretries);
                $result = $oid . ' = ' . $value;
            }
        }
        return ($result);
    }
    protected function log_oid($ip, $community, $oid, $type = 'walk') {
        if ($this->debug) {
            $date = date("Y-m-d H:i:s");
            $oidLog = $date . ' snmp' . $type . ' host: ' . $ip . ' community: ' . $community . ' OID: ' . $oid . PHP_EOL;
            file_put_contents(self::LOG_OID, $oidLog, FILE_APPEND);
        }
    }
    protected function snmp_walk_system($ip, $community, $oid, $cache = true) {
        $command = $this->pathwalk . ' -c ' . $community . ' -Cc ' . $ip . ' ' . $oid;
        $cachetime = time() - $this->cachetime;
        $cachepath = self::CACHE_FOLDER;
        $cacheFile = $cachepath . md5($ip.$oid);
        $updateCache = true;
        $result = '';
        if ($this->background) {
            $command = $command . ' > ' . $cacheFile . '&';
        }
        if (file_exists($cacheFile)) {
            if ((filemtime($cacheFile) > $cachetime) AND ( $cache == true)) {
                $updateCache = false;
            } else {
                $updateCache = true;
            }
        } else {
            $updateCache = true;
        }
        if ($updateCache) {
            $result = shell_exec($command);
            if ($this->debug) {
                $date = date("Y-m-d H:i:s");
                $commandLog = '# ' . $date . PHP_EOL;
                $commandLog .= $command . PHP_EOL;
                file_put_contents(self::LOG_COMMAND, $commandLog, FILE_APPEND);
            }
            if (!$this->background) {
                file_put_contents($cacheFile, $result);
            }
        } else {
            $result = file_get_contents($cacheFile);
        }
        return ($result);
    }
    protected function snmp_walk_class($ip,$community,$oid,$cache = true){
        $cachetime = time() - $this->cachetime;
        $cachepath = self::CACHE_FOLDER;
        $cacheFile = $cachepath.md5($ip.$oid);
        $result = '';
        if(file_exists($cacheFile)){
            if((filemtime($cacheFile) > $cachetime) AND ($cache == true)){
               $result = file_get_contents($cacheFile);
            }else{
                snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
                $session = new SNMP(SNMP::VERSION_2C, $ip, $community, $this->snmptimeout, $this->snmpretries);
                $session->oid_increasing_check = false;
                $raw = $session->walk($oid);
                $session->close();
                if (!empty($raw)) {
                    foreach ($raw as $oid => $value) {
                        $result .= $oid . ' = ' . $value . "\n\n";
                    }
                }
                file_put_contents($cacheFile, $result);
            }
        }else{
            snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
            $session = @new SNMP(SNMP::VERSION_2C, $ip, $community, $this->snmptimeout, $this->snmpretries);
            $raw = @$session->walk($oid);
            $session->close();
            if (!empty($raw)) {
                foreach ($raw as $oid => $value) {
                    $result .= $oid . ' = ' . $value . "\n\n";
                }
            }
        }
        return ($result);
    }    
	protected function snmp_get_native($ip,$community,$oid){
		$result = '';
        snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
        @$value = snmp2_get($ip, $community, $oid, $this->snmptimeout, $this->snmpretries);
		if (!empty($value)){
			$result = $oid . ' = ' . $value;
		}
        return ($result);	
	}	
	protected function snmp_get_system($ip,$community,$oid){
		$result = '';
		$command = $this->pathget . ' -c ' . $community . ' -Cc ' . $ip . ' ' . $oid;
		$result = shell_exec($command);
        return ($result);	
	}
	protected function snmp_get_class($ip,$community,$oid){
        $result = '';
        snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
        $session = new SNMP(SNMP::VERSION_2C, $ip, $community,$this->snmptimeout,$this->snmpretries);
		$raw = $session->get($oid,true);
        $session->close();
        if (!empty($raw)) {
            $result .= $raw;
        }
        return (isset($result) ? $result : null);
    }
}
?>