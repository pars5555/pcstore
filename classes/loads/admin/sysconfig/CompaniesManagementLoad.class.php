<?php

require_once (CLASSES_PATH . "/loads/admin/AdminLoad.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");

class CompaniesManagementLoad extends AdminLoad {

    public function load() {
        $loadPage = 0;
        if (isset($_REQUEST['load_page'])) {
            $loadPage = $_REQUEST['load_page'];
        }
        if ($loadPage == 1) {
            $this->addParam("page_loaded", 1);

            $companyManager = CompanyManager::getInstance($this->config, $this->args);
            $allCompanyDtos = $companyManager->getAllCompanies(true, true);
            $this->addParam("companies", $allCompanyDtos);
        } else {
            $this->addParam("page_loaded", 0);
        }
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/admin/sysconfig/companies_management.tpl";
    }

}

?>