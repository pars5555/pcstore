<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/UserDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class UserMapper extends AbstractMapper {

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
        $this->tableName = "users";
    }

    /**
     * Returns an singleton instance of this class
     * @return
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new UserMapper();
        }
        return self::$instance;
    }

    /**
     * @see AbstractMapper::createDto()
     */
    public function createDto() {
        return new UserDto();
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

    public static $GET_USER = "SELECT * FROM `%s` WHERE `email` = '%s' AND BINARY `password` = '%s'";

    public function getUser($email, $password) {
        $sqlQuery = sprintf(self::$GET_USER, $this->getTableName(), $email, $password);
        $result = $this->fetchRows($sqlQuery);
        if (count($result) === 1) {
            return $result[0];
        } else {
            return null;
        }
    }

    public static $GET_ALL_USERS_EMAILS = "SELECT `email` FROM `%s`";

    public function getAllUsersEmails() {
        $sqlQuery = sprintf(self::$GET_ALL_USERS_EMAILS, $this->getTableName());
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

    public static $GET_ALL_USERS_FULL = "SELECT `%s`.*,`online_users`.`status` as `online_status` FROM `%s` LEFT JOIN `online_users` ON `%s`.`email` = `online_users`.`email`";

    public function getAllUsersFull() {
        $sqlQuery = sprintf(self::$GET_ALL_USERS_FULL, $this->getTableName(), $this->getTableName(), $this->getTableName());
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

    public static $GET_USER_REGION = "SELECT region FROM `%s` WHERE `id` = '%s'";

    public function getRegion($id) {
        $sqlQuery = sprintf(self::$GET_USER_REGION, $this->getTableName(), $id);
        $result = $this->fetchField($sqlQuery, 'region');
        return $result;
    }

    public function setSubUsersRegistrationCode($user_id, $value) {
        $this->updateTextField($user_id, 'sub_users_registration_code', $value);
    }

    public static $VALIDATE_USER = "SELECT * FROM `%s` WHERE `id` = %d AND `hash` = '%s'";

    public function validate($id, $hash) {
        $sqlQuery = sprintf(self::$VALIDATE_USER, $this->getTableName(), $id, $hash);

        $result = $this->fetchRows($sqlQuery);

        if (count($result) === 1) {
            return $result[0];
        } else {
            return null;
        }
    }

}

?>