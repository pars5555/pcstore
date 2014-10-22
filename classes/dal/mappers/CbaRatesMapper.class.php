<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/CbaRatesDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class CbaRatesMapper extends AbstractMapper {

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
        $this->tableName = "cba_rates";
    }

    /**
     * Returns an singleton instance of this class
     * @return
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new CbaRatesMapper();
        }
        return self::$instance;
    }

    /**
     * @see AbstractMapper::createDto()
     */
    public function createDto() {
        return new CbaRatesDto();
    }

    /**
     * @see AbstractMapper::getPKFieldName()
     */
    public function getPKFieldName() {
        return "id";
    }

    public function getTableName() {
        return $this->tableName;
    }

    public function getLatestUSDExchange() {
        $sql = "SELECT * FROM `%s` WHERE iso='USD' ORDER BY `datetime` DESC LIMIT 0,1";
        $sqlQuery = sprintf($sql, $this->getTableName());
        $rows = $this->fetchRows($sqlQuery);
        if (count($rows) === 1) {
            return floatval($rows[0]->getRate());
        }
        return null;
    }

}

?>