<?php
require_once(CLASSES_PATH."/lib/smarty/Smarty.class.php");


class FAZSmarty extends Smarty{

/**
* constructor
* reading Smarty config and setting up smarty environment accordingly
*/

	public function __construct($version = 0){
		$this->template_dir = TEMPLATES_DIR;
		$this->compile_dir  = COMPILE_DIR;
		$this->config_dir   = CONFIG_DIR;
		$this->cache_dir    = CACHE_DIR;
		//$this->debugging  = true;
		$protocol = "http://";
		if(isset($_SERVER["HTTPS"])){
			$protocol = "https://";	
		}
		
		$this->assign("SITE_URL", $_SERVER["HTTP_HOST"]);
		$this->assign("SITE_PATH", $protocol.$_SERVER["HTTP_HOST"]);
		$this->assign("TEMPLATE_DIR", TEMPLATES_DIR);
		$this->assign("SITE_PROTOCOL", $protocol);
		$this->assign("VERSION", $version);
	}
}

?>