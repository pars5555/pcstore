<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/UserPendingSubUsersManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class RemoveUserFromUserInvitedUsersAction extends GuestAction {

	public function service() {
		$userPendingSubUsersManager = new UserPendingSubUsersManager($this->config, $this->args);
		$pendingUserEmail = $userPendingSubUsersManager->secure($_REQUEST["user_email"]);
		$userId = $this->sessionManager->getUser()->getId();


		if ($userPendingSubUsersManager->getByUserIdAndPendingSubUserEmail($userId, $pendingUserEmail)) {
			$userPendingSubUsersManager->removePendingSubUserFromUser($userId, $pendingUserEmail);
			$jsonArr = array('status' => "ok", "message" => "Invitation successfully removed!");
			echo json_encode($jsonArr);
			return true;
		} else {
			$jsonArr = array('status' => "err", "errText" => "System Error: Email dosn't exist in your pending emails list!");
			echo json_encode($jsonArr);
			return false;
		}
	}

	public function getRequestGroup() {
		return RequestGroups::$userRequest;
	}

}

?>