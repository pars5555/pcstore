<?php

require_once (CLASSES_PATH . "/loads/admin/AdminLoad.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyDealersManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyDealersManager.class.php");

class AdminUserManagementLoad extends AdminLoad {

    public function load() {

        $loadPage = 0;
        if (isset($_REQUEST['load_page'])) {
            $loadPage = $_REQUEST['load_page'];
        }
        if ($loadPage == 1) {
            $this->addParam("page_loaded", 1);
            $userManager = UserManager::getInstance($this->config, $this->args);
            $allUsersDtos = $userManager->selectAll();

            $this->addParam("users", $allUsersDtos);
        } else {
            $this->addParam("page_loaded", 0);
        }
        $companyDealersManager = CompanyDealersManager::getInstance($this->config, $this->args);
        $serviceCompanyDealersManager = ServiceCompanyDealersManager::getInstance($this->config, $this->args);
        $allCompanyDealers = $companyDealersManager->getAllUsersCompaniesFull();
        $userCompanies = $this->getUserCompaniesArray($allCompanyDealers);
        $allServiceCompanyDealers = $serviceCompanyDealersManager->getAllUsersCompaniesFull();
        $userServiceCompanies = $this->getUserCompaniesArray($allServiceCompanyDealers);
        $this->addParam('userCompanies', $userCompanies);
        $this->addParam('userServiceCompanies', $userServiceCompanies);
    }

    private function getUserCompaniesArray($allCompanyDealers) {
        $ret = array();
        foreach ($allCompanyDealers as $companyDealerDto) {
            $userId = $companyDealerDto->getUserId();
            $companyName= $companyDealerDto->getCompanyName();
            $ret[$userId][] = $companyName;
        }
        return $ret;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/admin/sysconfig/admin_user_management.tpl";
    }

}

?>