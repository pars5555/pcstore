<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/CreditSuppliersDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class CreditSuppliersMapper extends AbstractMapper {

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
		$this->tableName = "credit_suppliers";
	}

	/**
	 * Returns an singleton instance of this class
	 * @return
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new CreditSuppliersMapper();
		}
		return self::$instance;
	}

	/**
	 * @see AbstractMapper::createDto()
	 */
	public function createDto() {
		return new CreditSuppliersDto();
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

	private static $GET_ALL_SUP = "SELECT * FROM `%s` WHERE `visible`=1";

	public function getAllCreditSuppliers() {
		$sqlQuery = sprintf(self::$GET_ALL_SUP, $this->getTableName());
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

}

?>