<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/ServiceCompanyBranchesMapper.class.php");

/**
 * CompanyManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class ServiceCompanyBranchesManager extends AbstractManager {

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
        $this->mapper = ServiceCompanyBranchesMapper::getInstance();
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

            self::$instance = new ServiceCompanyBranchesManager($config, $args);
        }
        return self::$instance;
    }

    public function setBranchFields($service_company_branch_id, $phones, $address, $region, $working_days, $working_hours, $zip, $lng, $lat) {
        $dto = $this->mapper->selectByPK($service_company_branch_id);
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

    public function addBranch($serviceCompanyId, $street, $region, $zip) {
        $dto = $this->mapper->createDto();
        $dto->setServiceCompanyId($serviceCompanyId);
        $dto->setStreet($street);
        $dto->setRegion($region);
        $dto->setZip($zip);
        return $this->mapper->insertDto($dto);
    }

    public function getServiceCompaniesBranches($companiesIdsArray) {
        if (is_array($companiesIdsArray)) {
            $companiesIdsArray = implode(',', $companiesIdsArray);
        }
        return $this->mapper->getServiceCompaniesBranches($companiesIdsArray);
    }

}

?>