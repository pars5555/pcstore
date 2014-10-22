<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/CustomerLocalEmailsManager.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ComposeLoad extends GuestLoad {

    public function load() {
        $emailId = $_REQUEST['email_id'];
        $customerLocalEmailsManager = CustomerLocalEmailsManager::getInstance($this->config, $this->args);
        $userManager = UserManager::getInstance($this->config, $this->args);
        $emailDto = $customerLocalEmailsManager->selectByPK($emailId);
        if (isset($emailId) && isset($emailDto) && $emailDto->getCustomerEmail() === $this->getCustomerLogin()) {
            $this->addParam("email_subject", $emailDto->getSubject());
            $this->addParam("email_body", $emailDto->getBody());
            $customerEmail = $emailDto->getFromEmail();
            if (isset($_REQUEST['reply']) && $_REQUEST['reply'] == 1) {
                $this->addParam("email_to", $customerEmail);
                $customer = $userManager->getCustomerByEmail($customerEmail);
                if (isset($customer)) {
                    $customerContactNameForEmail = $customer->getCustomerContactNameForEmail();
                    $this->addParam("email_to_name", $customerContactNameForEmail);
                    $customerTypeString = $userManager->getCustomerTypeStringFromCustomerDto($customer);
                    $this->addParam("email_to_type", $customerTypeString);
                }
            }
            if ($emailDto->getReadStatus() == 0) {
                $customerLocalEmailsManager->setReadStatus($emailId, 1);
            }
        }
    }

    public function getDefaultLoads($args) {
        $loads = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/mails/compose.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$userCompanyRequest;
    }

}

?>