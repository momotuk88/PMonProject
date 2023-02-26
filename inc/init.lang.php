<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
define('LANG',true);
class lang implements arrayaccess {
	private $lang_system = array();
	public function __construct() {
		require ENGINE_DIR.'/lang/ua.php';
		$this->lang_system = $nova_ukraine;
	}
    public function offsetSet(mixed $offset, $value): void {
        $this->lang_system[$offset] = $value;
    }
    public function offsetExists(mixed $offset): bool {
        return isset($this->lang_system[$offset]);
    }
    public function offsetUnset($offset): void {
        unset($this->lang_system[$offset]);
    }
    public function offsetGet(mixed $offset): string {
        return isset($this->lang_system[$offset]) ? $this->lang_system[$offset] : 'NO_LANG_'.strtoupper($offset);
    }
}
$lang = new lang;
