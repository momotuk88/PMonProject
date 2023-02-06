<?php
if(!defined('PONMONITOR')){
	die("Hacking attempt!");
}
class TemplateMonitor{ 	
	var $folder = 'style/tpl';
	var $template = null;
	var $copy_template = null;
	var $desktop = true;
	var $data = array ();
	var $block_data = array ();
	var $result = array ('info' => '','content' => '' );
	var $allow_php_include = true;
	var $include_mode = 'tpl';	
	var $template_parse_time = 0;	
	function set($name, $var) {		
		if( is_array( $var ) ) {
			if( count( $var ) ) {
				foreach ( $var as $key => $key_var ) {
					$this->set( $key, $key_var );
				}
			}
			return;
		}		
		#$var = str_replace(array("{", "["),array("_&#123;_", "_&#91;_"), $var);			
		$this->data[$name] = $var;		
	}
	function set_block($name, $var) {
		if( is_array( $var ) && count( $var ) ) {
			foreach ( $var as $key => $key_var ) {
				$this->set_block( $key, $key_var );
			}
		} else
			$var = str_replace(array("{", "["),array("_&#123;_", "_&#91;_"), $var);			
			$this->block_data[$name] = $var;
	}	
	function load_template($tpl_name) {
		$time_before = $this->get_real_time();		
		$tpl_name = str_replace(chr(0), '', $tpl_name);
		$url = @parse_url ( $tpl_name );
		$file_path = dirname ($this->clear_url_dir($url['path']));
		$tpl_name = pathinfo($url['path']);
		$tpl_name = totranslit($tpl_name['basename']);
		$type = explode( ".", $tpl_name );
		$type = strtolower( end( $type ) );
		if ($type != "tpl") {
			$this->template = "Not Allowed Template Name: " .str_replace(ROOT_DIR, '', $this->folder)."/".$tpl_name ;
			$this->copy_template = $this->template;
			return "";
		}
		if ($file_path AND $file_path != ".") $tpl_name = $file_path."/".$tpl_name;
		if( stripos ( $tpl_name, ".php" ) !== false ) {
			$this->template = "Not Allowed Template Name: " .str_replace(ROOT_DIR, '', $this->folder)."/".$tpl_name ;
			$this->copy_template = $this->template;
			return "";
		}
		if( $tpl_name == '' || !file_exists( $this->folder . "/" . $tpl_name ) ) {
			$this->template = "Template not found: " .str_replace(ROOT_DIR, '', $this->folder)."/".$tpl_name ;
			$this->copy_template = $this->template;
			return "";
		}
		$this->template = file_get_contents( $this->folder . "/" . $tpl_name );
		$this->template = $this->check_module($this->template);
		$this->copy_template = $this->template;
		$this->template_parse_time += $this->get_real_time() - $time_before;
		return true;
	}
	function load_file( $matches=array() ) {
		global $db, $config, $sys_file;
		$name = $matches[1];
		$name = str_replace( chr(0), "", $name );
		$name = str_replace( '..', '', $name );
		$url = @parse_url ($name);
		$type = explode( ".", $url['path'] );
		$type = strtolower( end( $type ) );		
		if ($type == "tpl") {
			return $this->sub_load_template( $name );
		}
		if ($this->include_mode == "php") {
			if ( !$this->allow_php_include ) return;

			if ($type != "php") return "To connect permitted only files with the extension: .tpl or .php";

			if ($url['path'][0] == "/" )
				$file_path = dirname (ROOT_DIR.$url['path']);
			else
				$file_path = dirname (ROOT_DIR."/".$url['path']);

			$file_name = pathinfo($url['path']);
			$file_name = $file_name['basename'];
			ob_start();
			$tpl = new Template; 
			$tpl->folder = 'tpl/'.$config['skin'];
			include $file_path."/".$file_name;
			return ob_get_clean();
		}
		return '{include file="'.$name.'"}';
	}
	function sub_load_template( $tpl_name ) {		
		$tpl_name = str_replace(chr(0), '', $tpl_name);		
		$url = @parse_url ( $tpl_name );
		$file_path = dirname ($this->clear_url_dir($url['path']));
		$tpl_name = pathinfo($url['path']);
		$tpl_name = totranslit($tpl_name['basename']);
		$type = explode( ".", $tpl_name );
		$type = strtolower( end( $type ) );
		if ($type != "tpl") {
			return "Not Allowed Template Name: ". $tpl_name;
		}
		if ($file_path AND $file_path != ".") $tpl_name = $file_path."/".$tpl_name;
		if (strpos($tpl_name, '/tpl/') === 0) {
			$tpl_name = str_replace('/tpl/','',$tpl_name);
			$templatefile = ROOT_DIR . '/tpl/'.$tpl_name;
		} else 
			$templatefile = $this->folder . "/" . $tpl_name;

		if( $tpl_name == '' || !file_exists( $templatefile ) ) {
			$templatefile = str_replace(ROOT_DIR,'',$templatefile);
			return "Template not found: " . $templatefile ;
			return false;
		}
		if( stripos ( $templatefile, ".php" ) !== false ) return "Not Allowed Template Name: ". $tpl_name;

		$template = file_get_contents( $templatefile );		
		$template = $this->check_module($template);	
		return $template;
	}

