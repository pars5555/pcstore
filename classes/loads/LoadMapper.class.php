<?php

require_once(CLASSES_PATH . "/framework/AbstractLoadMapper.class.php");

class LoadMapper extends AbstractLoadMapper {

	public $isStatic = false;
	private $PROTOCOL;
	private $SITE_URL;
	private $SITE_PATH;
	

	public function initialize() {
		$this->PROTOCOL = "http://";
		if (isset($_SERVER["HTTPS"])) {
			$this->PROTOCOL = "https://";
		}

		$this->SITE_URL = $_SERVER["HTTP_HOST"];
		$this->SITE_PATH = $this->PROTOCOL . $this->SITE_URL;
		
	}

	public function getDynamicLoad($url, $matches) {
		return null;
	}

	public function __get($nm) {
		return $this->__call($nm, array());
	}

	public function __call($nm, $arguments) {
		$url = null;
		return $url;
	}

	public function getCurrentLoads() {

		$loads = array();
		if (isset($_REQUEST["p"])) {
			$pageName = $_REQUEST["p"];
			$loadName = ucfirst($pageName);
			$loadName = preg_replace("/_(\w)/e", "strtoupper('\\1')", $loadName);
			$loadName .= "Load";
		}
		return $loads;
	}

}

?>