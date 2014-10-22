<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/AdminManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyDealersManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class InsertContactLoad extends GuestLoad {

    public function load() {
        $companyManager = CompanyManager::getInstance($this->config, $this->args);
        $adminManager = AdminManager::getInstance($this->config, $this->args);
        $userManager = UserManager::getInstance($this->config, $this->args);
        $companyDealersManager = CompanyDealersManager::getInstance($this->config, $this->args);
        $allAdmins = $adminManager->selectAll();
        if ($this->getUserLevel() === UserGroups::$COMPANY) {
            $allCompanies = $companyManager->getAllCompanies();
            $companyDealersJoindWithUsersFullInfo = $companyDealersManager->getCompanyDealersJoindWithUsersFullInfo($this->getUserId());
            $this->addParam('allCompanies', $allCompanies);
            $this->addParam('allDealers', $companyDealersJoindWithUsersFullInfo);
            $this->addParam('allAdmins', $allAdmins);
        }
        if ($this->getUserLevel() === UserGroups::$SERVICE_COMPANY) {
            $allCompanies = $companyManager->getAllCompanies();
            $this->addParam('allCompanies', $allCompanies);
            $this->addParam('allAdmins', $allAdmins);
        }
        if ($this->getUserLevel() === UserGroups::$ADMIN) {
            $allCompanies = $companyManager->getAllCompanies(true, true);
            $allUsers = $userManager->selectAll();
            $this->addParam('allCompanies', $allCompanies);
            $this->addParam('allUsers', $allUsers);
            $this->addParam('allAdmins', $allAdmins);
        }
        if ($this->getUserLevel() === UserGroups::$USER) {
            $allCompanies = $companyManager->getAllCompanies();
            $allUsers = $userManager->selectAll();
            $dealerCompanies = $companyManager->getUserCompaniesJoindWithFullInfo($this->getUserId());
            $this->addParam('allCompanies', $dealerCompanies);
            //$this->addParam('allUsers', $allUsers);
            $this->addParam('allAdmins', $allAdmins);
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
        return TEMPLATES_DIR . "/main/mails/insert_contact.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$userCompanyRequest;
    }

}

?>