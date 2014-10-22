<?php

require_once (CLASSES_PATH . "/loads/company/CompanyLoad.class.php");
require_once (CLASSES_PATH . "/util/TreeView/SubCategorySelectionTreeViewModel.php");
require_once (CLASSES_PATH . "/util/TreeView/SubCategorySelectionTreeView.php");
require_once (CLASSES_PATH . "/managers/CategoryManager.class.php");
require_once (CLASSES_PATH . "/managers/CategoryHierarchyManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class SubCategoriesSelectionLoad extends CompanyLoad {

    private $categoryManager;
    private $categoryHierarchyManager;
    private $treeViewModel;

    public function load() {

        $this->categoryManager = CategoryManager::getInstance($this->config, $this->args);
        $this->categoryHierarchyManager = CategoryHierarchyManager::getInstance($this->config, $this->args);
        $rootCategoryId = $this->secure($_REQUEST["item_root_category"]);

        $rootCategoryDto = $this->categoryManager->getCategoryById($rootCategoryId);

        $this->treeViewModel = new SubCategorySelectionTreeViewModel($rootCategoryDto->getId(), $rootCategoryDto->getDisplayName(), $rootCategoryDto, true);
        $this->fillTreeViewModel($rootCategoryDto);

        $treeView = new SubCategorySelectionTreeView($this->treeViewModel, false, "sub_category_tree");
        $treeView->setRowsIndent(25);

        $this->addParam('selectSubCatTreeView', $treeView);
    }

    public function fillTreeViewModel($categoryDto) {

        $catId = $categoryDto->getId();
        $categoryChildrenIdsArray = $this->categoryHierarchyManager->getCategoryChildrenIdsArray($catId);

        foreach ($categoryChildrenIdsArray as $key => $childId) {
            $childCatDto = $this->categoryManager->getCategoryById($childId);
            assert($childCatDto);
            //category doesn't exist
            $this->treeViewModel->_insertNode($catId, $childId, $childCatDto->getDisplayName(), $childCatDto, false);
            $this->fillTreeViewModel($childCatDto);
        }
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/company/stock/sub_categories_selection.tpl";
    }

}

?>