<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/EmailAccountsDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class EmailAccountsMapper extends AbstractMapper {

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
		$this->tableName = "email_accounts";
	}

	/**
	 * Returns an singleton instance of this class
	 * @return
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new EmailAccountsMapper();
		}
		return self::$instance;
	}

	/**
	 * @see AbstractMapper::createDto()
	 */
	public function createDto() {
		return new EmailAccountsDto();
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

}

?>