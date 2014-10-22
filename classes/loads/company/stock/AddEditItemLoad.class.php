<?php

require_once (CLASSES_PATH . "/loads/company/CompanyLoad.class.php");
require_once (CLASSES_PATH . "/managers/CategoryManager.class.php");
require_once (CLASSES_PATH . "/managers/CategoryHierarchyManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class AddEditItemLoad extends CompanyLoad {

    public function load() {
        //Load First Level Categories from DB
        $categoryManager = CategoryManager::getInstance($this->config, $this->args);
        $categoryHierarchyManager = CategoryHierarchyManager::getInstance($this->config, $this->args);
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

        $this->addParam('firstLevelCategoriesNames', $firstLevelCategoriesNames);
        $this->addParam('firstLevelCategoriesIds', $firstLevelCategoriesIds);

        $item_warranty_options = explode(',', $this->getCmsVar('item_warranty_options'));
        $this->addParam('item_warranty_options', $item_warranty_options);
        $itemManager = ItemManager::getInstance($this->config, $this->args);
        if ($_REQUEST['item_id']) {
            $itemId = $this->secure($_REQUEST['item_id']);
            $itemDto = $itemManager->selectByPK($itemId);
            $this->addParam('item_pictures_count', $itemDto->getPicturesCount());
            $this->addParam('item_id', $itemDto->getId());
            $this->addParam('item_title', $itemDto->getDisplayName());
            $this->addParam('item_available', $itemManager->isItemAvailable($itemDto));
            $this->addParam('short_description', $itemDto->getShortDescription());
            $this->addParam('full_description', $itemDto->getFullDescription());
            $this->addParam('dealer_price', $itemDto->getDealerPrice());
            $this->addParam('vat_price', $itemDto->getVatPrice());
            $this->addParam('dealer_price_amd', $itemDto->getDealerPriceAmd());
            $this->addParam('vat_price_amd', $itemDto->getVatPriceAmd());
            $this->addParam('selected_warranty_option', $itemDto->getWarranty());
            $this->addParam('item_model', $itemDto->getModel());
            $this->addParam('item_brand', $itemDto->getBrand());
            $this->addParam('order_index_in_price', $itemDto->getOrderIndexInPrice());
            $this->addParam('item_available_till_date', $itemDto->getItemAvailableTillDate());

            $itemCategoriesIds = $itemDto->getCategoriesIds();
            $itemCategoriesIds = trim($itemCategoriesIds, ',');
            assert(!empty($itemCategoriesIds));

            $itemCategoriesIds = explode(',', $itemCategoriesIds);
            $selectedRootCategoryId = $itemCategoriesIds[0];
            $subCategoriesIdsArray = array_slice($itemCategoriesIds, 1);
            $this->addParam('selectedRootCategoryId', $selectedRootCategoryId);
            $this->addParam('sub_categories_ids', join(',', $subCategoriesIdsArray));
        } else {
            $selectedRootCategoryId = $firstLevelCategoriesHierarchyDtos[0]->getChildId();
            $this->addParam('selected_warranty_option', $item_warranty_options[12]);
            $this->addParam('selectedRootCategoryId', $selectedRootCategoryId);
            $this->addParam('sub_categories_ids', '');
        }


        $item_availability_options_names = array("Today", "2 days", "3 days", "1 week", "2 weeks", "1 month");
        $item_availability_options_values = array(0, 1, 2, 6, 13, 29);
        $this->addParam('item_availability_options_names', $item_availability_options_names);
        $this->addParam('item_availability_options_values', $item_availability_options_values);
        $this->addParam('item_availability_selected', $item_availability_options_values[3]);
        $this->addParam('company_id', intval($this->secure($_REQUEST["company_id"])));
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/company/stock/add_edit_item.tpl";
    }

}

?>