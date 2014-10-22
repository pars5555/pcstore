<?php

require_once (CLASSES_PATH . "/loads/Load.class.php");

abstract class CmsLoad extends Load {

    public function initialize($smarty, $sessionManager, $config, $loadMapper, $args) {
        parent::initialize($smarty, $sessionManager, $config, $loadMapper, $args);
        $customer = $this->getCustomer();
        $this->addParam("user", $customer);
        $this->addParam('load_class_name', get_called_class());
    }

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

    public function onNoAccess() {
        $this->redirect('admin/login');
    }

}

?>