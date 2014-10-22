<?php

require_once (CLASSES_PATH . "/loads/company/CompanyLoad.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class CompanyProfileLoad extends CompanyLoad {

    public function load() {
        $companyManager = CompanyManager::getInstance($this->config, $this->args);
        $companyId = $this->getUserId();


        if (!isset($_REQUEST['refresh']) || $_REQUEST['refresh'] != 1) {
            $companyLogin = $this->getCustomerLogin();

            if (isset($_REQUEST['password'])) {
                $password = $_REQUEST['password'];
                $dto = $companyManager->getCompanyByEmailAndPassword($companyLogin, $password);
                if (!isset($dto)) {
                    $this->addParam("message", $this->getPhraseSpan(581));
                    $this->addParam("checkPassword", 1);
                    return;
                }
            } else {
                $this->addParam("checkPassword", 1);
                return;
            }
        }
        $companyAndBranchesDtos = $companyManager->getCompanyAndBranches($companyId);
        $companyBranchesNamesIdsMap = $this->getCompanyBranchesNamesArrayByCompanyAndBranchesDtos($companyAndBranchesDtos);
        $selectedBranchNames = array_values($companyBranchesNamesIdsMap);
        $this->addParam("company_branches_names", $selectedBranchNames);
        $selectedBranchIds = array_keys($companyBranchesNamesIdsMap);
        $this->addParam("company_branches_ids", $selectedBranchIds);

        $selectedCompanyBranchDto = null;
        if (isset($_REQUEST['selected_branch_id'])) {
            $selectedBranchId = intval($this->secure($_REQUEST['selected_branch_id']));
            if (array_key_exists($selectedBranchId, $companyBranchesNamesIdsMap)) {
                $selectedCompanyBranchDto = $this->getCompanyBrancheByBranchId($companyAndBranchesDtos, $selectedBranchId);
            }
        }
        if (!isset($selectedCompanyBranchDto)) {
            $selectedCompanyBranchDto = $companyAndBranchesDtos[0];
        }
        if (!isset($selectedCompanyBranchDto)) {
            echo 'Error: no company branch data!';
            exit;
        }

        $this->addParam("selected_company_branch_id", $selectedCompanyBranchDto->getBranchId());


        $companyPhones = trim($selectedCompanyBranchDto->getPhones());
        $companyPhonesArray = array();
        if (!empty($companyPhones)) {
            $companyPhonesArray = explode(',', $companyPhones);
        }
        $this->addParam("phones", $companyPhonesArray);
        $this->addParam("working_days", $selectedCompanyBranchDto->getWorkingDays());
        $this->addParam("branch_address", $selectedCompanyBranchDto->getStreet());
        $this->addParam("lat", $selectedCompanyBranchDto->getLat());
        $this->addParam("lng", $selectedCompanyBranchDto->getLng());

        $regions_phrase_ids_array = explode(',', $this->getCmsVar('armenia_regions_phrase_ids'));
        $this->addParam('regions_phrase_ids_array', $regions_phrase_ids_array);
        $region = trim($selectedCompanyBranchDto->getRegion());
        $this->addParam('region_selected', $region);

        $minutes_block = 30;
        $start_time = '00:00:00';
        $total_day_minutes = 24 * 60;
        $cycleIndex = -1;
        $times = array();
        while (++$cycleIndex * $minutes_block < $total_day_minutes) {

            $timestamp = strtotime($start_time);
            $mins = $cycleIndex * $minutes_block;
            $time = strtotime("+$mins minutes", $timestamp);
            $times[] = date('H:i', $time);
        }
        $this->addParam('times', $times);
    }

    private function getCompanyBranchesNamesArrayByCompanyAndBranchesDtos($companyAndBranchesDtos) {
        $ret = array();
        foreach ($companyAndBranchesDtos as $comAndBranchDto) {
            $street = $comAndBranchDto->getStreet();
            $ret[$comAndBranchDto->getBranchId()] = $street;
        }
        return $ret;
    }

    private function getCompanyBrancheByBranchId($companyAndBranchesDtos, $branchId) {
        foreach ($companyAndBranchesDtos as $comAndBranchDto) {
            if ($comAndBranchDto->getBranchId() == $branchId) {
                return $comAndBranchDto;
            }
        }
        return null;
    }

    public function getDefaultLoads($args) {
        $loads = array();
        $loadName = $this->generateLoadClassName("next_24_hours_select");
        $loads["next_24_hours_select"]["load"] = "loads/company/" . $loadName;
        $loads["next_24_hours_select"]["args"] = array("start_time" => $this->getCustomer()->getSmsReceiveTimeStart());
        $loads["next_24_hours_select"]["loads"] = array();
        $loads["next_24_hours_select"]["loads"] = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/company/company_profile.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$companyRequest;
    }

}

?>