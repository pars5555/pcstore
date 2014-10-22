<?php

require_once (CLASSES_PATH . "/loads/admin/AdminLoad.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");

class AdminActionsViewLoad extends AdminLoad {

    public function load() {
        $companyManager = CompanyManager::getInstance($this->config, $this->args);
        $allCompanies = $companyManager->getAllCompanies(true, true);
        $companies = array();
        $companies[0] = 'All';
        foreach ($allCompanies as $company) {
            $companies[$company->getId()] = $company->getName();
        }
        $this->addParam("companies", $companies);
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/admin/sysconfig/actions.tpl";
    }

}

?>