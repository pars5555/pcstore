<?php

require_once (CLASSES_PATH . "/loads/company/CompanyLoad.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ManageItemsLoad extends CompanyLoad {

    public function load() {
        $companyManager = CompanyManager::getInstance($this->config, $this->args);
        $customer = $this->sessionManager->getUser();
        $userLevel = $customer->getLevel();
        if ($userLevel == UserGroups::$ADMIN) {
            $allCompaniesDtos = $companyManager->getAllCompanies(true, true);
        } else {
            $allCompaniesDtos = array($companyManager->selectByPK($customer->getId()));
        }

        $allCompaniesNames = array();
        $allCompaniesIds = array();
        foreach ($allCompaniesDtos as $key => $company) {
            $allCompaniesNames[] = $company->getName();
            $allCompaniesIds[] = $company->getId();
        }
        if (isset($_REQUEST["company_id"])) {
            $selectedCompanyId = $this->secure($_REQUEST["company_id"]);
        } else {
            $selectedCompanyId = $allCompaniesIds[0];
        }

        $this->addParam('selectedCompanyId', $selectedCompanyId);
        $this->addParam('allCompaniesNames', $allCompaniesNames);
        $this->addParam('allCompaniesIds', $allCompaniesIds);



        $itemManager = ItemManager::getInstance($this->config, $this->args);

        $allCompanyItems = $itemManager->getCompanyItems($selectedCompanyId, true);
        $this->addParam('company_items', $allCompanyItems);




        if (isset($_COOKIE['copied_item_id'])) {
            $this->addParam('copied_item_id', $_COOKIE['copied_item_id']);
        }
        $this->addParam('itemManager', $itemManager);
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/company/stock/manage_items.tpl";
    }

}

?>