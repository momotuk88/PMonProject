<?php
if (!defined('PONMONITOR')){
	die('System Billing Error Attempt!');
}
class Auth {
    var $type = 'cookie';
    private $connection;
    private $errors = array();
    var $minval = 4;
    var $maxval = 22;
    var $members = array();
    var $minpass = 6;
    var $salt = '#@()DIJK#)(F#&*()DS#@JKS)@(I()#@DU)*(&@#)(#U)J';
    var $emailAuth = false; 
    function __construct() {
        if ( $this->type == 'session') {
            session_start();
        }
        $this->check();
        $this->inSession();
    } 
    public function xss_uni($message) {
		$message = preg_replace("#&(?!\#[0-9]+;)#si", "&amp;", $message); // Fix & but allow unicode
		$message = str_replace("<","&lt;",$message);
		$message = str_replace(">","&gt;",$message);
		$message = str_replace("{","&gt;",$message);
		$message = str_replace("}","&gt;",$message);
		$message = str_replace("\"","&quot;",$message);
		$message = str_replace("  ", "&nbsp;&nbsp;", $message);
		return $message;
	}
    public function login($user,$pass) {
		global $db;
        $email = $this->emailAuth;
        $err = false;
        $user = Clean::text($user);
        $password = $this->encrypt($pass);
        if ( $email == true ) {
            if ( !$this->email($user) ) {
                $this->errors[] = 'Email invalid.';
                $err = true;
            } else {
                $col = 'email';
            }
        } else {
            if ( !$this->name($user) ) {
                $this->errors[] = 'Name invalid. Min chars: '.$this->minval.'. Max chars: '.$this->maxval;
                $err = true;
            } else {
                $col = 'username';
            }
        }
        if ( strlen($pass) < $this->minpass) {
            $this->errors[] = 'Password min value is 6 chars.';
            $err = true;
        } 
        if ($err == false){ 
            $row = $db->Fast('users','*',[$col=>$user]);
            if (!$row['id']) {
                $this->errors[] = ucfirst($col).' doesn\'t exist.';
            }
			if(!empty($row['password']) || !empty($row['username']) || !empty($row['email'])){
                if ($row['password']==$password) {
                    if ($this->type == 'session') {
                        $this->set_session($col, $user);
                        $this->set_session('password',$password);
                    } elseif($this->type == 'cookie') {
                        $this->set_cookie($col, $user);
                        $this->set_cookie('password',$password);
                    }
					if(!empty($row['id']))
						$this->set_cookie('uid',$row['id']);
                    header('Location: /?do=main');
                } else {
                    $this->errors[] = 'Incorrect password';
                }
            } 
        }
    } 
    public function generationHash($pass) {
		return $this->encrypt($pass);
	}
    private function encrypt($value) {
        $enc = md5($this->salt.md5($value));
        return sha1($enc);
    } 
    // Email validation
    private function email($email) {
        $reg = "#^(((([a-z\d][\.\-\+_]?)*)[a-z0-9])+)\@(((([a-z\d][\.\-_]?){0,62})[a-z\d])+)\.([a-z\d]{2,6})$#i";
        if ( !preg_match($reg, $email) ) {
            return false;
        } else {
            return true;
        }
    } 
    // Name validation
    private function name($name) {
        $min = $this->minval - 2;
        if ( !preg_match("#^[a-z][\da-z_]{".$min.",".$this->maxval."}[a-z\d]\$#i", $name) ) {
            return false;
        } else {
            return true;
        }
    } 
    private function set_session($name, $value) {
        $_SESSION[$name] = $value;
    } 
    private function destroy_session() {
        session_unset();
        session_destroy();
    } 
    private function set_cookie($name, $value, $time = 7200) {
        setcookie($name,$value,time()+$time, '/');
    } 
    private function destroy_cookie($name , $expires = '') {
        setcookie($name, '', time()-1, '/');
        #setcookie($name, '',0x7fffffff,'/');
    } 
    public function logout() {
        $col = $this->metodLogin();
        if($this->type=='session'){
            $this->destroy_session();
        }elseif ($this->type=='cookie') {
            $this->destroy_cookie('password');
            $this->destroy_cookie($col);
        }
		header('Location: /?do=login');
		exit();
    } 
    private function metodLogin() {
		if ($this->emailAuth == false ) {
			return 'username';
        } else {
            return 'email';
        }
	}
    public function us_ip() {
		return getenv('REMOTE_ADDR');
	}	
    public function members() {
		global $db, $time;
		if($this->isLoggedHeader()){
			if($this->member[0]['onlyip']=='on' && $this->member[0]['setip']!==self::us_ip()){
				die('Attempt to define too many indexes for area 6 database DBI (40) Version: 10.2Bx, 11.0 OS: All supported platforms');
			}
			if(!empty($this->member[0]['id'])){
				$SQLUpdate['ip'] = self::us_ip();
				$url = getenv('REQUEST_URI');	
				if($url)
					$SQLUpdate['url'] = $this->xss_uni($url);
			}
			if(is_array($SQLUpdate) && !empty($this->member[0]['id'])){
				$SQLUpdate['lastactivity'] = $time;
				$db->SQLupdate('users',$SQLUpdate,['id'=>$this->member[0]['id']]);
			}
			return $this->member[0];
		}else{
			return false;
		}
	}
    private function check() {
		global $db;
		$col = $this->metodLogin();
        if($this->type=='cookie'){
            if(isset($_COOKIE['password'])){
                $row = $db->Fast('users','*',[$col=>$_COOKIE[$col]]);
				$this->member[] = $row;
                if ($row[$col] !== $_COOKIE[$col] || $row['password'] !== $_COOKIE['password'] ) {
                    $this->logout();
                }
            } 
        }elseif($this->type=='session') {
            if (isset($_SESSION['password'])) {
                $row = $db->Fast('users','*',[$col=>$_SESSION[$col]]);
				$this->member[] = $row;
				$this->member[] = $row;
                if ($row[$col] !== $_SESSION[$col] || $row['password'] !== $_SESSION['password'] ) {
                    $this->logout();
                }
            }
        }
    } 
    public function error() {
        if ( is_array($this->errors) && !empty($this->errors) ) {
            echo '<div class="errorlogin"><i class="fi fi-rr-info"></i>';
            foreach ( $this->errors as $value ) {
                echo $value."<br />";
            }
            echo '</div>';
        }
    } 
    function get_ip() {
		return getenv('REMOTE_ADDR');
	}	
    public function inSession() {
		global $db;

	}
	public function isLoggedHeader() {
        $ret = false;
        $col = $this->metodLogin();
        if ( $this->type == 'cookie' ) {
            if (isset($_COOKIE['password']) ) {
                return true;
            }
        }elseif($this->type == 'session' ) {
            if(isset($_SESSION['password']) ) {
                return true;
            }
        }
		return false;
    }	
	public function isLoggedLogin() {
        $ret = false;
        $col = $this->metodLogin();
        if ( $this->type == 'cookie' ) {
            if ( isset($_COOKIE['password']) ) {
                $ret = true;
            }
        }elseif($this->type == 'session' ) {
            if(isset($_SESSION['password']) ) {
                $ret = true;
            }
        }
		if(!$ret)
			header('Location: /?do=login');
    } 
}
$USER = array();
$auth = new Auth(); 
$USER = $auth->members();
?>