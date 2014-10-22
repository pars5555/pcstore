<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");

/**
 * @author Vahagn Sookiasian
 */
class OrderCancelAction extends GuestAction {

    public function service() {
        $_SESSION['error_message'] = $this->getPhrase(594);
        $this->redirect('orders');
    }

    public function getRequestGroup() {
        return RequestGroups::$userRequest;
    }

}

?>