<?php

require_once (CLASSES_PATH . "/loads/admin/AdminLoad.class.php");
require_once (CLASSES_PATH . "/managers/SmsGatewaysManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class SendSmsLoad extends AdminLoad {

    public function load() {
        $smsGatewaysManager = SmsGatewaysManager::getInstance($this->config, $this->args);
        $allSmsGateways = $smsGatewaysManager->selectAll();
        $smsGatewaysNamesArray = array();
        foreach ($allSmsGateways as $allSmsGateway) {
            $smsGatewaysNamesArray[$allSmsGateway->getId()] = $allSmsGateway->getName();
        }
        $defaultSmsGateway = $this->getCmsVar('sms_gateway_name');
        $this->addParam('defaultSmsGateway', $defaultSmsGateway);
        $this->addParam('smsGatewaysNames', $smsGatewaysNamesArray);
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/admin/send_sms.tpl";
    }

}

?>