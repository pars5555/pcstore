<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/OnlineUsersDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class OnlineUsersMapper extends AbstractMapper {

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
        $this->tableName = "online_users";
    }

    /**
     * Returns an singleton instance of this class
     * @return
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new OnlineUsersMapper();
        }
        return self::$instance;
    }

    /**
     * @see AbstractMapper::createDto()
     */
    public function createDto() {
        return new OnlineUsersDto();
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
    public static $GET_ONLINE_USER_BY_EMAIL = "SELECT * FROM `%s` WHERE email = '%s'";

    public function getOnlineUserByEmail($email) {
        $sqlQuery = sprintf(self::$GET_ONLINE_USER_BY_EMAIL, $this->getTableName(), $email);
        $result = $this->fetchRows($sqlQuery);
        if (count($result) === 1) {
            return $result[0];
        } else {
            return null;
        }
    }

    public static $GET_ONLINE_REGISTERED_CUSTOMERS = "SELECT 
  `%s`.*, IF (`users`.`language_code`<>'',`users`.`language_code`, IF ( `companies`.`language_code`<>'',  `companies`.`language_code`, NULL ) ) AS `language_code` 
FROM
  `%s` 
  LEFT JOIN `users` 
    ON  `users`.`email` = `%s`.`email` 
    LEFT JOIN `companies` 
    ON  `companies`.`email` = `%s`.`email` 
    LEFT JOIN `admins` 
    ON  `admins`.`email` = `%s`.`email` 
  WHERE `%s`.`email` <> ''";

    public function getOnlineRegisteredCustomers() {

        $sqlQuery = sprintf(self::$GET_ONLINE_REGISTERED_CUSTOMERS, $this->getTableName(), $this->getTableName(), $this->getTableName(), $this->getTableName(), $this->getTableName(), $this->getTableName());
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

    public static $REMOVE_TIME_OUTED_USERS = "DELETE FROM `%s` WHERE `last_ping_time_stamp` < DATE_SUB('%s', INTERVAL %d SECOND)";

    public function removeTimeOutedUsers($timoutSeconds) {
        $sqlQuery = sprintf(self::$REMOVE_TIME_OUTED_USERS, $this->getTableName(), date('Y-m-d H:i:s'), $timoutSeconds);
        $result = $this->dbms->query($sqlQuery);
        return $result;
    }

}

?>