<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class UserManagementLoad extends GuestLoad {

	public function load() {
		$this->addParam("selected_tab_index", $this->args[0] == "invite" ? 1 : 0 );
	}

	public function getDefaultLoads($args) {
		$loads = array();

		$loadName1 = "PendingUsersListLoad";
		$loads["pending_users_list"]["load"] = "loads/main/" . $loadName1;
		$loads["pending_users_list"]["args"] = array("parentLoad" => &$this);
		$loads["pending_users_list"]["loads"] = array();
		

		$loadName2 = "SubUsersListLoad";
		$loads["sub_users_list"]["load"] = "loads/main/" . $loadName2;
		$loads["sub_users_list"]["args"] = array("parentLoad" => &$this);
		$loads["sub_users_list"]["loads"] = array();		
		
		return $loads;
	}

	public function isValidLoad($namespace, $load) {
		return true;
	}

	public function getTemplate() {
		return TEMPLATES_DIR . "/main/user_management.tpl";
	}

	public function getRequestGroup() {
		return RequestGroups::$userRequest;
	}

}

?>