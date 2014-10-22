<?php

require_once (CLASSES_PATH . "/loads/admin/AdminLoad.class.php");
require_once (CLASSES_PATH . "/managers/CategoryManager.class.php");
require_once (CLASSES_PATH . "/managers/CategoryHierarchyManager.class.php");
require_once (CLASSES_PATH . "/util/TreeView/TreeView.php");
require_once (CLASSES_PATH . "/util/TreeView/ItemsCategoryTreeView.php");
require_once (CLASSES_PATH . "/util/TreeView/TreeViewModel.php");
require_once (CLASSES_PATH . "/util/TreeView/ItemsCategoryTreeViewModel.php");
require_once (CLASSES_PATH . "/util/TreeView/TreeNode.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ItemsCategoriesLoad extends AdminLoad {

	private $categoryManager;
	private $categoryHierarchyManager;
	private $treeViewModel;

	public function load() {
		$this->categoryManager = CategoryManager::getInstance($this->config, $this->args);
		$this->categoryHierarchyManager = CategoryHierarchyManager::getInstance($this->config, $this->args);
		$rootDto = $this->categoryManager->getRoot();

		$this->treeViewModel = new ItemsCategoryTreeViewModel($rootDto->getId(), $rootDto->getDisplayName(), $rootDto, true);
		$this->fillTreeViewModel($rootDto);
		$treeView = new ItemsCategoryTreeView($this->treeViewModel, true, "my_tree");
		$this->addParam('treeView', $treeView);

		$this->addParam("ItemCategoriesLeftBarWidth", $this->getCmsVar("item_categories_left_bar_width"));
		$this->addParam("ItemCategoriesRightBarWidth", $this->getParam("wholePageWidth") - $this->getCmsVar("item_categories_left_bar_width"));
	}

	public function fillTreeViewModel($categoryDto) {
		$catId = $categoryDto->getId();
		$categoryChildrenIdsArray = $this->categoryHierarchyManager->getCategoryChildrenIdsArray($catId);
		foreach ($categoryChildrenIdsArray as $childId) {
			$childCatDto = $this->categoryManager->getCategoryById($childId);
			assert($childCatDto);
			//category doesn't exist
			$this->treeViewModel->_insertNode($catId, $childId, $childCatDto->getDisplayName(), $childCatDto, false);
			$this->fillTreeViewModel($childCatDto);
		}
	}

	public function getTemplate() {
		return TEMPLATES_DIR . "/admin/items_categories.tpl";
	}

}

?>