<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/CmsSearchRequestsDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class CmsSearchRequestsMapper extends AbstractMapper {

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
		$this->tableName = "cms_search_requests";
	}

	/**
	 * Returns an singleton instance of this class
	 * @return
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new CmsSearchRequestsMapper();
		}
		return self::$instance;
	}

	/**
	 * @see AbstractMapper::createDto()
	 */
	public function createDto() {
		return new CmsSearchRequestsDto();
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

	public function getSearchStatisticsByDays($daysNumber) {
		$q = "SELECT search_text, COUNT(id) AS `search_count` FROM `%s` 
			WHERE DATE_SUB(CURRENT_DATE, INTERVAL %d DAY)<`datetime`				
			GROUP BY `search_text`, DATE(`datetime`) , `win_uid`
			 ORDER BY `search_count` DESC, `datetime` DESC";
		return $this->fetchRows(sprintf($q, $this->getTableName(), $daysNumber));
	}
	
	public function removeOldRowsByDays($days) {
		$sqlQuery = sprintf("DELETE FROM `%s` WHERE `datetime`<DATE_SUB('%s', INTERVAL %d DAY)", $this->getTableName(), date('Y-m-d H:i:s'), $days);
		$this->dbms->query($sqlQuery);
	}

}

?>