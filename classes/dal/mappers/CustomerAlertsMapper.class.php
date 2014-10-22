<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/CustomerAlertsDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class CustomerAlertsMapper extends AbstractMapper {

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
        $this->tableName = "customer_alerts";
    }

    /**
     * @see AbstractMapper::createDto()
     */
    public function createDto() {
        return new CustomerAlertsDto();
    }

    /**
     * Returns an singleton instance of this class
     * @return
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new CustomerAlertsMapper();
        }
        return self::$instance;
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

    public function getCustomerAlertsByCustomerLogin($customerLogin, $win_uid) {
        $q = "SELECT * FROM `%s` WHERE (FIND_IN_SET('%s',`delivered_to_uid`) = 0 OR `delivered_to_uid` IS NULL) AND `to_login`='%s'";
        $sqlQuery = sprintf($q, $this->getTableName(), $win_uid, $customerLogin);
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

    public function removeOldAlerts($minute) {
        $q = "DELETE FROM `%s` WHERE `datetime` < DATE_SUB('%s', INTERVAL %d MINUTE)";
        $sqlQuery = sprintf($q, $this->getTableName(), date('Y-m-d H:i:s'), $minute);
        $result = $this->dbms->query($sqlQuery);
        return $result;
    }

}

?>