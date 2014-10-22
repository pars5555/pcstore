<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/CustomerLocalEmailsManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class EmailBodyLoad extends GuestLoad {

    public function load() {
        $emailId = $_REQUEST['email_id'];
        $customerLocalEmailsManager = CustomerLocalEmailsManager::getInstance($this->config, $this->args);
        $emailDto = $customerLocalEmailsManager->selectByPK($emailId);
        if ($emailDto->getCustomerEmail() === $this->getCustomerLogin()) {
            $this->addParam("email_body_html", $emailDto->getBody());
            if ($emailDto->getReadStatus() == 0) {
                $customerLocalEmailsManager->setReadStatus($emailId, 1);
            }
        }

        $customer = $this->getCustomer();
        $customerEmail = $customer->getEmail();
        $unreadCount = $customerLocalEmailsManager->getCustomerInboxUnreadEmailsCountCustomerEmail($customerEmail);
        $this->addParam("unread_mails_count", $unreadCount);
    }

    public function getDefaultLoads($args) {
        $loads = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/mails/email_body.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$userCompanyRequest;
    }

}

?>