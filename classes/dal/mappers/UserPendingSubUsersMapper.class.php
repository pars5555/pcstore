<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/UserPendingSubUsersDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class UserPendingSubUsersMapper extends AbstractMapper {

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
        $this->tableName = "user_pending_sub_users";
    }

    /**
     * Returns an singleton instance of this class
     * @return
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new UserPendingSubUsersMapper();
        }
        return self::$instance;
    }

    /**
     */
    public function createDto() {
        return new UserPendingSubUsersDto();
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

    public static $GET_BY_USER_ID = "SELECT * FROM `%s` WHERE user_id = %d ORDER BY `timestamp`";

    public function getByUserIdOrderByDate($userId) {
        $sqlQuery = sprintf(self::$GET_BY_USER_ID, $this->getTableName(), $userId);
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

    public static $GET_BY_USER_ID_AND_PENDING_SUB_USER_EMAIL = "SELECT * FROM `%s` WHERE user_id = %d AND pending_sub_user_email = '%s'";

    public function getByUserIdAndPendingSubUserEmail($userId, $pendingSubUserEmail) {
        $sqlQuery = sprintf(self::$GET_BY_USER_ID_AND_PENDING_SUB_USER_EMAIL, $this->getTableName(), $userId, $pendingSubUserEmail);
        $result = $this->fetchRows($sqlQuery);
        if (count($result) === 1) {
            return $result[0];
        } else {
            return null;
        }
    }

}

?>