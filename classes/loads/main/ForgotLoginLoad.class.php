<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ForgotLoginLoad extends GuestLoad {

	public function load() {
		$this->addParam('login', $_REQUEST['email']);
	}

	public function getDefaultLoads($args) {
		$loads = array();
		return $loads;
	}

	public function isValidLoad($namespace, $load) {
		return true;
	}

	public function getTemplate() {
		return TEMPLATES_DIR . "/main/forgot_login.tpl";
	}

	public function getRequestGroup() {
		return RequestGroups::$guestRequest;
	}

}

?>