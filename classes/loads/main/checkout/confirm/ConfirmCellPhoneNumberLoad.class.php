<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/SentSmsManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ConfirmCellPhoneNumberLoad extends GuestLoad {

    public function load() {
        $this->addParam('pcstore_contact_number', $this->getCmsVar('pcstore_sales_phone_number'));
        $customer = $this->getCustomer();
        $sms_sent = false;
        if (isset($_REQUEST['co_code'])) {
            $code = $this->secure($_REQUEST['co_code']);
            $this->addParam('co_code', $code);
            if ($customer->getLastSmsValidationCode() === $code) {
                $this->addParam('order_confirmed', true);
            } else {
                $this->addParam('errorMessage', 223);
            }
            $this->addParam('sms_sent', true);
            return true;
        }

        $cell_phone_editable = $this->secure($_REQUEST['cho_do_shipping']) != 1;
        if ($cell_phone_editable) {
            $this->addParam('infoMessage', 362);
        }

        $cell_phone_number = $this->getPhoneNumberToSendSMS();
        $validNumber = null;
        if ($cell_phone_number != null) {
            $validNumber = SentSmsManager::getValidArmenianNumber($cell_phone_number);
        }
        if ($validNumber != null) {
            $lastSmsValidationCode = substr(uniqid(rand(), true), 0, 6);
            if ($this->getUserLevel() == UserGroups::$USER) {
                $userManager = UserManager::getInstance($this->config, $this->args);
                $userManager->setLastSmsValidationCode($customer->getId(), $lastSmsValidationCode);
            } elseif ($this->getUserLevel() == UserGroups::$COMPANY) {
                $companyManager = CompanyManager::getInstance($this->config, $this->args);
                $companyManager->setLastSmsValidationCode($customer->getId(), $lastSmsValidationCode);
            }
            $sentSmsManager = SentSmsManager::getInstance($this->config, $this->args);
            $sentSmsManager->sendSmsToArmenia($validNumber, $lastSmsValidationCode);
            $this->addParam('infoMessage', "`319` ($validNumber)");
            $this->addParam('validNumber', "(" . $validNumber . ")");

            $this->addParam('sms_sent', true);
        } else {
            if (!empty($cell_phone_number)) {
                $this->addParam('errorMessage', 318);
            }
            $this->addParam('cell_phone_number', $cell_phone_number);
            if (!$cell_phone_editable) {
                $this->addParam('infoMessage', 387);
            }
        }
        $this->addParam('cell_phone_editable', $cell_phone_editable);
    }

    /**
     * Returns validated phone number of customer from $_REQUET.
     */
    public function getPhoneNumberToSendSMS() {
        $cell_phone_editable = $this->secure($_REQUEST['cho_do_shipping']) == 1 ? false : true;
        if (!$cell_phone_editable) {
            if ($this->secure($_REQUEST['billing_is_different_checkbox']) == 1) {
                return $this->secure($_REQUEST['cho_billing_cell']);
            } else {
                return $this->secure($_REQUEST['cho_shipping_cell']);
            }
        } elseif (!empty($_REQUEST['send_to_cell_phone'])) {
            return $this->secure($_REQUEST['send_to_cell_phone']);
        }
        return null;
    }

    public function getDefaultLoads($args) {
        $loads = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/checkout/confirm/confirm_cell_phone_number.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$userCompanyRequest;
    }

}
