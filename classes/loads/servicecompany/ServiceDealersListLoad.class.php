<?php

require_once (CLASSES_PATH . "/loads/servicecompany/ServiceCompanyLoad.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyDealersManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ServiceDealersListLoad extends ServiceCompanyLoad {

    public function load() {
        $serviceCompanyDealersManager = ServiceCompanyDealersManager::getInstance($this->config, $this->args);
        $serviceCompanyId = $this->sessionManager->getUser()->getId();
        $dealers = $serviceCompanyDealersManager->getCompanyDealersJoindWithUsersFullInfo($serviceCompanyId);
        $this->addParam('dealers', $dealers);
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/servicecompany/dealers_list.tpl";
    }

}

?>