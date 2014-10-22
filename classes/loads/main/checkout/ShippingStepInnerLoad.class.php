<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/checkout/CheckoutManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ShippingStepInnerLoad extends GuestLoad {

    public function load() {
        $regions_phrase_ids_array = explode(',', $this->getCmsVar('armenia_regions_phrase_ids'));
        $this->addParam('regions_phrase_ids_array', $regions_phrase_ids_array);
        $this->addParam('req_params', $_REQUEST);
    }

    public function getDefaultLoads($args) {
        $loads = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/checkout/shipping_step_inner.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

}

?>