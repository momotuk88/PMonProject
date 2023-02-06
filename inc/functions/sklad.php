<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
function skladInstallStatus($status){
	if($status=='yes'){
		return'<span class="skladnoinstall">встановлено</span>';
	}else{
		return'<span class="skladinstall"><b>встановлено</b></span>';
	}

}

