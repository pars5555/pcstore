<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompaniesPriceListManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ServiceCompanyZippedPricesMoreLoad extends ServiceCompanyLoad {

    public function load() {
        $serviceCompaniesPriceListManager = ServiceCompaniesPriceListManager::getInstance($this->config, $this->args);
        $serviceCompanyId = $_REQUEST['service_company_id'];
        $this->addParam("serviceCompaniesPriceListManager", $serviceCompaniesPriceListManager);
        $companiesZippedPricesByDaysNumber = $serviceCompaniesPriceListManager->getCompaniesZippedPricesByDaysNumber($serviceCompanyId);
        $groupServiceCompaniesZippedPricesByCompanyId = $this->groupCompaniesZippedPricesByCompanyId($companiesZippedPricesByDaysNumber);
        $this->addParam("groupServiceCompaniesZippedPricesByCompanyId", $groupServiceCompaniesZippedPricesByCompanyId);
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