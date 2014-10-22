<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");

abstract class AdminLoad extends GuestLoad {

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getRequestGroup() {
        return RequestGroups::$adminRequest;
    }

    public function getDefaultLoads($args) {
        $loads = array();
        return $loads;
    }

}

?>