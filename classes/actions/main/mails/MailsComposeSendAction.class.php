<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/CustomerLocalEmailsManager.class.php");
require_once (CLASSES_PATH . "/managers/CustomerAlertsManager.class.php");
require_once (CLASSES_PATH . "/managers/OnlineUsersManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class MailsComposeSendAction extends GuestAction {

    public function service() {
        $to_emails = $_REQUEST['to_emails'];
        $to_emails_array = explode(',', $to_emails);
        $subject = $_REQUEST['subject'];
        $body = $_REQUEST['body'];
        $customerLocalEmailsManager = CustomerLocalEmailsManager::getInstance($this->config, $this->args);
        $customerLocalEmailsManager->sendEmail($this->getCustomerLogin(), $to_emails_array, $subject, $body);
        $fromName = $this->getCustomer()->getCustomerContactNameForEmail();
        $this->addEventIntoEventsTableForOnlineCustomers($to_emails_array, $fromName, $subject);
        $jsonArr = array('status' => "ok");
        echo json_encode($jsonArr);
        return true;
    }

    public function addEventIntoEventsTableForOnlineCustomers($to_emails_array, $fromName, $subject) {
        $customerAlertsManager = CustomerAlertsManager::getInstance($this->config, $this->args);
        $onlineUsersManager = OnlineUsersManager::getInstance($this->config, $this->args);
        $customerLocalEmailsManager = CustomerLocalEmailsManager::getInstance($this->config, $this->args);
        $onlineRegisteredCustomers = $onlineUsersManager->getOnlineRegisteredCustomers();
        foreach ($onlineRegisteredCustomers as $onlineUsersDto) {
            $custEmail = $onlineUsersDto->getEmail();
            if (in_array($custEmail, $to_emails_array)) {
                $customerInboxUnreadEmailsCount = $customerLocalEmailsManager->getCustomerInboxUnreadEmailsCountCustomerEmail($custEmail);
                $customerAlertsManager->addNewEmailCustomerAlert($custEmail, $fromName, $subject, $customerInboxUnreadEmailsCount, $onlineUsersDto->getLanguageCode());
            }
        }
    }

    public function getRequestGroup() {
        return RequestGroups::$userCompanyRequest;
    }

}

?>