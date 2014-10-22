<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/ServiceCompanyDealersMapper.class.php");

/**
 * CompanyManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class ServiceCompanyDealersManager extends AbstractManager {

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
        $this->mapper = ServiceCompanyDealersMapper::getInstance();
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

            self::$instance = new ServiceCompanyDealersManager($config, $args);
        }
        return self::$instance;
    }

    public function getCompanyDealersCount($companyId, $includedHiddenDealers = false) {
        return $this->mapper->getCompanyDealersCount($companyId, $includedHiddenDealers);
    }

    public function getCompanyDealersJoindWithUsersFullInfo($company_id) {
        return $this->mapper->getCompanyDealersJoindWithUsersFullInfo($company_id);
    }

    public function getCompanyDealersUsersFullInfoHiddenIncluded($company_id) {
        return $this->mapper->getCompanyDealersUsersFullInfoHiddenIncluded($company_id);
    }

    public function removeUserFromCompany($userId, $companyId) {
        $dto = $this->getByCompanyIdAndUserId($userId, $companyId);
        if ($dto) {
            $this->deleteByPK($dto->getId());
            return true;
        }
        return false;
    }

    public function addUserToCompany($userId, $companyId) {
        $dto = $this->getByCompanyIdAndUserId($userId, $companyId);
        if (!$dto) {
            $dto = $this->mapper->createDto();
            $dto->setUserId($userId);
            $dto->setServiceCompanyId($companyId);
            $dto->setTimestamp(date('Y-m-d H:i:s'));
            $this->mapper->insertDto($dto);
            return true;
        }
        return false;
    }

    public function getByCompanyIdAndUserId($userId, $companyId) {

        return $this->mapper->getByCompanyIdAndUserId($userId, $companyId);
    }

    public function getUserCompaniesIdsArray($userId) {
        $userCompaniesIds = trim($this->mapper->getUserCompaniesIds($userId));
        if (!empty($userCompaniesIds)) {
            return explode(',', $userCompaniesIds);
        }
        return array();
    }

    public function getByCompanyId($companyId) {
        return $this->selectByField('service_company_id', $companyId);
    }

    public function getAfterGivenDatetime($companyId, $datetime) {
        return $this->mapper->getAfterGivenDatetime($companyId, $datetime);
    }

    public function getAllUsersCompaniesFull() {
        return $this->mapper->getAllUsersCompaniesFull();
    }

}

?>