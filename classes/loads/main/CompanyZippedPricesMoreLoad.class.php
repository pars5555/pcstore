<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/CompaniesPriceListManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class CompanyZippedPricesMoreLoad extends GuestLoad {

    public function load() {
        $companiesPriceListManager = CompaniesPriceListManager::getInstance($this->config, $this->args);
        $companyId = $_REQUEST['company_id'];
        $this->addParam("companiesPriceListManager", $companiesPriceListManager);
        $companiesZippedPricesByDaysNumber = $companiesPriceListManager->getCompaniesZippedPricesByDaysNumber($companyId);
        $groupCompaniesZippedPricesByCompanyId = $this->groupCompaniesZippedPricesByCompanyId($companiesZippedPricesByDaysNumber);
        $this->addParam("groupCompaniesZippedPricesByCompanyId", $groupCompaniesZippedPricesByCompanyId);
    }

    public function getDefaultLoads($args) {
        $loads = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/company_zipped_prices_load_more.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$userCompanyRequest;
    }

    public function groupCompaniesZippedPricesByCompanyId($companiesZippedPricesByDaysNumber) {
        $ret = array();
        foreach ($companiesZippedPricesByDaysNumber as $key => $dto) {
            if ($key > 10) {
                $ret[] = $dto;
            }
        }
        return $ret;
    }

}

?>