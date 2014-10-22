<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/CategoryHierarchyDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class CategoryHierarchyMapper extends AbstractMapper {

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
		$this->tableName = "category_hierarchy";
	}

	/**
	 * Returns an singleton instance of this class
	 * @return
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new CategoryHierarchyMapper();
		}
		return self::$instance;
	}

	/**
	 * @see AbstractMapper::createDto()
	 */
	public function createDto() {
		return new CategoryHierarchyDto();
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

	public static $GET_CATEGORY_HIERARCHY_BY_CHILD_CATEGORY_ID = "SELECT * FROM `%s` WHERE child_id = '%s'";

	public function getCategoryHierarchyByChildId($childId) {
		$sqlQuery = sprintf(self::$GET_CATEGORY_HIERARCHY_BY_CHILD_CATEGORY_ID, $this->getTableName(), $childId);
		$result = $this->fetchRows($sqlQuery);
		if (count($result) === 1) {
			return $result[0];
		} else {
			return null;
		}
	}

	public static $HAS_CAT_CHILD = "SELECT * FROM `%s` WHERE category_id = '%s'";

	public function hasCategoryChildren($catId) {
		$sqlQuery = sprintf(self::$HAS_CAT_CHILD, $this->getTableName(), $catId);
		$result = $this->fetchRows($sqlQuery);
		return count($result) > 0;
	}

	public static $GET_CATEGORY_CHILDREN = "SELECT * FROM `%s` WHERE category_id = '%s' ORDER BY `sort_index`";

	public function getCategoryChildren($catId) {
		$sqlQuery = sprintf(self::$GET_CATEGORY_CHILDREN, $this->getTableName(), $catId);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

	public static $GET_CATEGORIES_BY_PARENT_CATEGORY_ID = "SELECT display_name FROM (`%s` INNER JOIN `%s` ON `%s`.`id` = `%s`.`child_id`)  WHERE `category_id` = '%s' ORDER BY `%s`.`sort_index`";

	public function getCategoriesNamesByParentCategoryId($catId) {
		$sqlQuery = sprintf(self::$GET_CATEGORIES_BY_PARENT_CATEGORY_ID, 'categories', $this->getTableName(), 'categories', $this->getTableName(), $catId, $this->getTableName());
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

	public static $GET_CATEGORY_HIERARCHY_BY_SORT_INDEX = "SELECT * FROM `%s` WHERE category_id = '%s' AND sort_index = %d";

	public function getCategoryHierarchyBySortIndex($categoryId, $sortIndex) {
		$sqlQuery = sprintf(self::$GET_CATEGORY_HIERARCHY_BY_SORT_INDEX, $this->getTableName(), $categoryId, $sortIndex);
		$result = $this->fetchRows($sqlQuery);
		if (count($result) === 1) {
			return $result[0];
		} else { {
				return null;
			}
		}
	}

	public static $GET_CATEGORIES_BY_CATS_AND_CHILDS_IDS = "SELECT * FROM `%s` WHERE category_id IN (%s) and child_id IN (%s) ORDER BY category_id";

	public function getCategoriesByCatsAndChildsIds($cat_ids_array, $child_ids_array) {
		$sqlQuery = sprintf(self::$GET_CATEGORIES_BY_CATS_AND_CHILDS_IDS, $this->getTableName(), implode(',', $cat_ids_array), implode(',', $child_ids_array));
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

}

?>