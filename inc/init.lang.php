<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
define('LANG',true);
class lang implements ArrayAccess {
    private array $lang_system = [];
    private array $languages = array('ua', 'ru', 'en');
    public function __construct() {
		$sellang = $_GET['lang'] ?? $_COOKIE['lang'] ?? 'ua';
		if (!in_array($sellang, $this->languages)) {
			$sellang = 'ua';
		}
		setcookie('lang', $sellang, time() + (86400 * 30), "/");
        require ENGINE_DIR.'/lang/'.$sellang.'.php';
        $this->lang_system = $nova_ukraine;
    }
    public function offsetSet(mixed $offset, mixed $value): void {
        $this->lang_system[$offset] = $value;
    }
    public function offsetExists(mixed $offset): bool {
        return isset($this->lang_system[$offset]);
    }
    public function offsetUnset(mixed $offset): void {
        unset($this->lang_system[$offset]);
    }
    public function offsetGet(mixed $offset): string {
        return isset($this->lang_system[$offset]) ? $this->lang_system[$offset] : 'NO_LANG_'.strtoupper((string) $offset);
    }
}
$langbutton = '<div class="languages"><a href="/?lang=ua"><img src="../style/img/ua.png"></a><a href="/?lang=en"><img src="../style/img/en.png"></a></div>';
$lang = new lang();
?>
