<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/RequestHistoryDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class RequestHistoryMapper extends AbstractMapper {

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
        $this->tableName = "request_history";
    }

    /**
     * Returns an singleton instance of this class
     * @return
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new RequestHistoryMapper();
        }
        return self::$instance;
    }

    /**
     * @see AbstractMapper::createDto()
     */
    public function createDto() {
        return new RequestHistoryDto();
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

    public static $GET_RECENT_REQUESTS_COUNT = "SELECT COUNT(id) AS `requests_count` FROM `%s` WHERE `user_email`='%s' AND `datetime`>= DATE_SUB('%s', INTERVAL %d DAY)";

    public function getCustomerRecentRequestsCount($email, $daysNumber) {
        $sqlQuery = sprintf(self::$GET_RECENT_REQUESTS_COUNT, $this->getTableName(), $email, date('Y-m-d H:i:s'), $daysNumber);
        return $this->fetchField($sqlQuery, 'requests_count');
    }

    public static $GET_CUSTOMER_GIVEN_REQ_COUNT_BY_HOUR = "SELECT COUNT(id) AS `requests_count` FROM `%s` WHERE `user_email`='%s' AND `datetime`>= DATE_SUB('%s', INTERVAL %d HOUR) AND `request_url`='%s'";

    public function getCustomerGivenRequestRecentCountByHours($email, $hours, $requestName) {
        $sqlQuery = sprintf(self::$GET_CUSTOMER_GIVEN_REQ_COUNT_BY_HOUR, $this->getTableName(), $email, date('Y-m-d H:i:s'), $hours, $requestName);
        return $this->fetchField($sqlQuery, 'requests_count');
    }

    public function getAllSearchRequests($daysNumberFromToday) {
        $sqlQuery = sprintf("SELECT * FROM `%s` WHERE request_url = 'ItemSearchLoad' AND  DATE_SUB(CURRENT_DATE, INTERVAL %d DAY)<`datetime`", $this->getTableName(), $daysNumberFromToday);
        return $this->fetchRows($sqlQuery);
    }

    public function removeOldRowsByDays($days) {
        $sqlQuery = sprintf("DELETE FROM `%s` WHERE `datetime`<DATE_SUB('%s', INTERVAL %d DAY) OR `datetime` IS NULL", $this->getTableName(), date('Y-m-d H:i:s'), $days);
        $this->dbms->query($sqlQuery);
    }

}

?>