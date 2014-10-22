<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/CategoryDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class CategoryMapper extends AbstractMapper {

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
		$this->tableName = "categories";
	}

	/**
	 * Returns an singleton instance of this class
	 * @return
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new CategoryMapper();
		}
		return self::$instance;
	}

	/**
	 * @see AbstractMapper::createDto()
	 */
	public function createDto() {
		return new CategoryDto();
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

	public static $GET_CATS_BY_IDS = "SELECT * FROM `%s` WHERE";

	public function getCategoriesByIds($cat_ids, $order_by_ids = false) {
		$sqlQuery = sprintf(self::$GET_CATS_BY_IDS, $this->getTableName());
		$cat_ids_comma_seperated = implode(',', $cat_ids);
		$where .= " id IN (" . $cat_ids_comma_seperated . ')';
		$sqlQuery .= $where;
		if ($order_by_ids) {
			$sqlQuery .= ' ORDER BY FIELD(id,' . $cat_ids_comma_seperated . ')';
		}
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

	public static $QUERY_10 = "SELECT * FROM `%s` WHERE last_clickable = 1 AND id IN (%s)";

	public function getLastClickableCategoryFromCatIds($cat_ids_array) {
		$sqlQuery = sprintf(self::$QUERY_10, $this->getTableName(), implode(',', $cat_ids_array));
		//var_dump($sqlQuery );
		$result = $this->fetchRows($sqlQuery);
		if (count($result) === 1) {
			return $result[0];
		} else {
			return null;
		}
	}

}

?>