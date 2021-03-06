<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/CustomerLocalEmailsManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class SentLoad extends GuestLoad {

    public function load() {
        $customerLocalEmailsManager = CustomerLocalEmailsManager::getInstance($this->config, $this->args);
        $customer = $this->getCustomer();
        $customerEmail = $customer->getEmail();
        $emails = $customerLocalEmailsManager->getCustomerSentEmailsByCustomerEmail($customerEmail);
        $this->addParam("emails", $emails);
    }

    public function getDefaultLoads($args) {
        $loads = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/mails/sent.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$userCompanyRequest;
    }

}

?>