<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class LoginStepInnerLoad extends GuestLoad {

	public function load() {
		
	}

	public function getDefaultLoads($args) {
		$loads = array();
		return $loads;
	}

	public function isValidLoad($namespace, $load) {
		return true;
	}

	public function getTemplate() {
		return TEMPLATES_DIR . "/main/checkout/login_step_inner.tpl";
	}

	public function getRequestGroup() {
		return RequestGroups::$guestRequest;
	}

}

?>