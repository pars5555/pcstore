<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/CompanyBranchesDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class CompanyBranchesMapper extends AbstractMapper {

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
		$this->tableName = "company_branches";
	}

	/**
	 * Returns an singleton instance of this class
	 * @return
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new CompanyBranchesMapper();
		}
		return self::$instance;
	}

	/**
	 * @see AbstractMapper::createDto()
	 */
	public function createDto() {
		return new CompanyBranchesDto();
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

	public function getCompaniesBranches($companiesIdsArray) {
		$sql = sprintf("SELECT * FROM `%s` WHERE `company_id` IN (%s) ORDER BY FIELD(company_id, %s)", $this->getTableName(), $companiesIdsArray, $companiesIdsArray);
		return $this->fetchRows($sql);
	}

}

?>