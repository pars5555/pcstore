<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/UserPendingSubUsersManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class PendingUsersListLoad extends GuestLoad {

	public function load() {
		$userPendingSubUsersManager = UserPendingSubUsersManager::getInstance($this->config, $this->args);
		$userId = $this->getUserId();
		$pending_users = $userPendingSubUsersManager->getByUserIdOrderByDate($userId);
		$this->addParam("pendingUsers", $pending_users);
	}

	public function getDefaultLoads($args) {
		$loads = array();
		return $loads;
	}

	public function isValidLoad($namespace, $load) {
		return true;
	}

	public function getTemplate() {
		return TEMPLATES_DIR . "/main/pending_users_list.tpl";
	}

	public function getRequestGroup() {
		return RequestGroups::$userRequest;
	}

}

?>