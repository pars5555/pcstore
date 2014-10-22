<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/CustomerMessagesAfterLoginDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class CustomerMessagesAfterLoginMapper extends AbstractMapper {

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
        $this->tableName = "customer_messages_after_login";
    }

    /**
     * Returns an singleton instance of this class
     * @return
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new CustomerMessagesAfterLoginMapper();
        }
        return self::$instance;
    }

    /**
     * @see AbstractMapper::createDto()
     */
    public function createDto() {
        return new CustomerMessagesAfterLoginDto();
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

    public static $GET_ADMIN = "SELECT * FROM `%s` WHERE `email` = '%s' AND `shows_count`>`showed_count`";

    public function getCustomerMessages($email) {
        $sqlQuery = sprintf(self::$GET_ADMIN, $this->getTableName(), $email);
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

}

?>