	function clear_url_dir($var) {
		if ( is_array($var) ) return "";
		$var = str_ireplace( ".php", "", $var );
		$var = str_ireplace( ".php", ".ppp", $var );
		$var = trim( strip_tags( $var ) );
		$var = str_replace( "\\", "/", $var );
		$var = preg_replace( "/[^a-z0-9\/\_\-]+/mi", "", $var );
		$var = preg_replace( '#[\/]+#i', '/', $var );
		return $var;	
	}
	function check_module($matches) {
		global $sys_file;
		$regex = '/\[(aviable|available|not-aviable|not-available)=(.+?)\]((?>(?R)|.)*?)\[\/\1\]/is';
		if (is_array($matches)) {
			$aviable = $matches[2];
			$block = $matches[3];
			if( $action ) {				
				if( ! (in_array( $sys_file, $aviable )) and ($aviable[0] != "global") ) $matches = '';
				else $matches = $block;			
			} else {				
				if( (in_array( $sys_file, $aviable )) ) $matches = '';
				else $matches = $block;
			}		
		}	
		return preg_replace_callback($regex, array( &$this, 'check_module'), $matches);
	}
	function _clear() {
		$this->data = array ();
		$this->block_data = array ();
		$this->copy_template = $this->template;
	}
	function clear() {
		$this->data = array ();
		$this->block_data = array ();
		$this->copy_template = null;
		$this->template = null;
	}
	function global_clear() {
		$this->data = array ();
		$this->block_data = array ();
		$this->result = array ();
		$this->copy_template = null;
		$this->template = null;
	}	
	function compile($tpl) {
		$time_before = $this->get_real_time();
		if( count( $this->block_data ) ) {
			foreach ( $this->block_data as $key_find => $key_replace ) {
				$find_preg[] = $key_find;
				$replace_preg[] = $key_replace;
			}
			$this->copy_template = preg_replace( $find_preg, $replace_preg, $this->copy_template );
		}
		foreach ( $this->data as $key_find => $key_replace ) {
			$find[] = $key_find;
			$replace[] = $key_replace;
		}
		$this->copy_template = str_ireplace( $find, $replace, $this->copy_template );
		if( isset( $this->result[$tpl] ) ) $this->result[$tpl] .= $this->copy_template;
		else $this->result[$tpl] = $this->copy_template;
		$this->_clear();
		$this->template_parse_time += $this->get_real_time() - $time_before;
	}
	function get_real_time() {
		list ( $seconds, $microSeconds ) = explode( ' ', microtime() );
		return (( float ) $seconds + ( float ) $microSeconds);
	}
}

?>