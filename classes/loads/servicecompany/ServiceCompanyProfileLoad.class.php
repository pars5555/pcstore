<?php

require_once (CLASSES_PATH . "/loads/servicecompany/ServiceCompanyLoad.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ServiceCompanyProfileLoad extends ServiceCompanyLoad {

    public function load() {
        $serviceCompanyManager = ServiceCompanyManager::getInstance($this->config, $this->args);


        if (!isset($_REQUEST['refresh']) && $_REQUEST['refresh'] != 1) {
            $companyLogin = $this->getCustomerLogin();
            if (isset($_REQUEST['password'])) {
                $password = $_REQUEST['password'];
                $dto = $serviceCompanyManager->getServiceCompanyByEmailAndPassword($companyLogin, $password);
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

        $companyId = $this->getUserId();
        $companyAndBranchesDtos = $serviceCompanyManager->getCompanyAndBranches($companyId);
        $companyBranchesNamesIdsMap = $this->getCompanyBranchesNamesArrayByCompanyAndBranchesDtos($companyAndBranchesDtos);
        $selectedBranchNames = array_values($companyBranchesNamesIdsMap);
        $this->addParam("company_branches_names", $selectedBranchNames);
        $selectedBranchIds = array_keys($companyBranchesNamesIdsMap);
        $this->addParam("company_branches_ids", $selectedBranchIds);

        $selectedServiceCompanyBranchDto = null;
        if (isset($_REQUEST['selected_branch_id'])) {
            $selectedBranchId = intval($this->secure($_REQUEST['selected_branch_id']));
            if (array_key_exists($selectedBranchId, $companyBranchesNamesIdsMap)) {
                $selectedServiceCompanyBranchDto = $this->getCompanyBrancheByBranchId($companyAndBranchesDtos, $selectedBranchId);
            }
        }
        if (!isset($selectedServiceCompanyBranchDto)) {
            $selectedServiceCompanyBranchDto = $companyAndBranchesDtos[0];
        }
        if (!isset($selectedServiceCompanyBranchDto)) {
            echo 'Error: no company branch data!';
            exit;
        }

        $this->addParam("selected_company_branch_id", $selectedServiceCompanyBranchDto->getBranchId());


        $companyPhones = trim($selectedServiceCompanyBranchDto->getPhones());
        $companyPhonesArray = array();
        if (!empty($companyPhones)) {
            $companyPhonesArray = explode(',', $companyPhones);
        }
        $this->addParam("phones", $companyPhonesArray);
        $this->addParam("working_days", $selectedServiceCompanyBranchDto->getWorkingDays());
        $this->addParam("branch_address", $selectedServiceCompanyBranchDto->getStreet());
        $this->addParam("lat", $selectedServiceCompanyBranchDto->getLat());
        $this->addParam("lng", $selectedServiceCompanyBranchDto->getLng());

        $regions_phrase_ids_array = explode(',', $this->getCmsVar('armenia_regions_phrase_ids'));
        $this->addParam('regions_phrase_ids_array', $regions_phrase_ids_array);
        $region = trim($selectedServiceCompanyBranchDto->getRegion());
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
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/servicecompany/profile.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$companyRequest;
    }

}

?>