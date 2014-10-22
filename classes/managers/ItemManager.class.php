<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/managers/CategoryManager.class.php");
require_once (CLASSES_PATH . "/managers/CategoryHierarchyManager.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/AdminManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/ItemMapper.class.php");
require_once (CLASSES_PATH . "/util/pcc_categories_constants/CategoriesConstants.php");
require_once (CLASSES_PATH . "/util/ImageThumber.php");

/**
 * ItemManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class ItemManager extends AbstractManager {

    /**
     * @var app config
     */
    public $config;

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
        $this->config = $config;
        $this->args = $args;
        $this->mapper = ItemMapper::getInstance();
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

            self::$instance = new ItemManager($config, $args);
        }
        return self::$instance;
    }

    public function getItemsByIds($items_ids) {
        if (is_array($items_ids)) {
            $items_ids = implode(',', $items_ids);
        }
        return $this->mapper->getItemsByIds($items_ids);
    }

  
    public function getCompanyItems($companyId, $includeHiddens = false) {
        return $this->mapper->getCompanyItems($companyId, $includeHiddens);
    }

    public function resetCompanyItemsIndexes($companyId) {
        return $this->mapper->resetCompanyItemsIndexes($companyId);
    }

    public function hideCompanyItems($companyId) {
        return $this->mapper->hideCompanyItems($companyId);
    }

    public function increaseCompanyExpireItemsByGivenDays($companyId, $days, $categories_ids_array = null) {
        if (!isset($categories_ids_array)) {
            $categories_ids_array = array();
        }
        return $this->mapper->increaseItemsExpireDateByGivenDaysAndCategories($companyId, $days, $categories_ids_array);
    }

    public function UpdateAllAmdItemsPrices() {
        $allItemsWithDealerAmdOrVatAmdPrices = $this->getAllItemsWithDealerAmdOrVatAmdPrices();
        foreach ($allItemsWithDealerAmdOrVatAmdPrices as $item) {
            $dealerPriceAmd = intval($item->getDealerPriceAmd());
            if ($dealerPriceAmd > 0) {
                $dealerPrice = $this->exchangeFromAMDToUSD($dealerPriceAmd);
                $this->updateTextField($item->getId(), 'dealer_price', $dealerPrice);
            }
            $vatPriceAmd = intval($item->getVatPriceAmd());
            if ($vatPriceAmd > 0) {
                $vatPrice = $this->exchangeFromAMDToUSD($vatPriceAmd);
                $this->updateTextField($item->getId(), 'vat_price', $vatPrice);
            }
        }
    }

    public function getAllItemsWithDealerAmdOrVatAmdPrices() {
        return $this->mapper->getAllItemsWithDealerAmdOrVatAmdPrices();
    }

    public function addItem($displayName, $shortDescription, $fullDescription, $warranty, $dealerPrice, $vatPrice, $dealerPriceAmd, $vatPriceAmd, $companyID, $item_model, $item_brand, $categories_ids, $item_available_till_date, $item_price_sort_index = 0, $createdByEmail) {
        $item = $this->mapper->createDto();
        $item->setDisplayName($displayName);
        $item->setShortDescription($shortDescription);
        $item->setFullDescription($fullDescription);
        $item->setWarranty($warranty);
        $item->setDealerPrice($dealerPrice);
        $item->setVatPrice($vatPrice);
        $item->setDealerPriceAmd($dealerPriceAmd);
        $item->setVatPriceAmd($vatPriceAmd);
        $item->setCompanyId($companyID);
        $item->setHidden(0);
        $item->setModel($item_model);
        $item->setBrand($item_brand);


        $categoryManager = CategoryManager::getInstance($this->config, $this->args);
        $categoriesDtos = $categoryManager->getCategoriesByIds($categories_ids, true);
        $item_categories_names_array = array();
        foreach ($categoriesDtos as $catDto) {
            $item_categories_names_array[] = $catDto->getDisplayName();
        }
        $item_categories_names = ',' . implode(',', $item_categories_names_array) . ',';

        $item->setCategoriesNames($item_categories_names);
        $item->setCategoriesIds(',' . implode(',', $categories_ids) . ',');
        $item->setItemAvailableTillDate($item_available_till_date);
        $item->setOrderIndexInPrice($item_price_sort_index);
        $item->setCreatedDate(date('Y-m-d H:i:s'));
        $item->setCreatedByEmail($createdByEmail);
        $itemId = $this->mapper->insertDto($item);

        //calculate fake list price
        $itemForOrderDto = $this->getItemWithCustomerPrice($itemId);
        $customerItemPriceAmd = $this->exchangeFromUsdToAMD($itemForOrderDto->getCustomerItemPrice());
        $itemRandomDiscountPercent = rand(20, 28);
        $itemRandomDiscountParam = 1 - $itemRandomDiscountPercent / 100;
        $itemListPrice = intval($customerItemPriceAmd / $itemRandomDiscountParam);
        $this->updateNumericField($itemId, "list_price_amd", $itemListPrice);
        return $itemId;
    }

    public function copyItem($copied_item_id, $company_id, $item_position) {
        $itemDto = $this->selectByPK($copied_item_id);
        assert(isset($itemDto));
        $itemDto->setId(0);
        $itemDto->setCompanyId($company_id);
        $itemDto->setOrderIndexInPrice($item_position);
        $itemDto->setCreatedDate(date('Y-m-d H:i:s'));
        $itemDto->setUpdatedDate('0000-00-00 00:00:00');
        $pastedItemId = $this->mapper->insertDto($itemDto);

        //copy pictures
        $itemPictureCount = $itemDto->getPicturesCount();
        $dir = DATA_DIR . "/images/items/";
        for ($i = 1; $i <= $itemPictureCount; $i++) {
            $p_30_30 = $dir . $copied_item_id . '_' . $i . '_30_30' . '.' . 'jpg';
            $np_30_30 = $dir . $pastedItemId . '_' . $i . '_30_30' . '.' . 'jpg';
            if (file_exists($p_30_30)) {
                copy($p_30_30, $np_30_30);
            }
            $p_60_60 = $dir . $copied_item_id . '_' . $i . '_60_60' . '.' . 'jpg';
            $np_60_60 = $dir . $pastedItemId . '_' . $i . '_60_60' . '.' . 'jpg';
            if (file_exists($p_60_60)) {
                copy($p_60_60, $np_60_60);
            }
            $p_150_150 = $dir . $copied_item_id . '_' . $i . '_150_150' . '.' . 'jpg';
            $np_150_150 = $dir . $pastedItemId . '_' . $i . '_150_150' . '.' . 'jpg';
            if (file_exists($p_150_150)) {
                copy($p_150_150, $np_150_150);
            }
            $p_400_400 = $dir . $copied_item_id . '_' . $i . '_400_400' . '.' . 'jpg';
            $np_400_400 = $dir . $pastedItemId . '_' . $i . '_400_400' . '.' . 'jpg';
            if (file_exists($p_400_400)) {
                copy($p_400_400, $np_400_400);
            }
            $p_800_800 = $dir . $copied_item_id . '_' . $i . '_800_800' . '.' . 'jpg';
            $np_800_800 = $dir . $pastedItemId . '_' . $i . '_800_800' . '.' . 'jpg';
            if (file_exists($p_800_800)) {
                copy($p_800_800, $np_800_800);
            }
        }
    }

    public function updateItem($editedItemId, $displayName, $shortDescription = null, $fullDescription = null, $warranty, $dealerPrice, $vatPrice, $dealerPriceAmd, $vatPriceAmd, $companyID, $item_model, $item_brand, $categories_ids = null, $item_available_till_date = null, $item_price_sort_index = 0, $updatedByEmail) {
        $item = $this->mapper->selectByPK($editedItemId);
        $item->setDisplayName($displayName);
        if (isset($shortDescription)) {
            $item->setShortDescription($shortDescription);
        }
        if (isset($fullDescription)) {
            $item->setFullDescription($fullDescription);
        }
        $item->setWarranty($warranty);
        $item->setDealerPrice($dealerPrice);
        $item->setVatPrice($vatPrice);
        $item->setDealerPriceAmd($dealerPriceAmd);
        $item->setVatPriceAmd($vatPriceAmd);
        $item->setCompanyId($companyID);
        $item->setModel($item_model);
        $item->setBrand($item_brand);

        $categoryManager = CategoryManager::getInstance($this->config, $this->args);
        if (isset($categories_ids)) {
            $categoriesDtos = $categoryManager->getCategoriesByIds($categories_ids, true);
            $item_categories_names_array = array();
            foreach ($categoriesDtos as $catDto) {
                $item_categories_names_array[] = $catDto->getDisplayName();
            }
            $item_categories_names = ',' . implode(',', $item_categories_names_array) . ',';
            $item->setCategoriesNames($item_categories_names);
            $item->setCategoriesIds(',' . implode(',', $categories_ids) . ',');
        }
        if (isset($item_available_till_date)) {
            $item->setItemAvailableTillDate($item_available_till_date);
        }
        $item->setOrderIndexInPrice($item_price_sort_index);

        $item->setUpdatedDate(date('Y-m-d H:i:s'));
        $item->setUpdatedByEmail($updatedByEmail);

        return $this->mapper->updateByPK($item);
    }

    public function setItemCategories($itemId, $categoriesIdsArray) {
        $categoryManager = CategoryManager::getInstance($this->config, $this->args);
        if (isset($categoriesIdsArray)) {
            $categoriesDtos = $categoryManager->getCategoriesByIds($categoriesIdsArray, true);
            $item_categories_names_array = array();
            foreach ($categoriesDtos as $catDto) {
                $item_categories_names_array[] = $catDto->getDisplayName();
            }
            $item_categories_names = ',' . implode(',', $item_categories_names_array) . ',';
            $this->updateTextField($itemId, 'categories_names', $item_categories_names);
            $this->updateTextField($itemId, 'categories_ids', ',' . implode(',', $categoriesIdsArray) . ',');
        }
    }

    public function setItemHidden($itemId, $hidden) {
        return $this->mapper->updateNumericField($itemId, "hidden", $hidden);
    }

    public function setItemTillDateAttribute($itemId, $itemTillDate) {
        $item = $this->mapper->selectByPK($itemId);
        assert($item);
        if ($item) {
            $item->setItemAvailableTillDate($itemTillDate);
            $this->mapper->updateByPK($item);
        }
    }

    public function changeItemHiddenAttributeValue($item_id, $item_hidden) {
        $item = $this->mapper->selectByPK($item_id);
        $item->setHidden($item_hidden);
        return $this->mapper->updateByPK($item);
    }

    public function changeItemDealerPrice($item_id, $dealer_price) {
        return $this->mapper->updateTextField($item_id, "dealer_price", $dealer_price);
    }

    public function changeItemVatPrice($item_id, $vat_price) {
        return $this->mapper->updateTextField($item_id, "vat_price", $vat_price);
    }

    public function changeItemDealerPriceAmd($item_id, $dealer_price_amd) {
        return $this->mapper->updateNumericField($item_id, "dealer_price_amd", $dealer_price_amd);
    }

    public function changeItemVatPriceAmd($item_id, $vat_price_amd) {
        return $this->mapper->updateNumericField($item_id, "vat_price_amd", $vat_price_amd);
    }

    public function changeItemDisplayNamePrice($item_id, $display_name) {
        return $this->mapper->updateTextField($item_id, "display_name", $display_name);
    }

    public function changeItemOrderIndexInPrice($item_id, $order_index) {
        return $this->mapper->updateTextField($item_id, "order_index_in_price", $order_index);
    }

    public function searchItemsByTitle($userId, $userLevel, $search_text, $companyId, $price_range_min, $price_range_max, $selected_category, $groupedProperties, $show_only_vat_items, $show_only_non_picture_items, $show_only_no_short_spec_items, $show_only_no_full_spec_items, $offset, $limit, $orderByFieldName, $countOnly = false) {
        $profitFormula = $this->getItemProfitFormula($userId, $userLevel);
        return $this->mapper->searchItemsByTitle($userId, $userLevel, $profitFormula, $search_text, $companyId, $price_range_min, $price_range_max, $selected_category, $groupedProperties, $show_only_vat_items, $show_only_non_picture_items, $show_only_no_short_spec_items, $show_only_no_full_spec_items, $offset, $limit, $orderByFieldName, $countOnly);
    }

    public function getItemProfitFormula($userId, $userLevel) {
        $vipCustomer = false;
        $userManager = UserManager::getInstance($this->config, $this->args);
        if ($userLevel === UserGroups::$USER) {
            $userDto = $userManager->selectByPK($userId);
            $vipCustomer = $userManager->isVipAndVipEnabled($userDto);
        }
        if ($userLevel === UserGroups::$ADMIN) {
            $adminManager = AdminManager::getInstance($this->config, $this->args);
            $adminDto = $adminManager->selectByPK($userId);
            $vipCustomer = $adminDto->getPriceGroup() === 'vip';
        }

        if ($vipCustomer) {
            $customer_items_price_formula = $this->getCmsVar("vip_customer_items_price_formula");
        } else {
            $customer_items_price_formula = $this->getCmsVar("customer_items_price_formula");
        }
        //$10->25%,$1000->8%
        $bottomItemPriceLimit = (float) (substr($customer_items_price_formula, 1));
        $topItemPriceLimit = (float) ( substr($customer_items_price_formula, strrpos($customer_items_price_formula, '$') + 1));
        $bottomItemPriceProfitRatio = (float) ( substr($customer_items_price_formula, strrpos($customer_items_price_formula, '>') + 1)) / 100;
        $topItemPriceProfitRatio = (float) ( substr($customer_items_price_formula, strpos($customer_items_price_formula, '>') + 1)) / 100;

        $discount_percent_for_all_items = $this->getCmsVar("discount_percent_for_all_items");

        return "IF (`items`.`%s`<$bottomItemPriceLimit,`items`.`%s`*(1+ $topItemPriceProfitRatio),IF (`items`.`%s`>$topItemPriceLimit,`items`.`%s`*(1+ $bottomItemPriceProfitRatio),			
			(1+ $topItemPriceProfitRatio + ($bottomItemPriceProfitRatio-$topItemPriceProfitRatio)/($topItemPriceLimit-$bottomItemPriceLimit)*`items`.`%s`)*`items`.`%s`
			))";
    }

    public function searchItemsByTitleRowsCount($user_id, $search_text, $companyId, $price_range_min, $price_range_max, $deepest_category, $groupedProperties, $show_only_vat_items, $show_only_non_picture_items, $show_only_no_short_spec_items, $show_only_no_full_spec_items) {
        return $this->searchItemsByTitle($user_id, null, $search_text, $companyId, $price_range_min, $price_range_max, $deepest_category, $groupedProperties, $show_only_vat_items, $show_only_non_picture_items, $show_only_no_short_spec_items, $show_only_no_full_spec_items, null, null, null, true);
    }

    public function getItemWithCustomerPrice($items_id) {
        $profitFormula = $this->getItemProfitFormula(0, UserGroups::$GUEST);
        return $this->mapper->getItemWithCustomerPrice($items_id, $profitFormula);
    }

    public function getItemsForOrder($items_ids, $user_id, $userLevel, $showOutOfDateItems = false) {

        if (is_array($items_ids)) {
            $items_ids = implode(',', $items_ids);
        }
        if (strlen(trim($items_ids)) == 0) {
            return null;
        }
        $profitFormula = $this->getItemProfitFormula($user_id, $userLevel);
        $itemsDtos = $this->mapper->getItemsForOrder($items_ids, $user_id, $userLevel, $profitFormula, $showOutOfDateItems);
        if ($itemsDtos && is_array($itemsDtos)) {
            $itemsDtos = $this->makeItemsDtosArrayCorrespondingGivenIdsArray($itemsDtos, $items_ids);
        }
        //TODO use mysql "union all" to get duplicated items and FIELD to sort them
        return $itemsDtos;
    }

    /**
     * if any id doesn't exist in dtos then return null
     */
    public function makeItemsDtosArrayCorrespondingGivenIdsArray($items_dtos, $item_ids) {
        if (!is_array($item_ids)) {
            $item_ids = explode(',', $item_ids);
        }
        $ret = array();
        foreach ($item_ids as $key => $id) {
            $dto = $this->findItemDtoByItemId($items_dtos, $id);
            if ($dto) {
                $ret[] = $dto;
            } else {
                $ret[] = null;
            }
        }
        return $ret;
    }

    public function findItemDtoByItemId($items_dtos, $searchedItemsId) {

        foreach ($items_dtos as $key => $item_dto) {
            if ($item_dto->getId() == $searchedItemsId) {
                return $item_dto;
            }
        }
        return null;
    }

    /**
     * exchange USD to AMD and round it to min 100 AMD
     */
    public function exchangeFromUsdToAMD($priceInUSD, $us_dollar_exchange = null, $roundUp = false) {

        if ($us_dollar_exchange == null) {
            $us_dollar_exchange = $this->getCmsVar("us_dollar_exchange");
        }

        $ret = intval($priceInUSD * $us_dollar_exchange);
        if ($roundUp) {
            $ret = intval($ret / 100) * 100 + 100;
        }
        return $ret;
    }

    /**
     * exchange AMD to USD and round it
     */
    public function exchangeFromAMDToUSD($priceInAMD, $us_dollar_exchange = null) {

        if ($us_dollar_exchange == null) {
            $us_dollar_exchange = $this->getCmsVar("us_dollar_exchange_down");
        }

        $ret = $priceInAMD / $us_dollar_exchange;
        return $ret;
    }

    /**
     * Chack is item getItemAvailableTillDate < currentDate then returns 0 else returns 1
     */
    public function isItemAvailable($itemDto) {
        return $itemDto->getItemAvailableTillDate() < date('Y-m-d') ? 0 : 1;
    }

    public function isItemVisibleAndInAvailableDate($itemDto) {
        return $itemDto->getHidden() == 0 && $itemDto->getItemAvailableTillDate() >= date('Y-m-d');
    }

    public function growItemShowsCountByOne($itemDto) {
        if ($itemDto) {
            $showsCount = (int) $itemDto->getShowsCount();
            $this->mapper->updateNumericField($itemDto->getId(), 'shows_count', $showsCount + 1);
        }
        return false;
    }

    /**
     *
     * Returns the item categories path till reach to first static category.
     * Returns format is following, [cat_id=>catDisplayName,...]
     */
    private function getItemCategoriesPath($itemDto) {
        $cat_ids = $itemDto->getCategoriesIds();

        $cat_ids_array = explode(',', trim($cat_ids, ','));

        $cat_names = $itemDto->getCategoriesNames();
        $cat_names_array = explode(',', trim($cat_names, ','));

        $categoryManager = CategoryManager::getInstance($this->config, $this->args);
        $catDtos = $categoryManager->getCategoriesByIds($cat_ids_array);

        $catDtosArray = $this->dtosToArrayKeyId($catDtos);

        $ret = array();
        $i = 0;
        foreach ($cat_ids_array as $key => $cid) {
            $ret[$cid] = $cat_names_array[$i];
            if (array_key_exists($cid, $catDtosArray) && $catDtosArray[$cid]->getLastClickable() === '1') {
                break;
            }
            $i++;
        }
        return $ret;
    }

    public function dtosToArrayKeyId($catDtos) {
        $ret = array();
        foreach ($catDtos as $key => $catDto) {
            $ret[$catDto->getId()] = $catDto;
        }
        return $ret;
    }

    /**
     *
     * getItemCategoriesPath function to string
     */
    public function getItemCategoriesPathToString($itemDto) {
        $path = $this->getItemCategoriesPath($itemDto);
        $ret = "";
        foreach ($path as $key => $value) {
            $ret .= $value . '->';
        }
        $ret = substr($ret, 0, strlen($ret) - 2);
        return $ret;
    }

    public function getItemRootCategoryId($itemDto) {
        $categoriesIds = $itemDto->getCategoriesIds();
        $categoriesIds = trim($categoriesIds, ',');
        $categoriesIdsArray = explode(',', $categoriesIds);
        return $categoriesIdsArray [0];
    }

    /**
     *
     * returns [   catname1=>[subcat1, subcat2,...],catname2=>[subcat1, subcat2,...]   ]
     */
    public function getItemProperties($item_id) {
        $categoryHierarchyManager = CategoryHierarchyManager::getInstance($this->config, $this->args);
        $ids_after_last_clickable = $this->getItemCategoriesArrayUnderLastClickableCategory($item_id);
        $cathDtos = $categoryHierarchyManager->getCategoriesByCatsAndChildsIds($ids_after_last_clickable, $ids_after_last_clickable);
        $catNames = $this->getCategoriesNamesArrayByIdsArray($ids_after_last_clickable);

        $ret = array();
        $pid = null;
        foreach ($cathDtos as $i => $cathDto) {
            if ($pid != $cathDto->getCategoryId()) {
                $ret[$catNames[$cathDto->getCategoryId()]] = array();
                $ret[$catNames[$cathDto->getCategoryId()]][] = $catNames[$cathDto->getChildId()];
                $pid = $cathDto->getCategoryId();
            } else {
                $ret[$catNames[$cathDto->getCategoryId()]][] = $catNames[$cathDto->getChildId()];
            }
        }
        return $ret;
    }

    public function getCategoriesNamesArrayByIdsArray($ids) {
        $categoryManager = CategoryManager::getInstance($this->config, $this->args);
        $catDtos = $categoryManager->getCategoriesByIds($ids);
        $ret = array();
        foreach ($catDtos as $key => $dto) {
            $ret[$dto->getId()] = $dto->getDisplayName();
        }
        return $ret;
    }

    public function getItemCategoriesArrayUnderLastClickableCategory($item_id) {
        $itemDto = $this->selectByPK($item_id);
        $cat_ids = $itemDto->getCategoriesIds();
        $cat_ids_array = explode(',', substr($cat_ids, 1, strlen($cat_ids) - 2));
        $categoryManager = CategoryManager::getInstance($this->config, $this->args);
        $lastClickableDto = $categoryManager->getLastClickableCategoryFromCatIds($cat_ids_array);
        if ($lastClickableDto) {
            $lastClickableID = $lastClickableDto->getId();
            assert(in_array($lastClickableID, $cat_ids_array));
            $lcp = array_search($lastClickableID, $cat_ids_array);
            $ret = array_slice($cat_ids_array, $lcp + 1);
            return $ret;
        } else {
            return array();
        }
    }

    public function getPccItemsByCategoryFormula($user_id, $userLevel, $requiredCategoriesFormulasArray, $neededCategoriesIdsAndOrFormulaArray, $offset, $limit, $selected_items_array = null, $search_text = null) {

        $profitFormula = $this->getItemProfitFormula($user_id, $userLevel);
        if (isset($selected_items_array)) {
            if (is_array($selected_items_array)) {
                $selected_items_sql_array_str = '(' . implode(',', $selected_items_array) . ')';
            } else {
                $selected_items_sql_array_str = '(' . $selected_items_array . ')';
            }
        }
        return $this->mapper->getPccItemsByCategoryFormula($user_id, $userLevel, $profitFormula, $requiredCategoriesFormulasArray, $neededCategoriesIdsAndOrFormulaArray, $offset, $limit, $selected_items_sql_array_str, false, $search_text);
    }

    public function getPccItemsByCategoryFormulaCount($requiredCategoriesFormulasArray, $search_text) {
        return $this->mapper->getPccItemsByCategoryFormulaCount($requiredCategoriesFormulasArray, false, $search_text);
    }

    /**
     * $items_ids should be string represented item ids joined by comma (example "3,7,34,67") OR
     * array represented items ids.
     * Returns array
     */
    public function getNotAvailableItemsIds($item_ids) {
        if (is_array($item_ids)) {
            $item_ids = implode(',', $item_ids);
        }
        $availableItemsDtos = $this->getItemsByIds($item_ids);
        $ret = array();
        $item_ids = explode(',', $item_ids);
        foreach ($item_ids as $key => $id) {
            $itemDto = $this->findItemDtoByItemId($availableItemsDtos, $id);
            if (!$itemDto) {
                $ret[] = $itemDto->getId();
            }
        }

        return $ret;
    }

    /**
     * combine the same ids in subarrays
     * Returns array containing the same ids as given ids, but the same ids combined in the subarrays
     * returns format is this, array(id1, id2, array(id3, id3), ....  )
     */
    public function combineSameIdsInArray($ids_array) {
        $ret = array();
        foreach ($ids_array as $key => $id) {
            if (!isset($ret[$id])) {
                $ret[$id] = $id;
            } else {
                if (!is_array($ret[$id])) {
                    $ret[$id] = array($id, $id);
                } else {
                    $ret[$id][] = $id;
                }
            }
        }
        return $ret;
    }

    public function getCombinedItemDtosFromCombinedItemsIds($combined_items_ids, $user) {

        $itemsDtosCombined = array();
        foreach ($combined_items_ids as $key => $item_id) {
            $itemCount = 1;
            if (is_array($item_id)) {
                $itemCount = count($item_id);
                $item_id = $item_id[0];
            }
            $itemDto = $this->getItemsForOrder($item_id, $user->getId(), $user->getLevel());
            if ($itemDto != null) {
                $itemsDtosCombined[] = array_fill(0, $itemCount, $itemDto);
            }
        }
        return $itemsDtosCombined;
    }

    /**
     * @param $item can be item object or itemID, if itemid given then default items_image will return always noimage
     * @param $picture_size_str can be 30_30, 60_60, 150_150, 400_400, 800_800
     */
    public function getItemImageURL($itemId, $itemCategoriesIds, $picture_size_str, $picture_number) {
        $fileName = IMAGE_ROOT_DIR . "/items/" . $itemId . '_' . $picture_number . "_" . $picture_size_str . ".jpg";
        if ($picture_size_str === '800_800' && !file_exists($fileName)) {
            $fileName = IMAGE_ROOT_DIR . "/items/" . $itemId . '_' . $picture_number . "_" . "400_400" . ".jpg";
        }
        if (file_exists($fileName)) {
            return HTTP_PROTOCOL . $_SERVER['HTTP_HOST'] . '/images/item_' . $picture_size_str . '/' . $itemId . '/' . $picture_number;
        } else {
            return $this->getItemDefaultImageByCategoriesIds($itemCategoriesIds, $picture_size_str);
        }
    }

    public function getItemDefaultImageByCategoriesIds($itemCategoriesIds, $picture_size_str) {
        require_once (CLASSES_PATH . "/managers/CategoriesDefaultPicturesManager.class.php");
        $categoriesDefaultPicturesManager = CategoriesDefaultPicturesManager::getInstance($this->config, $this->args);
        $pre = HTTP_PROTOCOL . $_SERVER['HTTP_HOST'];
        if ($picture_size_str === '800_800' || $picture_size_str === '400_400') {
            return '/img/items_default_images/no_image_' . $picture_size_str . '.png';
        }
        $pictureFileName = $categoriesDefaultPicturesManager->getItemDefaultPictureByCategoriesIds($itemCategoriesIds);
        if (isset($pictureFileName)) {
            return '/img/items_default_images/' . $pictureFileName . '_' . $picture_size_str . '.png';
        }
        return '/img/items_default_images/no_image_' . $picture_size_str . '.png';
    }

    public function putItemsDtosInArrayWithItemIdInArrayKey($itemsDtos) {
        $ret = array();
        foreach ($itemsDtos as $key => $itemDto) {
            $ret[$itemDto->getId()] = $itemDto;
        }
        return $ret;
    }

    public function setItemDefaultPicture($itemId, $pictureIndex) {
        $dir = DATA_DIR . "/images/items/";
        $defPicture[1] = $dir . $itemId . '_1_30_30.jpg';
        $defPicture[2] = $dir . $itemId . '_1_60_60.jpg';
        $defPicture[3] = $dir . $itemId . '_1_150_150.jpg';
        $defPicture[4] = $dir . $itemId . '_1_400_400.jpg';
        $defPicture[5] = $dir . $itemId . '_1_800_800.jpg';

        $p[1] = $dir . $itemId . '_' . $pictureIndex . '_30_30.jpg';
        $p[2] = $dir . $itemId . '_' . $pictureIndex . '_60_60.jpg';
        $p[3] = $dir . $itemId . '_' . $pictureIndex . '_150_150.jpg';
        $p[4] = $dir . $itemId . '_' . $pictureIndex . '_400_400.jpg';
        $p[5] = $dir . $itemId . '_' . $pictureIndex . '_800_800.jpg';

        $tmpFilePath = DATA_DIR . "/temp/temp_picture.jpg";
        for ($i = 1; $i <= 5; $i++) {
            if (file_exists($defPicture[$i]) && file_exists($p[$i])) {
                rename($defPicture[$i], $tmpFilePath);
                rename($p[$i], $defPicture[$i]); // replace the file1
                rename($tmpFilePath, $p[$i]); // save the temporary file2 as the file2
            }
        }
        if (file_exists($tmpFilePath)) {
            unlink($tmpFilePath);
        }
        $this->setItemImage1AndImage2FromPicturesFile($itemId);
    }

    public function deleteItemWithPictures($itemId) {
        $this->deleteItemPictures($itemId);
        $this->deleteByPK($itemId);
    }

    public function deleteItemPictures($itemId) {
        $itemDto = $this->selectByPK($itemId);
        $picturesCount = $itemDto->getPicturesCount();
        $dir = DATA_DIR . "/images/items/";
        for ($i = 1; $i < $picturesCount + 1; $i++) {
            $picFullName_30_30 = $dir . $itemId . '_' . $i . '_30_30' . '.' . 'jpg';
            $picFullName_60_60 = $dir . $itemId . '_' . $i . '_60_60' . '.' . 'jpg';
            $picFullName_150_150 = $dir . $itemId . '_' . $i . '_150_150' . '.' . 'jpg';
            $picFullName_400_400 = $dir . $itemId . '_' . $i . '_400_400' . '.' . 'jpg';
            $picFullName_800_800 = $dir . $itemId . '_' . $i . '_800_800' . '.' . 'jpg';
            if (is_file($picFullName_30_30)) {
                unlink($picFullName_30_30);
            }
            if (is_file($picFullName_60_60)) {
                unlink($picFullName_60_60);
            }
            if (is_file($picFullName_150_150)) {
                unlink($picFullName_150_150);
            }
            if (is_file($picFullName_400_400)) {
                unlink($picFullName_400_400);
            }
            if (is_file($picFullName_800_800)) {
                unlink($picFullName_800_800);
            }
        }
        return $this->setItemPicturesCount($itemId, 0);
    }

    public function removeItemPicturesByPicturesIndexes($itemId, $picturesIndexes) {
        if (!is_array($picturesIndexes)) {
            $picturesIndexes = array($picturesIndexes);
        }
        $itemDto = $this->selectByPK($itemId);
        $picsCount = $itemDto->getPicturesCount($itemId);
        if (!empty($picturesIndexes)) {
            $dir = DATA_DIR . "/images/items/";
            asort($picturesIndexes, SORT_NUMERIC);
            foreach ($picturesIndexes as $key => $pictureIndex) {
                $picFullName_30_30 = $dir . $itemId . '_' . $pictureIndex . '_30_30' . '.' . 'jpg';
                $picFullName_60_60 = $dir . $itemId . '_' . $pictureIndex . '_60_60' . '.' . 'jpg';
                $picFullName_150_150 = $dir . $itemId . '_' . $pictureIndex . '_150_150' . '.' . 'jpg';
                $picFullName_400_400 = $dir . $itemId . '_' . $pictureIndex . '_400_400' . '.' . 'jpg';
                $picFullName_800_800 = $dir . $itemId . '_' . $pictureIndex . '_800_800' . '.' . 'jpg';
                if (is_file($picFullName_30_30)) {
                    unlink($picFullName_30_30);
                }
                if (is_file($picFullName_60_60)) {
                    unlink($picFullName_60_60);
                }
                if (is_file($picFullName_150_150)) {
                    unlink($picFullName_150_150);
                }
                if (is_file($picFullName_400_400)) {
                    unlink($picFullName_400_400);
                }
                if (is_file($picFullName_800_800)) {
                    unlink($picFullName_800_800);
                }
            }
            //renaming the rest pictures ids to be successively
            //$removedPicturesCount = count($picturesIndexes);
            //$picsCount = (int)$picsCount - (int)$removedPicturesCount;
            $startPicId = 1;
            for ($i = 1; $i < $picsCount + 1; $i++) {
                $picName_30_30 = $itemId . '_' . $i . '_30_30' . '.' . 'jpg';
                $picName_60_60 = $itemId . '_' . $i . '_60_60' . '.' . 'jpg';
                $picName_150_150 = $itemId . '_' . $i . '_150_150' . '.' . 'jpg';
                $picName_400_400 = $itemId . '_' . $i . '_400_400' . '.' . 'jpg';
                $picName_800_800 = $itemId . '_' . $i . '_800_800' . '.' . 'jpg';
                if (is_file($dir . $picName_30_30)) {
                    $this->renamePictureNameToCorrespondingGivenIndex($dir, $picName_30_30, $startPicId);
                    $this->renamePictureNameToCorrespondingGivenIndex($dir, $picName_60_60, $startPicId);
                    $this->renamePictureNameToCorrespondingGivenIndex($dir, $picName_150_150, $startPicId);
                    $this->renamePictureNameToCorrespondingGivenIndex($dir, $picName_400_400, $startPicId);
                    $this->renamePictureNameToCorrespondingGivenIndex($dir, $picName_800_800, $startPicId);
                    $startPicId++;
                }
            }
        }
        $pCount = intval($picsCount) - count($picturesIndexes);
        $this->setItemImage1AndImage2FromPicturesFile($itemId);

        return $this->setItemPicturesCount($itemId, $pCount);
    }

    private function setItemImage1AndImage2FromPicturesFile($itemId) {
        $dir = DATA_DIR . "/images/items/";
        $p1 = $dir . $itemId . '_1_30_30.jpg';
        $p2 = $dir . $itemId . '_1_60_60.jpg';
        if (file_exists($p1)) {
            $this->updateTextField($itemId, 'image1', base64_encode(file_get_contents($p1)));
        } else {
            $this->updateTextField($itemId, 'image1', '');
        }
        if (file_exists($p2)) {
            $this->updateTextField($itemId, 'image2', base64_encode(file_get_contents($p2)));
        } else {
            $this->updateTextField($itemId, 'image2', '');
        }
        return true;
    }

    public function setItemPicturesCount($itemId, $picturesCount) {
        return $this->updateNumericField($itemId, 'pictures_count', $picturesCount);
    }

    public function renamePictureNameToCorrespondingGivenIndex($dir, $pictureName, $pictureIndex) {
        assert($pictureName && strlen($pictureName) > 0 && strpos($pictureName, '_') !== false);
        assert(is_file($dir . $pictureName));
        $firstUnderscorePos = strpos($pictureName, '_');
        $pn = substr($pictureName, $firstUnderscorePos + 1);
        $secondUnderscorePos = strpos($pn, '_');
        $filePictureId = (int) substr($pn, 0, $secondUnderscorePos);
        $newPictureName = str_replace('_' . $filePictureId . '_', '_' . $pictureIndex . '_', $pictureName);
        if ($filePictureId != $pictureIndex) {
            assert($filePictureId > $pictureIndex);
            rename($dir . $pictureName, $dir . $newPictureName);
        }
    }

    public function findSimillarItems($searchText, $limit) {
        $ret = $this->mapper->getSimillarItems($searchText, $limit);
        return $ret;
    }

    public function copyItemPictures($sourceItemDto, $targetItemDto) {
        $sourceItemPicturesCount = $sourceItemDto->getPicturesCount();
        $sourceItemId = $sourceItemDto->getId();
        $targetItemId = $targetItemDto->getId();
        $this->deleteItemPictures($targetItemId);
        $this->updateNumericField($targetItemId, "pictures_count", $sourceItemPicturesCount);
        $this->updateTextField($targetItemId, "image1", $sourceItemDto->getImage1());
        $this->updateTextField($targetItemId, "image2", $sourceItemDto->getImage2());

        $dir = DATA_DIR . "/images/items/";
        for ($i = 1; $i < $sourceItemPicturesCount + 1; $i++) {
            $sourcePicFullName_30_30 = $dir . $sourceItemId . '_' . $i . '_30_30' . '.' . 'jpg';
            $sourcePicFullName_60_60 = $dir . $sourceItemId . '_' . $i . '_60_60' . '.' . 'jpg';
            $sourcePicFullName_150_150 = $dir . $sourceItemId . '_' . $i . '_150_150' . '.' . 'jpg';
            $sourcePicFullName_400_400 = $dir . $sourceItemId . '_' . $i . '_400_400' . '.' . 'jpg';
            $sourcePicFullName_800_800 = $dir . $sourceItemId . '_' . $i . '_800_480' . '.' . 'jpg';
            if (is_file($sourcePicFullName_30_30)) {
                $targetPicFullName_30_30 = $dir . $targetItemId . '_' . $i . '_30_30' . '.' . 'jpg';
                copy($sourcePicFullName_30_30, $targetPicFullName_30_30);
            }
            if (is_file($sourcePicFullName_60_60)) {
                $targetPicFullName_60_60 = $dir . $targetItemId . '_' . $i . '_60_60' . '.' . 'jpg';
                copy($sourcePicFullName_60_60, $targetPicFullName_60_60);
            }
            if (is_file($sourcePicFullName_150_150)) {
                $targetPicFullName_150_150 = $dir . $targetItemId . '_' . $i . '_150_150' . '.' . 'jpg';
                copy($sourcePicFullName_150_150, $targetPicFullName_150_150);
            }
            if (is_file($sourcePicFullName_400_400)) {
                $targetPicFullName_400_400 = $dir . $targetItemId . '_' . $i . '_400_400' . '.' . 'jpg';
                copy($sourcePicFullName_400_400, $targetPicFullName_400_400);
            }
            if (is_file($sourcePicFullName_800_800)) {
                $targetPicFullName_800_800 = $dir . $targetItemId . '_' . $i . '_800_800' . '.' . 'jpg';
                copy($sourcePicFullName_800_800, $targetPicFullName_800_800);
            }
        }
    }

    public function getItemsByAdminConditions($selectedCompanyId, $includeHiddens = false, $emptyModel = true, $emptyShortSpec = true, $emptyFullSpec = true, $picturesCount = 'any') {
        if ($picturesCount === 'any') {
            $picturesCount = -1;
        }
        return $this->mapper->getItemsByAdminConditions($selectedCompanyId, $includeHiddens, $emptyModel, $emptyShortSpec, $emptyFullSpec, $picturesCount);
    }

    public function getAllItemsTitleAndModelAndBrandMappedArray() {
        $dtos = $this->mapper->getAllItemsTitleAndModelAndBrand();
        $ret = array();
        foreach ($dtos as $dto) {
            $ret[$dto->getId()] = array($dto->getDisplayName(), $dto->getModel(), $dto->getBrand(), $dto->getCompanyName(), $dto->getPicturesCount());
        }
        return $ret;
    }

    public function copyItemFullDescription($sourceItemDto, $targetItemDto) {
        if (!isset($sourceItemDto) || !isset($targetItemDto)) {
            return false;
        }
        $this->updateTextField($targetItemDto->getId(), 'full_description', $sourceItemDto->getFullDescription());
        return true;
    }

    public function copyItemShortDescription($sourceItemDto, $targetItemDto) {
        if (!isset($sourceItemDto) || !isset($targetItemDto)) {
            return false;
        }
        $this->updateTextField($targetItemDto->getId(), 'short_description', $sourceItemDto->getShortDescription());
        return true;
    }

    public function copyItemModel($sourceItemDto, $targetItemDto) {
        if (!isset($sourceItemDto) || !isset($targetItemDto)) {
            return false;
        }
        $this->updateTextField($targetItemDto->getId(), 'model', $sourceItemDto->getModel());
        return true;
    }

    public function addPicture($itemDto, $fileName = 'item_picture', $already_uploaded_file = null) {
        $itemId = $itemDto->getId();
        ////////////////////////////
        $originalPictureFullName = null;

        if (!isset($already_uploaded_file)) {
            $file_name = $_FILES[$fileName]['name'];
            $tmp_name = $_FILES[$fileName]['tmp_name'];
            $file_size = $_FILES[$fileName]['size'];
        } else {
            $file_name = $already_uploaded_file;
        }

        if (isset($file_size) && ($file_size == 0 || $file_size > 5 * 1024 * 1024)) {
            return "Maximum file size can be 5MB";
        }

        $file_name_parts = explode('.', $file_name);
        $pictureExt = end($file_name_parts);
        $supported_file_formats = array('jpg', 'png', 'gif', 'jpeg');
        if (!in_array(strtolower($pictureExt), $supported_file_formats)) {
            return "Not supported file format!";
        }

        $dir = DATA_DIR . "/images/";
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        $dir = DATA_DIR . "/images/items/";
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }

        $picsCount = $itemDto->getPicturesCount();
        $currentPicIndex = $picsCount + 1;
        $originalPictureFullName = $dir . $itemId . '_' . $currentPicIndex . '_original' . '.' . 'jpg';
        $resizedPictureFullName_30_30 = $dir . $itemId . '_' . $currentPicIndex . '_30_30' . '.' . 'jpg';
        $resizedPictureFullName_60_60 = $dir . $itemId . '_' . $currentPicIndex . '_60_60' . '.' . 'jpg';
        $resizedPictureFullName_150_150 = $dir . $itemId . '_' . $currentPicIndex . '_150_150' . '.' . 'jpg';
        $resizedPictureFullName_400_400 = $dir . $itemId . '_' . $currentPicIndex . '_400_400' . '.' . 'jpg';
        $resizedPictureFullName_800_800 = $dir . $itemId . '_' . $currentPicIndex . '_800_800' . '.' . 'jpg';
        if (!isset($already_uploaded_file)) {
            move_uploaded_file($tmp_name, $originalPictureFullName);
        } else {
            $originalPictureFullName = $already_uploaded_file;
        }

        //resize image
        $p1 = resizeImageToGivenType($originalPictureFullName, $resizedPictureFullName_30_30, 30, 30, 'jpg');
        $p2 = resizeImageToGivenType($originalPictureFullName, $resizedPictureFullName_60_60, 60, 60, 'jpg');
        $p3 = resizeImageToGivenType($originalPictureFullName, $resizedPictureFullName_150_150, 150, 150, 'jpg');
        $p4 = resizeImageToGivenType($originalPictureFullName, $resizedPictureFullName_400_400, 400, 400, 'jpg');
        $p5 = resizeImageToGivenType($originalPictureFullName, $resizedPictureFullName_800_800, 800, 800, 'jpg');

        if ($p1 === false || $p2 === false || $p3 === false || $p4 === false || $p5 === false) {

            if (is_file($resizedPictureFullName_30_30)) {
                unlink($resizedPictureFullName_30_30);
            }
            if (is_file($resizedPictureFullName_60_60)) {
                unlink($resizedPictureFullName_60_60);
            }
            if (is_file($resizedPictureFullName_150_150)) {
                unlink($resizedPictureFullName_150_150);
            }
            if (is_file($resizedPictureFullName_400_400)) {
                unlink($resizedPictureFullName_400_400);
            }
            if (is_file($resizedPictureFullName_800_800)) {
                unlink($resizedPictureFullName_800_800);
            }
            return "Error resizing image!";
        }

        if (is_file($originalPictureFullName)) {
            unlink($originalPictureFullName);
        }
        $this->setItemPicturesCount($itemId, $picsCount + 1);
        $this->setItemImage1AndImage2FromPicturesFile($itemId);
        return true;
    }
    
    public function deleteOldHiddenItemsByMonthsNumber($monthsNumber)
    {
        $itemsDtos = $this->mapper->getHiddenItemsByMonthsNumber($monthsNumber);
        foreach ($itemsDtos as $itemDto) {
                $this->deleteItemWithPictures($itemDto->getId());
        }
        return count($itemsDtos);
    }

}

?>