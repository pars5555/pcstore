<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyDealersManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/CategoryHierarchyManager.class.php");
require_once (CLASSES_PATH . "/managers/CategoryManager.class.php");
require_once (CLASSES_PATH . "/managers/CmsSearchRequestsManager.class.php");
require_once (CLASSES_PATH . "/util/category_property_view/CategoryPropertyView.php");
require_once (CLASSES_PATH . "/managers/CompanyDealersManager.class.php");
require_once (CLASSES_PATH . "/managers/checkout/CreditManager.class.php");
require_once (CLASSES_PATH . "/managers/CreditSuppliersManager.class.php");
require_once (CLASSES_PATH . "/util/TreeView/filter_items_tree_view/FilterItemsTreeView.php");
require_once (CLASSES_PATH . "/util/TreeView/filter_items_tree_view/FilterItemsTreeViewModel.php");
require_once (CLASSES_PATH . "/util/menuview/ItemCategoryModel.php");
require_once (CLASSES_PATH . "/util/menuview/ItemsCategoryMenuView.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ItemSearchLoad extends GuestLoad {

    private $totalItemsRowsCount = 0;
    private $current_page_number = 1;
    private $large_view_load_enable = false;

    public function load() {
        $this->addParam("searchLeftBarWidth", $this->getCmsVar("search_left_bar_width"));
        $itemManager = ItemManager::getInstance($this->config, $this->args);

        $this->setDescriptionTagValue('Serach any item you want. PC, Laptop, Tablet, Computer, Printer, Monitor...');
        $this->setTitleTagValue('Pcstore Search');


        if (isset($_COOKIE['copied_item_id'])) {
            $this->addParam('copied_item_id', $_COOKIE['copied_item_id']);
        }

        if (isset($this->args[0])) {
            $this->large_view_load_enable = true;
            $selectedItemId = $this->args[0];
            $this->addParam('selected_item_id', $selectedItemId);
        }

        $this->current_page_number = 1;
        if (isset($_REQUEST["spg"])) {
            $this->current_page_number = $_REQUEST["spg"];
        }
        $userLevel = $this->getUserLevel();

        $this->categoryManager = CategoryManager::getInstance($this->config, $this->args);
        $this->categoryHierarchyManager = CategoryHierarchyManager::getInstance($this->config, $this->args);


        $selectedCompanyId = $this->initCompaniesSelectionList();

        $selectedCategoryId = 0;
        if (!empty($_REQUEST["cid"])) {
            $selectedCategoryId = $this->secure($_REQUEST["cid"]);
        }


        $selected_category_property_ids = isset($_REQUEST["scpids"]) ? $this->secure($_REQUEST["scpids"]) : '';
        $groupedProperties = null;
        if (!empty($selected_category_property_ids)) {
            $this->addParam('selected_category_property_ids', $selected_category_property_ids);
            $selected_category_property_ids = explode(',', $selected_category_property_ids);
            $groupedProperties = $this->groupCategoryProperties($selected_category_property_ids);
        } else {
            $selected_category_property_ids = array();
            $groupedProperties = array();
        }

        $item_search_limit_rows = intval($this->getCmsVar("item_search_limit_rows"));

        $userId = $this->getUserId();


        $price_range_min = isset($_REQUEST["prmin"]) ? $this->secure($_REQUEST["prmin"]) : '';
        $price_range_max = isset($_REQUEST["prmax"]) ? $this->secure($_REQUEST["prmax"]) : '';

        $this->addParam('search_item_price_range_min_value', $price_range_min);
        $this->addParam('search_item_price_range_max_value', $price_range_max);

        if (!empty($price_range_min) && strlen($price_range_min) > 0) {
            $price_range_min = floatval($this->secure($_REQUEST["prmin"])) / floatval($this->getCmsVar('us_dollar_exchange'));
        }
        if (!empty($price_range_max) && strlen($price_range_max) > 0) {
            $price_range_max = floatval($this->secure($_REQUEST["prmax"])) / floatval($this->getCmsVar('us_dollar_exchange'));
        }



        $orderByFieldName = $this->initSortBySelectionList();

        if ($orderByFieldName === 'relevance') {
            $orderByFieldName = null;
        }

        $search_text = isset($_REQUEST["st"]) ? $this->secure($_REQUEST["st"]) : '';
        $this->addParam("search_text", $search_text);


        if (!empty($search_text)) {
            $cmsSearchRequestsManager = CmsSearchRequestsManager::getInstance($this->config, $this->args);
            $cmsSearchRequestsManager->addRow($search_text, date('Y-m-d H:i:s'), $_REQUEST['win_uid']);
        }

        $show_only_vat_items = 0;
        if (isset($_REQUEST['shov'])) {
            $show_only_vat_items = $this->secure($_REQUEST['shov']);
            $this->addParam('show_only_vat_items', 1);
        }


        $show_only_non_picture_items = null;
        if (isset($_REQUEST["show_only_non_picture_items"])) {
            $show_only_non_picture_items = intval($_REQUEST["show_only_non_picture_items"]);
            $this->addParam('show_only_non_picture_items', $show_only_non_picture_items);
        }

        $show_only_no_short_spec_items = null;
        if (isset($_REQUEST["show_only_no_short_spec_items"])) {
            $show_only_no_short_spec_items = intval($_REQUEST["show_only_no_short_spec_items"]);
            $this->addParam('show_only_no_short_spec_items', $show_only_no_short_spec_items);
        }

        $show_only_no_full_spec_items = null;
        if (isset($_REQUEST["show_only_no_full_spec_items"])) {
            $show_only_no_full_spec_items = intval($_REQUEST["show_only_no_full_spec_items"]);
            $this->addParam('show_only_no_full_spec_items', $show_only_no_full_spec_items);
        }

        searchStared:
        $offset = $item_search_limit_rows * ($this->current_page_number - 1);
        $foundItems = $itemManager->searchItemsByTitle($userId, $userLevel, $search_text, $selectedCompanyId, $price_range_min, $price_range_max, $selectedCategoryId, $groupedProperties, $show_only_vat_items, $show_only_non_picture_items, $show_only_no_short_spec_items, $show_only_no_full_spec_items, $offset, $item_search_limit_rows, $orderByFieldName);
        $itemsDtosOnlyCategories = $itemManager->searchItemsByTitleRowsCount($userId, $search_text, $selectedCompanyId, $price_range_min, $price_range_max, $selectedCategoryId, null, $show_only_vat_items, $show_only_non_picture_items, $show_only_no_short_spec_items, $show_only_no_full_spec_items);

        $this->totalItemsRowsCount = 0;
        $this->categories_count_array = array();
        $selectedCategoryGroupedSubProperties = array();
        $selectedCategorySubTreeIds = array();
        $propertyViewIsVisible = false;
        $selectedCategoryDto = $this->categoryManager->getCategoryById($selectedCategoryId);
        if ($selectedCategoryDto->getLastClickable() == 1) {
            $selectedCategoryGroupedSubProperties = $this->categoryHierarchyManager->getCategoryGroupedSubProperties($selectedCategoryId);
            $selectedCategorySubTreeIds = $this->categoryHierarchyManager->getCategorySubTreeIds($selectedCategoryId);
            $propertyViewIsVisible = true;
        }
        foreach ($itemsDtosOnlyCategories as $itemDto) {
            $categoriesIds = trim($itemDto->getCategoriesIds(), ',');
            $categoriesIdsArray = explode(',', $categoriesIds);
            $ItemIsVisible = true;
            foreach ($groupedProperties as $propertiesGroupStaticCategoryId => $propIdsArray) {
                if (!(empty($propIdsArray) || count(array_intersect($categoriesIdsArray, $propIdsArray)) > 0)) {
                    $ItemIsVisible = false;
                    break;
                }
            }

            if ($ItemIsVisible) {
                $this->totalItemsRowsCount++;
            }

            //here calculating categories count which is valid for only category menu items not properties
            if (!$propertyViewIsVisible) {
                foreach ($categoriesIdsArray as $catId) {
                    if (!in_array($catId, $selectedCategorySubTreeIds)) {
                        if (!array_key_exists($catId, $this->categories_count_array)) {
                            $this->categories_count_array[$catId] = 1;
                        } else {
                            $this->categories_count_array[$catId] +=1;
                        }
                    }
                }
            } else {

                //here calculating future selecting properties count
                foreach ($selectedCategoryGroupedSubProperties as $propertyGroupCategoryId => $propertiesIds) {
                    $isItemVisibleForCurrentlySelectedOtherPropertyGroup = true;
                    foreach ($groupedProperties as $propertiesGroupStaticCategoryId => $propIdsArray) {
                        if ($propertiesGroupStaticCategoryId == $propertyGroupCategoryId) {
                            continue;
                        }
                        if (!(empty($propIdsArray) || count(array_intersect($categoriesIdsArray, $propIdsArray)) > 0)) {
                            $isItemVisibleForCurrentlySelectedOtherPropertyGroup = false;
                            break;
                        }
                    }



                    foreach ($propertiesIds as $proertyId) {
                        if (in_array($proertyId, $selected_category_property_ids)) {
                            //this is for optimization
                            continue;
                        }
                        if ($isItemVisibleForCurrentlySelectedOtherPropertyGroup && in_array($proertyId, $categoriesIdsArray)) {
                            if (!array_key_exists($proertyId, $this->categories_count_array)) {
                                $this->categories_count_array[$proertyId] = 1;
                            } else {
                                $this->categories_count_array[$proertyId] +=1;
                            }
                        }
                    }
                }
            }
        }

        //if page number exceed last page number then go to last page
        if (count($foundItems) === 0 && $this->totalItemsRowsCount > 0) {
            $lastPageNumber = ceil($this->totalItemsRowsCount / $item_search_limit_rows);
            $this->current_page_number = $lastPageNumber;
            goto searchStared;
        }

        $this->addParam("foundItems", $foundItems);
        $this->addParam("itemManager", $itemManager);
        $this->addParam("totalItemsRowsCount", $this->totalItemsRowsCount);

        ///credit items calculation
        $creditManager = CreditManager::getInstance($this->config, $this->args);
        $creditSuppliersManager = CreditSuppliersManager::getInstance($this->config, $this->args);
        $allCreditSuppliersDtos = $creditSuppliersManager->getAllCreditSuppliers();
        $allCreditSuppliersDtos = $creditSuppliersManager->getCreditSuppliersInMapArrayById($allCreditSuppliersDtos);
        $creditPossibleMonths = $creditManager->getAllSuppliersCombinePossibleMonths($allCreditSuppliersDtos);

        $this->addParam("creditPossibleMonthsValues", $creditPossibleMonths);
        $this->addParam("defaultSelectedCreditMonths", end($creditPossibleMonths));
        $defaultCreditSupplierDto = reset($allCreditSuppliersDtos);


        $this->addParam("defaultSupplierCommission", floatval($defaultCreditSupplierDto->getCommission()));
        $this->addParam("defaultCreditInterestMonthlyRatio", floatval($defaultCreditSupplierDto->getAnnualInterestPercent() / 100 / 12 + $defaultCreditSupplierDto->getAnnualCommision() / 100 / 12));


        //categories 
        if ($selectedCategoryDto->getLastClickable() == '0') {
            $itemCategoryModel = new ItemCategoryModel(!empty($selectedCategoryId) ? $selectedCategoryId : 0);
            $itemsCategoryMenuView = new ItemsCategoryMenuView($itemCategoryModel, $this->categories_count_array, $this->config, false);
            $this->addParam('itemsCategoryMenuView', $itemsCategoryMenuView);
        }

        //selected category properties
        $propertiesViews = array();
        if (isset($selectedCategoryId) && $this->categoryManager->getCategoryById($selectedCategoryId)->getLastClickable() == '1') {
            $propertiesHierarchyDtos = $this->categoryHierarchyManager->getCategoryChildren($selectedCategoryId);
            foreach ($propertiesHierarchyDtos as $propertyHierarchyDto) {
                $propertyView = new CategoryPropertyView($this->categoryManager, $this->categoryHierarchyManager, $this->categories_count_array, $propertyHierarchyDto->getChildId(), $selected_category_property_ids);
                $propertiesViews[] = $propertyView;
            }
        }
        $this->addParam("properties_views", $propertiesViews);
        $this->addParam('category_id', $selectedCategoryId);
        $this->addParam('category_dto', $selectedCategoryDto);
        if ($selectedCategoryId > 0) {
            $categoryFullPath = $this->categoryManager->getCategoryFullPath($selectedCategoryId);
            if (count($categoryFullPath) >= 1) {
                $this->addParam('category_path', $categoryFullPath);
                $this->addParam('itemSearchManager', ItemSearchManager::getInstance($this->config, $this->args));
            }
        }
    }

    public function groupCategoryProperties($selected_category_property_ids) {
        assert($selected_category_property_ids);
        $ret = array();
        $allCategoryHierarchiesDtos = $this->categoryHierarchyManager->selectAll();
        $mappedChildParentCategories = $this->putCategoriesHierarchiesInMapChildParent($allCategoryHierarchiesDtos);
        foreach ($selected_category_property_ids as $key => $value) {
            $propParentCatId = $mappedChildParentCategories[$value];
            $ret[$propParentCatId][] = $value;
        }
        return $ret;
    }

    public function putCategoriesHierarchiesInMapChildParent($allCategoriesHierarchies) {
        $ret = array();
        foreach ($allCategoriesHierarchies as $key => $dto) {
            $ret[$dto->getChildId()] = $dto->getCategoryId();
        }
        return $ret;
    }

    public function initSortBySelectionList() {

        $sort_by_values = array('relevance', 'customer_item_price');
        $sort_by_display_names = array($this->getPhrase(155), $this->getPhrase(156));
        $sort_by_display_names_phrase_ids_array = array(155, 156);
        if (isset($_REQUEST["srt"])) {
            $selected_sort_by_value = $this->secure($_REQUEST["srt"]);
        } else {
            $selected_sort_by_value = $sort_by_values[0];
        }

        $this->addParam("sort_by_values", $sort_by_values);
        $this->addParam("sort_by_display_names", $sort_by_display_names);
        $this->addParam("sort_by_display_names_phrase_ids_array", $sort_by_display_names_phrase_ids_array);
        $this->addParam("selected_sort_by_value", $selected_sort_by_value);
        return $selected_sort_by_value;
    }

    public function initCompaniesSelectionList() {

        $userLevel = $this->getUserLevel();

        $companyManager = CompanyManager::getInstance($this->config, $this->args);
        $companiesNames = array();
        $companiesIds = array();
        if ($userLevel === UserGroups::$COMPANY || $userLevel === UserGroups::$SERVICE_COMPANY || $userLevel === UserGroups::$ADMIN) {
            $allCompanies = $companyManager->getAllCompanies($userLevel === UserGroups::$ADMIN || false, $userLevel === UserGroups::$ADMIN);
            $companiesIds = $companyManager->getCompaniesIdsArray($allCompanies);
            $companiesNames = $companyManager->getCompaniesNamesArray($allCompanies);
        } elseif ($userLevel === UserGroups::$USER) {
            $userId = $this->getUserId();
            $companiesDtos = $companyManager->getUserCompanies($userId, false);
            $companiesIds = $companyManager->getCompaniesIdsArray($companiesDtos);
            $companiesNames = $companyManager->getCompaniesNamesArray($companiesDtos);
        }
        $selectedCompanyId = 0;
        array_splice($companiesIds, 0, 0, 0);
        array_splice($companiesNames, 0, 0, $this->getPhrase(153));
        $this->addParam("companiesIds", $companiesIds);
        $this->addParam("companiesNames", $companiesNames);
        if (isset($_REQUEST["sci"])) {
            $selectedCompanyId = $this->secure($_REQUEST["sci"]);
        }
        $this->addParam("selectedCompanyId", $selectedCompanyId);
        return $selectedCompanyId;
    }

    public function getDefaultLoads($args) {
        $loads = array();

        if ($this->large_view_load_enable) {
            $loadName = "ItemLargeViewLoad";
            $loads["item_large_view"]["load"] = "loads/main/" . $loadName;
            $loads["item_large_view"]["args"] = array("parentLoad" => &$this);
            $loads["item_large_view"]["loads"] = array();
        }


        if ($this->totalItemsRowsCount > 0) {

            $loadName = "PagingLoad";
            $loads["paging"]["load"] = "loads/main/" . $loadName;
            $loads["paging"]["args"] = array("parentLoad" => &$this, "current_page_number" => $this->current_page_number, "total_items_count" => $this->totalItemsRowsCount);
            $loads["paging"]["loads"] = array();
        }

        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/item_search.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

    protected function logRequest() {
        return true;
    }

}

?>