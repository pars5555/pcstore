<?php

require_once (CLASSES_PATH . "/loads/cms/CmsLoad.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyDealersManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyDealersManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class UsersLoad extends CmsLoad {

    public function load() {
        $userManager = UserManager::getInstance($this->config, $this->args);
        $allUsersDtos = $userManager->selectAll();

        $this->addParam("users", $allUsersDtos);
        $companyDealersManager = CompanyDealersManager::getInstance($this->config, $this->args);
        $serviceCompanyDealersManager = ServiceCompanyDealersManager::getInstance($this->config, $this->args);
        $allCompanyDealers = $companyDealersManager->getAllUsersCompaniesFull();
        $userCompanies = $this->getUserCompaniesArray($allCompanyDealers);
        $allServiceCompanyDealers = $serviceCompanyDealersManager->getAllUsersCompaniesFull();
        $userServiceCompanies = $this->getUserCompaniesArray($allServiceCompanyDealers);
        $this->addParam('userCompanies', $userCompanies);
        $this->addParam('userServiceCompanies', $userServiceCompanies);
        $this->addParam('visible_fields_names', array('id', 'email','name', 'lastName', 'phones', 'password', 'registeredDate','lastSmsValidationCode', 'vip'));
    }

    private function getUserCompaniesArray($allCompanyDealers) {
        $ret = array();
        foreach ($allCompanyDealers as $companyDealerDto) {
            $userId = $companyDealerDto->getUserId();
            $companyName = $companyDealerDto->getCompanyName();
            $ret[$userId][] = $companyName;
        }
        return $ret;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/cms/users.tpl";
    }

}

?>