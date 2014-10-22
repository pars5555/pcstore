<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyDealersManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyBranchesManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyBranchesManager.class.php");
require_once (CLASSES_PATH . "/managers/CompaniesPriceListManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompaniesPriceListManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class CompaniesListLoad extends GuestLoad {

    public function load() {
        $this->setDescriptionTagValue('Companies that are registered in pcstore.am and their products are listed.');
        $this->setTitleTagValue('Pcstore Company List');
        $this->setKeywordsTagValue('Companies that are registered in pcstore.am and their products are listed.');
        $companyManager = CompanyManager::getInstance($this->config, $this->args);
        $serviceCompanyManager = ServiceCompanyManager::getInstance($this->config, $this->args);
        $companiesPriceListManager = CompaniesPriceListManager::getInstance($this->config, $this->args);
        $serviceCompaniesPriceListManager = ServiceCompaniesPriceListManager::getInstance($this->config, $this->args);
        $show_only_last_hours_values = array(0, 10, 24, 72, 168);
        $ph_all = $this->getPhrase(461);
        $ph_last = $this->getPhrase(456);
        $ph_hours = $this->getPhrase(455);
        $ph_days = $this->getPhrase(459);
        $ph_week = $this->getPhrase(460);
        $ph_updated = $this->getPhrase(457);


        $show_only_last_hours_names_phrase_ids_array = array(461, "`456` 10 `455` `457`", "`456` 24 `455` `457`", "`456` 3 `459` `457`", "`456` `460` `457`");
        $show_only_last_hours_names = array($ph_all, $ph_last . ' 10 ' . $ph_hours . ' ' . $ph_updated, $ph_last . ' 24 ' . $ph_hours . ' ' . $ph_updated,
            $ph_last . ' 3 ' . $ph_days . ' ' . $ph_updated, $ph_last . ' ' . $ph_week . ' ' . $ph_updated);
        $show_only_last_hours_selected = 0;
        if (isset($_REQUEST['show_only_last_hours_selected'])) {
            $show_only_last_hours_selected = intval($_REQUEST['show_only_last_hours_selected']);
        }
        $this->addParam("show_only_last_hours_values", $show_only_last_hours_values);
        $this->addParam("show_only_last_hours_names", $show_only_last_hours_names);
        $this->addParam("show_only_last_hours_names_phrase_ids_array", $show_only_last_hours_names_phrase_ids_array);
        $this->addParam("show_only_last_hours_selected", $show_only_last_hours_selected);

        $searchText = "";
        if (isset($_REQUEST['search_text'])) {
            $searchText = $this->secure($_REQUEST['search_text']);
        }
        $this->addParam("search_text", $searchText);

        $userLevel = $this->getUserLevel();
        $userId = $this->getUserId();
        $companiesList = array();
        $allServiceCompaniesWithBranches = $serviceCompanyManager->getAllServiceCompaniesWithBranches();
        foreach ($allServiceCompaniesWithBranches as $serviceCompanyDto) {
            $serviceCompanyDto->setShowPrice(1);
        }
        if ($userLevel == UserGroups::$USER) {
            $serviceCompanyDealersManager = ServiceCompanyDealersManager::getInstance($this->config, $this->args);
            $userserviceCompaniesIdsArray = $serviceCompanyDealersManager->getUserCompaniesIdsArray($userId);
            foreach ($allServiceCompaniesWithBranches as $serviceCompanyDto) {
                if (in_array($serviceCompanyDto->getId(), $userserviceCompaniesIdsArray)) {
                    $serviceCompanyDto->setShowPrice(1);
                } else {
                    $serviceCompanyDto->setShowPrice(0);
                }
            }
        }

        if ($userLevel == UserGroups::$COMPANY || $userLevel == UserGroups::$SERVICE_COMPANY) {
            $companiesList = $companyManager->getAllCompaniesByPriceHours($show_only_last_hours_selected, $searchText);
        }
        if ($userLevel == UserGroups::$USER) {
            $companiesList = $companyManager->getUserCompaniesJoindWithFullInfo($userId, $show_only_last_hours_selected, $searchText);
        }
        if ($userLevel == UserGroups::$ADMIN) {
            $companiesList = $companyManager->getAllCompaniesByPriceHours($show_only_last_hours_selected, $searchText, true, true);
        }

        $this->addParam('allCompanies', $companiesList);
        $this->addParam('allServiceCompanies', $allServiceCompaniesWithBranches);

        $allNonHiddenCompanies = $companyManager->getAllCompanies();
        $this->addParam('allNonHiddenCompanies', $allNonHiddenCompanies);


        $this->addParam('allCompaniesDtosToArray', json_encode(AbstractDto::dtosToArray($companiesList, array("id" => "id", "name" => "name", "rating" => "rating"))));
        $this->addParam('allServiceCompaniesDtosToArray', json_encode(AbstractDto::dtosToArray($allServiceCompaniesWithBranches, array("id" => "id", "name" => "name"))));
        // $this->addParam('userLevel', $userLevel);
        // $this->addParam('userGroupsUser', UserGroups::$USER);
        $companyBranchesManager = CompanyBranchesManager::getInstance($this->config, $this->args);
        $serviceCompanyBranchesManager = ServiceCompanyBranchesManager::getInstance($this->config, $this->args);
        $cids = $this->getCompanyIdsArray($companiesList);
        $scids = $this->getCompanyIdsArray($allServiceCompaniesWithBranches);
        $companiesBranchesDtos = $companyBranchesManager->getCompaniesBranches($cids);
        $serviceCompaniesBranchesDtos = $serviceCompanyBranchesManager->getServiceCompaniesBranches($scids);
        $this->addParam('allCompaniesBranchesDtosToArray', json_encode(AbstractDto::dtosToArray($companiesBranchesDtos)));
        $this->addParam('allServiceCompaniesBranchesDtosToArray', json_encode(AbstractDto::dtosToArray($serviceCompaniesBranchesDtos)));

        $this->addParam("companiesPriceListManager", $companiesPriceListManager);
        $this->addParam("serviceCompaniesPriceListManager", $serviceCompaniesPriceListManager);
        $companiesZippedPricesByDaysNumber = $companiesPriceListManager->getCompaniesZippedPricesByDaysNumber($cids, 360);
        $serviceCompaniesZippedPricesByDaysNumber = $serviceCompaniesPriceListManager->getCompaniesZippedPricesByDaysNumber($cids, 360);
        $groupCompaniesZippedPricesByCompanyId = $this->groupCompaniesZippedPricesByCompanyId($companiesZippedPricesByDaysNumber);
        $groupServiceCompaniesZippedPricesByCompanyId = $this->groupServiceCompaniesZippedPricesByCompanyId($serviceCompaniesZippedPricesByDaysNumber);
        $this->addParam("groupCompaniesZippedPricesByCompanyId", $groupCompaniesZippedPricesByCompanyId);
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
        return TEMPLATES_DIR . "/main/companies_list.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$userCompanyRequest;
    }

    public function getCompanyIdsArray($companiesList) {
        if (empty($companiesList)) {
            return array();
        }
        $ret = array();
        foreach ($companiesList as $dto) {
            $ret [] = $dto->getId();
        }
        return $ret;
    }

    public function groupCompaniesZippedPricesByCompanyId($companiesZippedPricesByDaysNumber) {
        $ret = array();
        foreach ($companiesZippedPricesByDaysNumber as $dto) {
            if (count($ret[$dto->getCompanyId()]) <= intval($this->getCmsVar('company_list_zip_price_first_load_limit'))) {
                $ret[$dto->getCompanyId()][] = $dto;
            }
        }
        return $ret;
    }

    public function groupServiceCompaniesZippedPricesByCompanyId($companiesZippedPricesByDaysNumber) {
        $ret = array();
        foreach ($companiesZippedPricesByDaysNumber as $dto) {
            if (count($ret[$dto->getServiceCompanyId()]) <= intval($this->getCmsVar('company_list_zip_price_first_load_limit'))) {
                $ret[$dto->getServiceCompanyId()][] = $dto;
            }
        }
        return $ret;
    }

}

?>