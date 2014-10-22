<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/CustomerMessagesAfterLoginManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class SetShowCustomerAfterLoginMessageDontShowAnymoreAction extends GuestAction {

    public function service() {
        $message_ids = $this->secure($_REQUEST['message_ids']);
        $message_ids_array = explode(',', $message_ids);
        $customerMessagesAfterLoginManager = CustomerMessagesAfterLoginManager::getInstance($this->config, $this->args);
        foreach ($message_ids_array as $mId) {
            $customerMessagesAfterLoginManager->setMesssageNotShowAnyMoreToCustomer($mId);
        }
        $jsonArr = array('status' => "ok");
        echo json_encode($jsonArr);
        return true;
    }

    public function getRequestGroup() {
        return RequestGroups::$userRequest;
    }

}

?>