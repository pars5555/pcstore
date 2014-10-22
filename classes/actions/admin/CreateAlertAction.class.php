<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/CustomerMessagesAfterLoginManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class CreateAlertAction extends AdminAction {

    public function service() {
        $customerMessagesAfterLoginManager = CustomerMessagesAfterLoginManager::getInstance($this->config, $this->args);
        $customer_emails = $this->secure($_REQUEST['to']);
        if (empty($customer_emails)) {
            $this->error(array('message' => "Please select recipients!"));
        }

        $title_formula = $_REQUEST['title_formula'];
        if (empty($title_formula)) {
            $this->error(array('message' => "Please input message title formula!"));
        }
        $message_formula = $_REQUEST['message_formula'];
        if (empty($message_formula)) {
            $this->error(array('message' => "Please input message body formula!"));
        }
        $type = $this->secure($_REQUEST['type']);
        $shows_count = $this->secure($_REQUEST['shows_count']);

        $customer_emails_array = explode(',', $customer_emails);
        foreach ($customer_emails_array as $customer_email) {
            $dto = $customerMessagesAfterLoginManager->createDto();
            $dto->setType($type);
            $dto->setMessageFormula($message_formula);
            $dto->setTitleFormula($title_formula);
            $dto->setFromEmail($this->getCustomerLogin());
            $dto->setEmail($customer_email);
            $dto->setShowsCount($shows_count);
            $dto->setDatetime(date('Y-m-d H:i:s'));
            $customerMessagesAfterLoginManager->insertDto($dto);
        }
        $this->ok();
    }

}

?>