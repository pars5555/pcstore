<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/ItemWarrantyDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class ItemWarrantiesMapper extends AbstractMapper {

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
		$this->tableName = "item_warranties";
	}

	/**
	 * Returns an singleton instance of this class
	 * @return
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new ItemWarrantiesMapper();
		}
		return self::$instance;
	}

	/**
	 * @see AbstractMapper::createDto()
	 */
	public function createDto() {
		return new ItemWarrantyDto();
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

	public static $GET_COMPANY_WARRANTY_ITEMS = "SELECT * FROM `%s` WHERE `company_id` = %d %s LIMIT %d, %d";

	public function getCompanyWarrantyItems($companyId, $search_serial_number, $offset, $limit) {
		$s = "";
		if ($search_serial_number && strlen($search_serial_number) > 0) {
			$s = "AND serial_number LIKE '%" . $search_serial_number . "%'";
		}
		$sqlQuery = sprintf(self::$GET_COMPANY_WARRANTY_ITEMS, $this->getTableName(), $companyId, $s, $offset, $limit);

		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

	public static $GET_COMPANY_ALL_WARRANTY_ITEMS = "SELECT * FROM `%s` WHERE `company_id` = %d ORDER BY `customer_warranty_start_date` DESC";

	public function getCompanyAllWarrantyItems($companyId) {
		$sqlQuery = sprintf(self::$GET_COMPANY_ALL_WARRANTY_ITEMS, $this->getTableName(), $companyId);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

}

?>