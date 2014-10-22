<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/ServiceCompanyBranchesDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class ServiceCompanyBranchesMapper extends AbstractMapper {

    /**
     * @var table name in DB
     */
    private $tableName;

    /**
     * @var an instance of this class
     */
    private static $instance = null;

    /**
     * Initializes DBMS pointers and table name private class member.
     */
    function __construct() {
        // Initialize the dbms pointer.
        AbstractMapper::__construct();

        // Initialize table name.
        $this->tableName = "service_company_branches";
    }

    /**
     * Returns an singleton instance of this class
     * @return
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new ServiceCompanyBranchesMapper();
        }
        return self::$instance;
    }

    /**
     * @see AbstractMapper::createDto()
     */
    public function createDto() {
        return new ServiceCompanyBranchesDto();
    }

    /**
     * @see AbstractMapper::getPKFieldName()
     */
    public function getPKFieldName() {
        return "id";
    }

    /**
     * @see AbstractMapper::getTableName()
     */
    public function getTableName() {
        return $this->tableName;
    }

    public function getServiceCompaniesBranches($serviceCompaniesIdsArray) {
        $sql = sprintf("SELECT * FROM `%s` WHERE `service_company_id` IN (%s) ORDER BY FIELD(service_company_id, %s)", $this->getTableName(), $serviceCompaniesIdsArray, $serviceCompaniesIdsArray);
        return $this->fetchRows($sql);
    }

}

?>