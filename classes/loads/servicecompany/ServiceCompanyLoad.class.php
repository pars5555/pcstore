<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");

abstract class ServiceCompanyLoad extends GuestLoad {

	public function isValidLoad($namespace, $load) {
		return true;
	}

	public function getRequestGroup() {
		return RequestGroups::$serviceCompanyRequest;
	}

	public function getDefaultLoads($args) {
		$loads = array();
		return $loads;
	}

}

?>