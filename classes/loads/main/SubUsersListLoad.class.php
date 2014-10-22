<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/UserSubUsersManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class SubUsersListLoad extends GuestLoad {

	public function load() {

		$userManager = UserManager::getInstance($this->config, $this->args);
		$userSubUsersManager = UserSubUsersManager::getInstance($this->config, $this->args);
		$userId = $this->getUserId();

		$subUsersDtos = $userSubUsersManager->getUserSubUsers($userId);

		$this->addParam("subUsers", $subUsersDtos);
		$this->addParam("userManager", $userManager);
	}

	public function getDefaultLoads($args) {
		$loads = array();
		return $loads;
	}

	public function isValidLoad($namespace, $load) {
		return true;
	}

	public function getTemplate() {
		return TEMPLATES_DIR . "/main/sub_users_list.tpl";
	}

	public function getRequestGroup() {
		return RequestGroups::$userRequest;
	}

}

?>