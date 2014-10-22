<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ContactUsLoad extends GuestLoad {

    public function load() {
        $this->addParam("pcstore_contact_us_phone_number", $this->getCmsVar('pcstore_contact_us_phone_number'));

        if ($this->getUserLevel() !== UserGroups::$GUEST) {
            $userManager = UserManager::getInstance($this->config, $this->args);
            $customerEmail = $userManager->getRealEmailAddressByUserDto($this->getCustomer());
            $this->addParam("customer_email", $customerEmail);
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
        return TEMPLATES_DIR . "/main/contact_us.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

}

?>