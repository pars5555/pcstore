<?php

require_once (CLASSES_PATH . "/loads/admin/AdminLoad.class.php");
require_once (CLASSES_PATH . "/managers/ImportItemsTempManager.class.php");
require_once (CLASSES_PATH . "/managers/ImportPriceManager.class.php");
require_once (CLASSES_PATH . "/managers/CategoryHierarchyManager.class.php");
require_once (CLASSES_PATH . "/managers/CategoryManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ImportStepTwoLoad extends AdminLoad {

    public function load() {
        //new items import level
        $company_id = $_REQUEST['company_id'];
        $used_columns_indexes_array = array(2/* name */, 1/* model */, 9/* brand */, 3/* dealer price $ */, 4/* $dealer price amd */, 5/* vat $ */, 6/* vat amd */, 7/* warranty */); //explode(',', $_REQUEST['used_columns_indexes']);
        $importItemsTempManager = ImportItemsTempManager::getInstance($this->config, $this->args);
        $categoryHierarchyManager = CategoryHierarchyManager::getInstance($this->config, $this->args);
        $categoryManager = CategoryManager::getInstance($this->config, $this->args);

        $customerLogin = $this->getCustomerLogin();
        $priceRowsDtos = $importItemsTempManager->getUserCurrentPriceNewRows($customerLogin);
        foreach ($priceRowsDtos as $dto) {
            $itemModel = $dto->getModel();
            if (empty($itemModel)) {
                $model = $importItemsTempManager->findModelFromItemTitle($dto->getDisplayName());
                if (!empty($model)) {
                    $dto->setSupposedModel($model);
                }
            } else {
                $dto->setSupposedModel($itemModel);
            }
        }


        $columnNames = ImportPriceManager::getColumnNamesMap($used_columns_indexes_array);

        $rootDto = $categoryManager->getRoot();
        $firstLevelCategoriesHierarchyDtos = $categoryHierarchyManager->getCategoryChildren($rootDto->getId());
        $firstLevelCategoriesNamesDtos = $categoryHierarchyManager->getCategoriesNamesByParentCategoryId($rootDto->getId());


        $firstLevelCategoriesIds = array();
        foreach ($firstLevelCategoriesHierarchyDtos as $key => $category) {
            $firstLevelCategoriesIds[] = $category->getChildId();
        }
        $firstLevelCategoriesNames = array();
        foreach ($firstLevelCategoriesNamesDtos as $key => $category) {
            $firstLevelCategoriesNames[] = $category->getDisplayName();
        }

        $this->addParam('columnNames', $columnNames);
        $this->addParam('priceRowsDtos', $priceRowsDtos);
        $this->addParam('firstLevelCategoriesNames', $firstLevelCategoriesNames);
        $this->addParam('firstLevelCategoriesIds', $firstLevelCategoriesIds);

        if (isset($_REQUEST['new_items_row_ids'])) {
            $this->addParam('new_items_row_ids', explode(',', $_REQUEST['new_items_row_ids']));
        }

        //$json_parsed_price = $_REQUEST['json_parsed_price'];
        //$parsed_price_array = json_decode($json_parsed_price);
        /* $companyItems = $itemManager->getCompanyItems($company_id, true);

          $stockItemModels = array();
          $stockItemNames = array();
          $stockItemDealerPrices = array();
          $stockItemVatPrices = array();
          $stockItemIsHidden = array();
          foreach ($companyItems as $itemDto) {
          $stockItemModels[] =
          } */
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/admin/import/import_step_two.tpl";
    }

}

?>