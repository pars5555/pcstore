<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/LoginHistoryDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class LoginHistoryMapper extends AbstractMapper {

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
        $this->tableName = "login_history";
    }

    /**
     * Returns an singleton instance of this class
     * @return
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new LoginHistoryMapper();
        }
        return self::$instance;
    }

    /**
     * @see AbstractMapper::createDto()
     */
    public function createDto() {
        return new LoginHistoryDto();
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

    public function getVisitorsByDate($dateStart, $dateEnd) {
        $sql = "SELECT * FROM `%s` WHERE `datetime`>='%s' AND `datetime`<='%s'";
        $sqlQuery = sprintf($sql, $this->getTableName(), $dateStart, $dateEnd);
        return $this->fetchRows($sqlQuery);
    }

}

?>