<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/BonusHistoryDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class BonusHistoryMapper extends AbstractMapper {

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
		$this->tableName = "bonus_history";
	}

	/**
	 * Returns an singleton instance of this class
	 * @return
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new BonusHistoryMapper();
		}
		return self::$instance;
	}

	/**
	 * @see AbstractMapper::createDto()
	 */
	public function createDto() {
		return new BonusHistoryDto();
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

	public static $GET_AFTER_DATE = "SELECT * FROM `%s` WHERE user_id = %d AND `datetime`>'%s' ";

	public function getUserBonusesAfterGivenDatetime($userId, $datetime) {
		$sqlQuery = sprintf(self::$GET_AFTER_DATE, $this->getTableName(), $userId, $datetime);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

}

?>