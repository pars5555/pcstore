<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/PriceValuesDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class PriceValuesMapper extends AbstractMapper {

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
        $this->tableName = "price_values";
    }

    /**
     * Returns an singleton instance of this class
     * @return
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new PriceValuesMapper();
        }
        return self::$instance;
    }

    /**
     * @see AbstractMapper::createDto()
     */
    public function createDto() {
        return new PriceValuesDto();
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

    /**
     * @see AbstractMapper::getTableName()
     */
    public function getCompanyPriceSheetValues($companyId, $priceIndex, $sheetIndex) {
        $sql = "SELECT * FROM `%s` WHERE company_id=%d AND price_index = %d AND sheet_index = %d";
        $sqlQuery = sprintf($sql, $this->getTableName(), $companyId, $priceIndex, $sheetIndex);
        return $this->fetchRows($sqlQuery);
    }

    public function setColumnValuesToNull($companyId, $priceIndex, $sheetIndex, $colName) {
        $sql = "UPDATE `%s` SET `%s`=NULL WHERE company_id=%d AND price_index = %d AND `sheet_index`=%d";
        $sqlQuery = sprintf($sql, $this->getTableName(), $colName, $companyId, $priceIndex, $sheetIndex);
        return $this->dbms->query($sqlQuery);
    }

    public function swapColumnsValues($companyId, $priceIndex, $sheetIndex, $colName1, $colName2) {
        $sql = "UPDATE `%s` SET `%s`=(@temp:=`%s`), `%s` = `%s`, `%s` = @temp WHERE company_id=%d AND price_index = %d AND `sheet_index`=%d";
        $sqlQuery = sprintf($sql, $this->getTableName(), $colName1, $colName1, $colName1, $colName2, $colName2, $companyId, $priceIndex, $sheetIndex);
        return $this->dbms->query($sqlQuery);
    }

}

?>