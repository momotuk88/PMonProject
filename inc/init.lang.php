<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
define('LANG',true);
class lang implements arrayaccess {
	private $lang_system = array();
	public function __construct() {
		global $nova_ukraine, $config;
		require ENGINE_DIR.'/lang/'.(!empty($config['mova']) && $config['mova'] : 'ua').'.php';
		$this->lang_system = $nova_ukraine;
	}
    public function offsetSet($offset, $value) {
        $this->lang_system[$offset] = $value;
    }
    public function offsetExists($offset) {
        return isset($this->lang_system[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->lang_system[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->lang_system[$offset]) ? $this->lang_system[$offset] : 'NO_LANG_'.strtoupper($offset);
    }
}
$lang = new lang;
