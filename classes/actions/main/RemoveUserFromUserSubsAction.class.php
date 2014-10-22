<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/UserSubUsersManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class RemoveUserFromUserSubsAction extends GuestAction {

    public function service() {
        $userSubUsersManager = new UserSubUsersManager($this->config, $this->args);
        $subUserId = $userSubUsersManager->secure($_REQUEST["user_id"]);
        $userId = $this->sessionManager->getUser()->getId();

        if ($userSubUsersManager->getByUserIdAndSubUserId($userId, $subUserId)) {
            $userSubUsersManager->removeSubUserFromUser($subUserId, $userId);
            $jsonArr = array('status' => "ok", "message" => "Sub user removed!");
            echo json_encode($jsonArr);
            return true;
        } else {
            $jsonArr = array('status' => "err", "errText" => "System Error: Sub user dosn't exist in your sub users list!");
            echo json_encode($jsonArr);
            return false;
        }
    }

    public function getRequestGroup() {
        return RequestGroups::$userRequest;
    }

}

?>