<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/CategoryManager.class.php");
require_once (CLASSES_PATH . "/managers/CategoryHierarchyManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ItemLargeViewLoad extends GuestLoad {

    public function load() {

        $customer = $this->getCustomer();
        $vipCustomer = false;
        if (isset($customer)) {
            $userManager = UserManager::getInstance($this->config, $this->args);
            $vipCustomer = $userManager->isVipAndVipEnabled($customer);
        }
        if ($vipCustomer) {
            $pccDiscount = floatval($this->getCmsVar('vip_pc_configurator_discount'));
        } else {
            $pccDiscount = floatval($this->getCmsVar('pc_configurator_discount'));
        }


        $itemManager = ItemManager::getInstance($this->config, $this->args);
        if (isset($_REQUEST["item_id"])) {
            $item_id = $_REQUEST["item_id"];
        } elseif ($this->args[0]) {
            $item_id = $this->args[0];
        }

        $selectedItemDto = $itemManager->selectByPK($item_id);
        if (isset($selectedItemDto)) {
            $this->setDescriptionTagValue($selectedItemDto->getDisplayName());
            $this->setKeywordsTagValue($selectedItemDto->getDisplayName());
            $this->setTitleTagValue($selectedItemDto->getDisplayName());
        }

        $userLevel = $this->getUserLevel();
        $userId = $this->getUserId();
        $itemDto = $itemManager->getItemsForOrder($item_id, $userId, $userLevel, true);

        $this->addParam('item_id', $item_id);

        if ($itemDto) {
            $itemManager->growItemShowsCountByOne($itemDto);
            $itemPicturesCount = $itemDto->getPicturesCount();
            $this->addParam('item', $itemDto);

            //$this->addParam('userLevel', $userLevel);

            $this->addParam('itemManager', $itemManager);
            $this->addParam('itemPicturesCount', $itemPicturesCount);
            $this->addParam('itemPropertiesHierarchy', $itemManager->getItemProperties($item_id));
        }


        if ($this->getUserLevel() === UserGroups::$ADMIN) {
            $this->initRootCategories();
        }
    }

    public function getDefaultLoads($args) {
        $loads = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/item_large_view.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

    private function initRootCategories() {
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
    }

}

?>