<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/CompanyPriceEmailHistoryDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class CompanyPriceEmailHistoryMapper extends AbstractMapper {

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
        $this->tableName = "company_price_email_history";
    }

    /**
     * Returns an singleton instance of this class
     * @return
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new CompanyPriceEmailHistoryMapper();
        }
        return self::$instance;
    }

    /**
     * @see AbstractMapper::createDto()
     */
    public function createDto() {
        return new CompanyPriceEmailHistoryDto();
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

    //////////////////////////////// custom functions //////////////////////////////////

    public static $GET_ADMIN = "SELECT * FROM `%s` WHERE company_id = %d AND company_type = '%s' AND `datetime` >= DATE_SUB('%s', INTERVAL %d HOUR)";

    public function getCompanySentEmailsByHours($companyId, $companyType, $hours) {
        $sqlQuery = sprintf(self::$GET_ADMIN, $this->getTableName(), $companyId, $companyType, date('Y-m-d H:i:s'), $hours);
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

}

?>