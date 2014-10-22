<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/ServiceCompanyMapper.class.php");
require_once (CLASSES_PATH . "/managers/RequestHistoryManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyBranchesManager.class.php");

/**
 * CompanyManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class ServiceCompanyManager extends AbstractManager {

    /**
     * @var app config
     */
    private $config;

    /**
     * @var passed arguemnts
     */
    private $args;

    /**
     * @var singleton instance of class
     */
    private static $instance = null;

    /**
     * Initializes DB mappers
     *
     * @param object $config
     * @param object $args
     * @return
     */
    function __construct($config, $args) {
        $this->mapper = ServiceCompanyMapper::getInstance();
        $this->config = $config;
        $this->args = $args;
    }

    /**
     * Returns an singleton instance of this class
     *
     * @param object $config
     * @param object $args
     * @return
     */
    public static function getInstance($config, $args) {

        if (self::$instance == null) {

            self::$instance = new ServiceCompanyManager($config, $args);
        }
        return self::$instance;
    }

    public function enableSound($id, $value) {
        $this->mapper->updateNumericField($id, 'sound_on', $value);
    }

    public function getCompanyAndBranches($id) {
        return $this->mapper->getServiceCompanyAndBranches($id);
    }

    public function getServiceCompanyByEmailAndPassword($email, $password) {
        return $this->mapper->getServiceCompany($email, $password);
    }

    public function getAllServiceCompaniesWithBranches() {
        return $this->mapper->getAllServiceCompaniesWithBranches();
    }

    public function getCompaniesIdsArray($companiesDtos) {

        $ret = array();
        if (!$companiesDtos) {
            return $ret;
        }
        foreach ($companiesDtos as $key => $dto) {
            $ret[] = $dto->getId();
        }
        return $ret;
    }

    public function getCompaniesNamesArray($companiesDtos) {
        $ret = array();
        if (!$companiesDtos) {
            return $ret;
        }
        foreach ($companiesDtos as $key => $dto) {
            $ret[] = $dto->getName();
        }
        return $ret;
    }

    public function getServiceCompanyByEmail($email) {
        $dtos = $this->mapper->selectByField('email', $email);
        if (count($dtos) === 1) {
            return $dtos[0];
        }
        return null;
    }

    public function updateCompanyProfileFieldsById($id, $service_company_branch_id, $name, $change_pass, $new_pass, $accessKey, $phone1, $phone2, $phone3, $address, $zip, $region, $working_days, $working_hours, $url, $lng, $lat) {
        $companyDto = $this->selectByPK($id);
        $companyDto->setName($name);
        $companyDto->setAccessKey($accessKey);
        if ($change_pass) {
            $companyDto->setPassword($new_pass);
        }
        $phonesArray = array();
        if (!empty($phone1)) {
            $phonesArray [] = $phone1;
        }

        if (!empty($phone2)) {
            $phonesArray [] = $phone2;
        }

        if (!empty($phone3)) {
            $phonesArray [] = $phone3;
        }
        $phones = implode(',', $phonesArray);
        $serviceCompanyBranchesManager = ServiceCompanyBranchesManager::getInstance($this->config, $this->args);
        $serviceCompanyBranchesManager->setBranchFields($service_company_branch_id, $phones, $address, $region, $working_days, $working_hours, $zip, $lng, $lat);

        $companyDto->setUrl($url);
        $this->mapper->updateByPK($companyDto);
    }

    public function getAllCompaniesEmails() {
        $dtos = $this->mapper->getAllCompaniesEmails();
        $ret = array();
        foreach ($dtos as $dto) {
            $ret[] = $dto->getEmail();
        }
        return $ret;
    }

    public function updateCompanyHash($uId) {
        $hash = $this->generateHash($uId);
        $companyDto = $this->mapper->createDto();
        $companyDto->setId($uId);
        $companyDto->setHash($hash);
        $this->mapper->updateByPK($companyDto);
        return $hash;
    }

    public function generateHash($id) {
        return md5($id * time() * 19);
    }

    public function validate($id, $hash) {
        return $this->mapper->validate($id, $hash);
    }

    public function setLanguageCode($id, $lc) {
        $this->mapper->updateTextField($id, 'language_code', $lc);
    }

    public function setLastPingToNow($id) {
        $this->mapper->updateTextField($id, 'last_ping', date('Y-m-d H:i:s'));
    }

}

?>