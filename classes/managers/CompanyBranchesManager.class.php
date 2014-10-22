<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/CompanyBranchesMapper.class.php");

/**
 * CompanyManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class CompanyBranchesManager extends AbstractManager {

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
        $this->mapper = CompanyBranchesMapper::getInstance();
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

            self::$instance = new CompanyBranchesManager($config, $args);
        }
        return self::$instance;
    }

    public function setBranchFields($company_branch_id, $phones, $address, $region, $working_days, $working_hours, $zip, $lng, $lat) {
        $dto = $this->mapper->selectByPK($company_branch_id);
        if (isset($dto)) {
            $dto->setPhones($phones);
            $dto->setStreet($address);
            $dto->setRegion($region);
            $dto->setWorkingDays($working_days);
            $dto->setWorkingHours($working_hours);
            $dto->setZip($zip);
            $dto->setLng($lng);
            $dto->setLat($lat);
            $this->mapper->updateByPK($dto);
            return true;
        }
        return false;
    }

    public function addBranch($companyId, $street, $region, $zip) {
        $dto = $this->mapper->createDto();
        $dto->setCompanyId($companyId);
        $dto->setStreet($street);
        $dto->setRegion($region);
        $dto->setZip($zip);
        return $this->mapper->insertDto($dto);
    }

    public function getCompaniesBranches($companiesIdsArray) {
        if (is_array($companiesIdsArray)) {
            $companiesIdsArray = implode(',', $companiesIdsArray);
        }
        return $this->mapper->getCompaniesBranches($companiesIdsArray);
    }

}

?>