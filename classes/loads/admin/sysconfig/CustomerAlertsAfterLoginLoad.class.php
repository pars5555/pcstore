<?php

require_once (CLASSES_PATH . "/loads/admin/AdminLoad.class.php");
require_once (CLASSES_PATH . "/managers/CustomerMessagesAfterLoginManager.class.php");

class CustomerAlertsAfterLoginLoad extends AdminLoad {

    public function load() {
        $customerMessagesAfterLoginManager = CustomerMessagesAfterLoginManager::getInstance($this->config, $this->args);
        $dtos = $customerMessagesAfterLoginManager->getByFromEmail($this->getCustomerLogin());
        $this->addParam('dtos', $dtos);
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/admin/sysconfig/customer_alerts_after_login.tpl";
    }

}

?>