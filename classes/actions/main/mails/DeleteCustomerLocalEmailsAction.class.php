<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/CustomerLocalEmailsManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class DeleteCustomerLocalEmailsAction extends GuestAction {

    public function service() {

        $toTrash = isset($_REQUEST['to_trash']) && $_REQUEST['to_trash'] == 1 ? true : false;
        $delete = isset($_REQUEST['delete']) && $_REQUEST['delete'] == 1 ? true : false;

        $email_ids = $_REQUEST['email_ids'];
        $email_ids_array = explode(',', $email_ids);
        $customerLocalEmailsManager = CustomerLocalEmailsManager::getInstance($this->config, $this->args);
        if ($toTrash) {
            $customerLocalEmailsManager->trashEmails($this->getCustomerLogin(), $email_ids_array);
        } elseif ($delete) {
            $customerLocalEmailsManager->deleteEmails($this->getCustomerLogin(), $email_ids_array);
        }
        $jsonArr = array('status' => "ok");
        echo json_encode($jsonArr);
        return true;
    }

    public function getRequestGroup() {
        return RequestGroups::$userCompanyRequest;
    }

}

?>