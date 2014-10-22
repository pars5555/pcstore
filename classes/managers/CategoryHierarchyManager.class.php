<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/CategoryHierarchyMapper.class.php");

/**
 * CategoryHierarchyManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class CategoryHierarchyManager extends AbstractManager {

	/**
	 * @var app config
	 */
	private $config;	
	private $allCategoryHierarchyMap;

	/**
	 * @var passed arguemnts
	 */
	private $args;

	/**
	 * @var singleton instance of class
	 */
	private static $instance = null;

	/**
	 * Initializes DB mappers
	 *
	 * @param object $config
	 * @param object $args
	 * @return
	 */
	function __construct($config, $args) {
		$this->mapper = CategoryHierarchyMapper::getInstance();
		$this->config = $config;
		$this->args = $args;
		$this->cacheAllHierarchyInMap();
	}

	private function cacheAllHierarchyInMap() {
		$allDtos = $this->mapper->selectAll();
		foreach ($allDtos as $dto) {
			$this->allCategoryHierarchyMap[intval($dto->getCategoryId())][$dto->getSortIndex()] = intval($dto->getChildId());
		}
		foreach ($this->allCategoryHierarchyMap as $catId => $arr) {
			ksort($arr, SORT_NUMERIC);
			$this->allCategoryHierarchyMap [$catId] = $arr;
		}
	}

	/**
	 * Returns an singleton instance of this class
	 *
	 * @param object $config
	 * @param object $args
	 * @return
	 */
	public static function getInstance($config, $args) {

		if (self::$instance == null) {

			self::$instance = new CategoryHierarchyManager($config, $args);
		}
		return self::$instance;
	}

	/**
	 * This function doesn't call to DB, it returns from cache
	 * @param int $id
	 * @return array
	 */
	public function hasCategoryChildren($catId) {
		return !empty($this->allCategoryHierarchyMap[intval($catId)]);
            }
            
	/**
	 * This function doesn't call to DB, it returns from cache
	 * @param int $id
	 * @return array
	 */
	public function getCategoryChildrenIdsArray($id) {
		return array_key_exists(intval($id), $this->allCategoryHierarchyMap) ? $this->allCategoryHierarchyMap[intval($id)] : array();
        }
	
	/**
	 * This function doesn't call to DB, it returns from cache
	 * @param int $id
	 * @return array
	 */
	public function getCategoryGroupedSubProperties($id) {
		$categoryFirstLevelChildrenIds = $this->getCategoryChildrenIdsArray($id);
		$ret = array();
		foreach ($categoryFirstLevelChildrenIds as $catId) {
			$ret[$catId] = $this->getCategoryChildrenIdsArray($catId);
		}
		return $ret;
	}

	/**
	 * This function doesn't call to DB, it returns from cache
	 * @param int $id
	 * @return array
	 */
	public function getCategorySubTreeIds($id) {
		$categoryFirstLevelChildrenIds = $this->getCategoryChildrenIdsArray($id);
		$ret = $categoryFirstLevelChildrenIds;
		foreach ($categoryFirstLevelChildrenIds as $catId) {
			$ret = array_merge($ret, $this->getCategoryChildrenIdsArray($catId));
}
		return $ret;
	}

	/**
	 * This function doesn't call to DB, it returns from cache
	 * @param int $id
	 * @return array
	 */
	public function getCategoryParentId($categoryId) {
		foreach ($this->allCategoryHierarchyMap as $catId => $ChildrenIdsArray) {
			foreach ($ChildrenIdsArray as $childId) {
				if ($childId == $categoryId) {
					return $catId;
				}
			}
		}
		return false;
	}

	public function getCategoryChildren($id) {
		return $this->mapper->getCategoryChildren($id);
	}

	public function getCategoriesNamesByParentCategoryId($id) {
		return $this->mapper->getCategoriesNamesByParentCategoryId($id);
	}

	public function addSubCategoryToCategory($categoryId, $subCategoryId, $sortIndex) {
		$dto = $this->mapper->createDto();
		$dto->setCategoryId($categoryId);
		$dto->setChildId($subCategoryId);
		$dto->setSortIndex($sortIndex);
		$this->mapper->insertDto($dto);
	}

	public function MoveCategoryOrderUp($catId) {

		$catDto = $this->mapper->getCategoryHierarchyByChildId($catId);
		$index = intval($catDto->getSortIndex());
		if ($index > 1) {
			$prevCatDto = $this->mapper->getCategoryHierarchyBySortIndex($catDto->getCategoryId(), $index - 1);
			assert($prevCatDto);
			$prevCatDto->setSortIndex($index);
			$this->mapper->updateByPK($prevCatDto);
			$catDto->setSortIndex($index - 1);
			$this->mapper->updateByPK($catDto);
		}
	}

	public function MoveCategoryOrderDown($catId) {

		$catDto = $this->mapper->getCategoryHierarchyByChildId($catId);
		$index = intval($catDto->getSortIndex());
		if ($index > 0) {
			$nextCatDto = $this->mapper->getCategoryHierarchyBySortIndex($catDto->getcategoryId(), $index + 1);
			if ($nextCatDto) {
				$nextCatDto->setSortIndex($index);
				$this->mapper->updateByPK($nextCatDto);
				$catDto->setSortIndex($index + 1);
				$this->mapper->updateByPK($catDto);
			}
		}
	}

	public function removeCategoryHierarchyByChildCategoryID($childCategoryId) {
		$catDto = $this->mapper->getCategoryHierarchyByChildId($childCategoryId);
		if ($catDto) {
			$this->deleteByPK($catDto->getId());
		}
	}

	public function getCategoriesByCatsAndChildsIds($cat_ids_array, $child_ids_array) {
		return $this->mapper->getCategoriesByCatsAndChildsIds($cat_ids_array, $child_ids_array);
	}

}

?>