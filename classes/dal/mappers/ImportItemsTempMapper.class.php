<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/ImportItemsTempDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class ImportItemsTempMapper extends AbstractMapper {

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
		$this->tableName = "import_items_temp";
	}

	/**
	 * Returns an singleton instance of this class
	 * @return
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new ImportItemsTempMapper();
		}
		return self::$instance;
	}

	/**
	 * @see AbstractMapper::createDto()
	 */
	public function createDto() {
		return new ImportItemsTempDto();
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

	public function deleteCustomerRows($login) {
		$sql = "DELETE FROM `%s` WHERE login='%s'";
		$sqlReady = sprintf($sql, $this->tableName, $login);
		return $this->dbms->query($sqlReady);
	}

	public function getUserCurrentPriceNewRows($customerLogin) {
		$sql = "SELECT * FROM `%s` WHERE login='%s' AND (matched_item_id IS NULL OR matched_item_id=0)";
		$sqlReady = sprintf($sql, $this->tableName, $customerLogin);
		return $this->fetchRows($sqlReady);
	}

	public function getUserCurrentPriceChangedRows($customerLogin) {
		$sql = "SELECT * FROM `%s` WHERE login='%s' AND matched_item_id >0";
		$sqlReady = sprintf($sql, $this->tableName, $customerLogin);
		return $this->fetchRows($sqlReady);
	}

	public function getUserCurrentRows($customerLogin) {
		$sql = "SELECT * FROM `%s` WHERE login='%s'";
		$sqlReady = sprintf($sql, $this->tableName, $customerLogin);
		return $this->fetchRows($sqlReady);
	}

}

?>