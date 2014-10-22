<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/SentSmsManager.class.php");
require_once (CLASSES_PATH . "/managers/SmsGatewaysManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class SendSmsAction extends AdminAction {

    public function service() {
        $sentSmsManager = SentSmsManager::getInstance($this->config, $this->args);
        $smsGatewaysManager = SmsGatewaysManager::getInstance($this->config, $this->args);
        $phoneNumber = $this->secure($_REQUEST['phone_number']);
        $gatewayId = $this->secure($_REQUEST['gateway']);
        $gateway = $smsGatewaysManager->selectByPK($gatewayId);
        if (!isset($gateway)) {
            $this->error(array('message' => 'Wrong gateway!'));
        }


        $message = $_REQUEST['message'];
        if (empty($message)) {
            $this->error(array('message' => 'Message can not be empty!'));
        }
        $validNumber = SentSmsManager::getValidArmenianNumber($phoneNumber);
        if (!isset($validNumber)) {
            $this->error(array('message' => 'Invalid phone number!'));
        }

        $sentSmsManager->sendSmsToArmenia($phoneNumber, $message, $gateway->getName());
        $this->ok();
    }

}

?>