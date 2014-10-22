<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class LoginDialogLoad extends GuestLoad {

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
		return TEMPLATES_DIR . "/main/login_dialog.tpl";
	}

	public function getRequestGroup() {
		return RequestGroups::$guestRequest;
	}

}

?>