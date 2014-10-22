<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/OnlineUsersManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class RemoveOldOnlineUsersAction extends GuestAction {

	public function service() {
		$onlineUsersManager = new OnlineUsersManager($this->config, $this->args);
		$onlineUsersManager->removeTimeOutedUsers(180); //3 minutes		
	}

	public function getRequestGroup() {
		return RequestGroups::$guestRequest;
	}

}

?>