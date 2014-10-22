<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/SpecialFeeDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class SpecialFeesMapper extends AbstractMapper {

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
		$this->tableName = "special_fees";
	}

	/**
	 * Returns an singleton instance of this class
	 * @return
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new SpecialFeesMapper();
		}
		return self::$instance;
	}

	/**
	 */
	public function createDto() {
		return new SpecialFeeDto();
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

	public static $GET_SPEC_FEES_BY_IDS = "SELECT * FROM `%s` WHERE `id` in (%s)";

	public function getSpecialFeesByIds($special_fees_ids) {
		$sqlQuery = sprintf(self::$GET_SPEC_FEES_BY_IDS, $this->getTableName(), $special_fees_ids);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

}

?>