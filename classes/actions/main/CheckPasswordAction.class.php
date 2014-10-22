<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");

/**
 * @author Vahagn Sookiasian
 */
class CheckPasswordAction extends GuestAction {

    public function service() {

        $pass = $this->secure($_REQUEST['pass']);
        $customer = $this->getCustomer();
        $customerRealPassword = $customer->getPassword();
        if ($customerRealPassword === $pass) {
            $jsonArr = array('status' => "ok");
            echo json_encode($jsonArr);
            return true;
        }
        $jsonArr = array('status' => "err");
        echo json_encode($jsonArr);
        return false;
    }

    public function getRequestGroup() {
        return RequestGroups::$userCompanyRequest;
    }

}

?>