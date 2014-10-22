<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/ItemDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class ItemMapper extends AbstractMapper {

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
        $this->tableName = "items";
    }

    /**
     * Returns an singleton instance of this class
     * @return
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new ItemMapper();
        }
        return self::$instance;
    }

    /**
     * @see AbstractMapper::createDto()
     */
    public function createDto() {
        return new ItemDto();
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

    public static $GET_COMPANY_ITEMS = "SELECT * FROM `%s` WHERE company_id = %d %s ORDER BY `hidden`, `order_index_in_price`, `categories_names`";

    public function getCompanyItems($companyId, $includeHiddens) {
        $includeHiddensCommand = "";
        if ($includeHiddens === false) {
            $includeHiddensCommand = "AND `hidden` = 0";
        }
        $sqlQuery = sprintf(self::$GET_COMPANY_ITEMS, $this->getTableName(), $companyId, $includeHiddensCommand);
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

    public static $RESET_COMPANY_ITEMS_INDEXES = "UPDATE `%s` SET `order_index_in_price`=0 WHERE company_id = %d";

    public function resetCompanyItemsIndexes($companyId) {
        $sqlQuery = sprintf(self::$RESET_COMPANY_ITEMS_INDEXES, $this->getTableName(), $companyId);
        $this->dbms->query($sqlQuery);
    }

    public static $HIDE_COMPANY_ITEMS = "UPDATE `%s` SET `hidden`=1 WHERE company_id = %d";

    public function hideCompanyItems($companyId) {
        $sqlQuery = sprintf(self::$HIDE_COMPANY_ITEMS, $this->getTableName(), $companyId);
        $this->dbms->query($sqlQuery);
    }

    public static $INCREASE_COMPANY_EXPIRE_ITEMS = "UPDATE `%s` SET `item_available_till_date` = '%s' + INTERVAL %d DAY, `updated_date` = '%s'
																									 WHERE `company_id` = %d AND `hidden` = 0 %s";

    public function increaseItemsExpireDateByGivenDaysAndCategories($companyId, $days, $categories_ids_array) {
        assert(is_array($categories_ids_array));
        $q = "";
        if (!empty($categories_ids_array)) {
            $q = " AND (";
            foreach ($categories_ids_array as $key => $cid) {
                $q .= "`categories_ids` LIKE '%," . $cid . ",%' or";
            }
            $q = substr($q, 0, strlen($q) - 2) . ')';
        }
        $sqlQuery = sprintf(self::$INCREASE_COMPANY_EXPIRE_ITEMS, $this->getTableName(), date('Y-m-d'), $days, date('Y-m-d H:i:s'), $companyId, $q);
        $result = $this->dbms->query($sqlQuery);
        return $result;
    }

    public static $GET_ALL_ITEMS_WITH_AMD_PRICES = "SELECT `id`, `dealer_price_amd`, `vat_price_amd` FROM `%s` WHERE `dealer_price_amd`>0 OR `vat_price_amd`>0";

    public function getAllItemsWithDealerAmdOrVatAmdPrices() {
        $sqlQuery = sprintf(self::$GET_ALL_ITEMS_WITH_AMD_PRICES, $this->getTableName());
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

    /**
     * This is the main function that is used in system item search tab.
     * @param $show_only_vat_items can be 0 or 1
     */
    public static $SEARCH_ITEMS_BY_TITLE = "SELECT  `items`.`id`,
                                            `items`.`display_name`,
                                            `items`.`warranty`, `items`.`dealer_price`, `items`.`vat_price`,
                                            `items`.`dealer_price_amd`, `items`.`vat_price_amd`,
                                            `items`.`company_id`,
                                            `items`.`list_price_amd`,
                                            `items`.`hidden`,
                                            `items`.`model`,
                                            `items`.`brand`,
                                            `items`.`categories_ids`,
                                            `items`.`item_available_till_date`,
                                            `items`.`pictures_count`,
                                            `items`.`created_date`,
                                            `items`.`updated_date`,
                                            `items`.`image2`,
                                            %s
                                            `companies`.`name` as `company_name`, `companies`.`rating` as `company_rating`,
                                            GROUP_CONCAT(`company_branches`.`phones`) as `company_phones`,
                                            %s
                                            `online_users`.`email` = `online_users`.`email` AS `is_company_online`,
                                            %s 
                                            FROM `%s` 
                                            INNER JOIN `companies` ON `company_id` = `companies`.`id` AND `companies`.`hidden`=0 AND `companies`.`passive`=0
                                            LEFT JOIN `company_branches` ON `company_branches`.`company_id` = `companies`.`id`
                                            %s
                                            LEFT JOIN `online_users` ON `companies`.`email` = `online_users`.`email`
                                            WHERE %s %s %s %s %s %s %s `items`.`hidden`=0  GROUP BY `items`.`id` %s %s %s";

    public function searchItemsByTitle($user_id, $userLevel, $profitFormula, $search_text, $companyId, $price_range_min, $price_range_max, $selectedCategory, $groupedProperties, $show_only_vat_items, $show_only_non_picture_items, $show_only_no_short_spec_items, $show_only_no_full_spec_items, $offset, $limit, $orderByFieldName, $countOnly = false) {

        $join_company_dealers_table_for_user_only = "";
        $add_is_dealer_of_this_company = "";
        if (!$countOnly) {
            if ($userLevel == UserGroups::$USER) {
                $join_company_dealers_table_for_user_only = "LEFT JOIN  `company_dealers` ON `company_dealers`.`user_id`= %d AND `company_dealers`.`company_id` = `companies`.`id`";
                $join_company_dealers_table_for_user_only = sprintf($join_company_dealers_table_for_user_only, $user_id);
                $add_is_dealer_of_this_company = " IF (`company_dealers`.`user_id`, 1,0) AS `is_dealer_of_this_company`, ";
            } elseif ($userLevel == UserGroups::$GUEST) {
                $add_is_dealer_of_this_company = " 0 AS `is_dealer_of_this_company`, ";
            } else {
                $add_is_dealer_of_this_company = " 1 AS `is_dealer_of_this_company`, ";
            }
        }
        $add_customer_price_field = " " . sprintf($profitFormula, 'dealer_price', 'dealer_price', 'dealer_price'
                        , 'dealer_price', 'dealer_price', 'dealer_price') . " AS `customer_item_price`, " . sprintf($profitFormula, 'vat_price', 'vat_price', 'vat_price'
                        , 'vat_price', 'vat_price', 'vat_price') . " AS `customer_vat_item_price` ";

        $sub_query_1 = "";
        if ($companyId > 0) {
            $sub_query_1 = "`items`.`company_id` = %d AND";
            $sub_query_1 = sprintf($sub_query_1, $companyId);
        }
        $sub_query_2 = "";
        if (isset($price_range_min) && strlen($price_range_min) > 0) {
            $sub_query_2 = "HAVING `customer_item_price` >= %d";
            $sub_query_2 = sprintf($sub_query_2, $price_range_min);
        }
        $sub_query_3 = "";
        if (isset($price_range_max) && strlen($price_range_max) > 0) {
            $sub_query_3 = "`customer_item_price` <= %d";
            $sub_query_3 = sprintf($sub_query_3, $price_range_max);
            if (strlen($sub_query_2) > 0) {
                $sub_query_3 = 'AND ' . $sub_query_3;
            } else {
                $sub_query_3 = 'HAVING ' . $sub_query_3;
            }
        }
        $sub_query_4 = "";
        if (!empty($groupedProperties)) {
            foreach ($groupedProperties as $key => $propertiesIds) {
                $groupQuery = " (";
                foreach ($propertiesIds as $key => $propertyId) {
                    $q = sprintf(" `items`.`categories_ids` LIKE '%s' OR", '%,' . $propertyId . ',%');
                    $groupQuery .= $q;
                }
                $groupQuery = substr($groupQuery, 0, strlen($groupQuery) - 2) . ') AND';
                $sub_query_4 .= $groupQuery;
            }
        } else {
            if ($selectedCategory) {
                $sub_query_4 = sprintf(" `items`.`categories_ids` LIKE '%s' AND", '%,' . $selectedCategory . ',%');
            }
        }

        $order_by_query = "";
        if (!$countOnly) {
            if (isset($orderByFieldName) && strlen($orderByFieldName) > 0) {
                $order_by_query = "ORDER BY `" . $orderByFieldName . "` , `shows_count` DESC";
            }
            if (empty($order_by_query) && !empty($search_text)) {
                $order_by_query = "ORDER BY (name_match  + model_match  +categories_names_match) DESC, `shows_count` DESC";
            }
            if (empty($order_by_query)) {
                $order_by_query = "ORDER BY `shows_count` DESC";
            }
        }
        $showOnlyNonPicturesItemsSubQuery = "";
        if ($show_only_non_picture_items == 1) {
            $showOnlyNonPicturesItemsSubQuery = "`items`.`pictures_count`=0 AND";
        }
        $showOnlyNoShortSpecItemsSubQuery = "";
        if ($show_only_no_short_spec_items == 1) {
            $showOnlyNoShortSpecItemsSubQuery = "NULLIF(`items`.`short_description`, '') IS NULL AND";
        }
        $showOnlyNoFullSpecItemsSubQuery = "";
        if ($show_only_no_full_spec_items == 1) {
            $showOnlyNoFullSpecItemsSubQuery = "NULLIF(`items`.`full_description`, '') IS NULL AND";
        }

        $only_vat_items_query = "";
        if ($show_only_vat_items == 1) {
            $only_vat_items_query = "`items`.`vat_price`>0 AND";
        }

        $text_serch_query_part1 = "";
        $text_serch_query_part2 = "";
        if (!empty($search_text)) {
            $search_text_formated_for_match = $this->formatSearchTextForUsingInMatch($search_text);

            if (!$countOnly) {
                $text_serch_query_part1 = sprintf("MATCH (`items`.display_name) AGAINST ('%s' IN BOOLEAN MODE)*5 AS name_match,
												   MATCH (`items`.model) AGAINST ('%s' IN BOOLEAN MODE)*3 AS model_match,
												   MATCH (`items`.categories_names) AGAINST ('%s' IN BOOLEAN MODE)*2 AS categories_names_match,"
                        , $search_text_formated_for_match, $search_text_formated_for_match, $search_text_formated_for_match);
            }


            $text_serch_query_part2 = sprintf(" (MATCH (display_name, model, categories_names) AGAINST ('%s' IN BOOLEAN MODE) OR 
				", $search_text_formated_for_match);
            $search_text_words = $this->formatSearchTextWords($search_text);
            $likeSql = "";
            foreach ($search_text_words as $word) {
                $likeSql .= "CONCAT(`items`.display_name, `items`.model, `items`.categories_names) LIKE '%" . $word . "%' AND ";
            }
            $likeSql = substr($likeSql, 0, -4);
            $text_serch_query_part2 .= ' (' . $likeSql . ')) AND ';
        }


        if (!$countOnly) {
            $sqlQuery = sprintf(self::$SEARCH_ITEMS_BY_TITLE, $text_serch_query_part1, $add_is_dealer_of_this_company, $add_customer_price_field, $this->getTableName(), $join_company_dealers_table_for_user_only, $text_serch_query_part2, $sub_query_1, $sub_query_4, $only_vat_items_query, $showOnlyNonPicturesItemsSubQuery, $showOnlyNoShortSpecItemsSubQuery, $showOnlyNoFullSpecItemsSubQuery, $sub_query_2, $sub_query_3, $order_by_query) . ' LIMIT ' . $offset . ', ' . $limit;
            $result = $this->fetchRows($sqlQuery);

            return $result;
        } else {
            $sqlQuery = sprintf(self::$SEARCH_ITEMS_BY_TITLE, $text_serch_query_part1, $add_is_dealer_of_this_company, $add_customer_price_field, $this->getTableName(), $join_company_dealers_table_for_user_only, $text_serch_query_part2, $sub_query_1, $sub_query_4, $only_vat_items_query, $showOnlyNonPicturesItemsSubQuery, $showOnlyNoShortSpecItemsSubQuery, $showOnlyNoFullSpecItemsSubQuery, $sub_query_2, $sub_query_3, $order_by_query);
            $baseQuery = sprintf("SELECT `items_table_alias`.`categories_ids` as `categories_ids`,
                    `items_table_alias`.`id` as `id`				
                    FROM (%s) as `items_table_alias`", $sqlQuery);

            return $this->fetchRows($baseQuery);
        }
    }

    public function getSimillarItems($searchText, $limit) {
        $searchReg = $this->formatSearchTextForUsingInRegexp($searchText);
        $sql = "SELECT `items`.id,`items`.display_name, `companies`.name as `company_name` from `%s` LEFT JOIN `companies` ON `companies`.id=`items`.`company_id` "
                . "WHERE CONCAT(`items`.display_name, `items`.model, `items`.categories_names) REGEXP '%s' LIMIT 0, %d";
        $sqlQuery = sprintf($sql, $this->getTableName(), $searchReg, $limit);
        // var_dump($sqlQuery);exit;
        return $this->fetchRows($sqlQuery);
    }

    public function getAllItemsTitleAndModelAndBrand() {
        $sql = "SELECT `items`.id,`items`.display_name,`items`.`model`,`items`.`brand`,`items`.`pictures_count`, `items`.`pictures_count`,"
                . "`companies`.name as `company_name` FROM `%s` LEFT JOIN `companies` ON `companies`.id=`items`.`company_id` ";
        $sqlQuery = sprintf($sql, $this->getTableName());
        return $this->fetchRows($sqlQuery);
    }

    public function getItemsByAdminConditions($selectedCompanyId, $includeHiddens, $emptyModel, $emptyShortSpec, $emptyFullSpec, $picturesCount) {
        $sql = "SELECT `items`.id, display_name, model, short_description, pictures_count FROM `%s` LEFT JOIN `companies` ON `companies`.id=`items`.`company_id` %s";
        $whereSql = "WHERE `company_id` = " . $selectedCompanyId;
        if ($includeHiddens == false) {
            $whereSql .= " AND `items`.`hidden`=0";
        }
        if ($emptyModel || $emptyShortSpec || $emptyFullSpec || $picturesCount >= 0) {
            $whereSql .= " AND (";
        }
        if ($emptyModel) {
            $whereSql .= " `model` IS NULL OR `model`='' OR";
        }
        if ($emptyShortSpec) {
            $whereSql .= " `short_description` IS NULL OR `short_description`=''  OR";
        }
        if ($emptyFullSpec) {
            $whereSql .= " `full_description` IS NULL OR `full_description`='' OR";
        }
        if ($picturesCount >= 0) {
            $whereSql .= " `pictures_count`=" . $picturesCount . ' OR';
        }
        if ($emptyModel || $emptyShortSpec || $emptyFullSpec || $picturesCount >= 0) {
            $whereSql = substr($whereSql, 0, -2);
            $whereSql .= ")";
        }
        $sqlQuery = sprintf($sql, $this->getTableName(), $whereSql);
        return $this->fetchRows($sqlQuery);
    }

    private function formatSearchTextForUsingInMatch($search_text) {
        $search_text = str_replace(array('-', '/', '\\'), " ", $search_text);
        $parts = preg_split('/\s+/', $search_text);
        $retParts = array();
        foreach ($parts as $p) {
            $retParts[] = '+' . $p . '*';
        }
        return implode(' ', $retParts);
    }

    private function formatSearchTextForUsingInRegexp($search_text) {
        $parts = str_replace(array('-', '/', '\\'), ' ', $search_text);
        $parts = preg_split('/\s+/', $search_text);
        return implode('.*', $parts);
    }

    private function formatSearchTextWords($search_text) {
        $parts = str_replace(array('-', '/', '\\'), ' ', $search_text);
        $parts = preg_split('/\s+/', $search_text);
        return $parts;
    }

    public function getItemWithCustomerPrice($items_id, $profitFormula) {
        $sql = "SELECT *, %s FROM items WHERE id=%d";
        $custPrice = " " . sprintf($profitFormula, 'dealer_price', 'dealer_price', 'dealer_price'
                        , 'dealer_price', 'dealer_price', 'dealer_price') . " AS `customer_item_price`, " . sprintf($profitFormula, 'vat_price', 'vat_price', 'vat_price'
                        , 'vat_price', 'vat_price', 'vat_price') . " AS `customer_vat_item_price` ";
        $sqlQuery = sprintf($sql, $custPrice, $items_id);
        $ret = $this->fetchRows($sqlQuery);
        if (count($ret) === 1) {
            return $ret[0];
        }
        return null;
    }

    /**
     * Returns items dtos, false if no any item available
     * if given items ids are repeat then it will retrun only one Dto for that repeated item in the result
     */
    public static $GET_ITEM_FOR_ORDER = "SELECT 
                                    `items`.*
                                    ,`companies`.`name` as `company_name`,  
                                    `companies`.`rating` as `company_rating`,							
                                                        GROUP_CONCAT(`company_branches`.`phones`) as `company_phones`,
                                            `online_users`.`email` = `online_users`.`email` AS `is_company_online`,
                                            %s 																					
                                            %s 
                                    FROM `%s` INNER JOIN `companies` ON `company_id` = `companies`.`id` AND `companies`.`hidden`=0 
                                                        LEFT JOIN `company_branches` ON `company_branches`.`company_id` = `companies`.`id`
                                      %s
                                      LEFT JOIN `online_users`	ON `companies`.`email` = `online_users`.`email`
                                      WHERE `items`.`id` in (%s) AND `items`.`hidden`=0 %s GROUP BY `items`.`id`";

    public function getItemsForOrder($items_ids, $user_id, $userLevel, $profitFormula, $showOutOfDateItems) {
        //TODO check for SQL injection
        $items_ids_count = substr_count($items_ids, ',') + 1;
        if ($items_ids_count === 0) {
            return null;
        }
        $join_company_dealers_table_for_user_only = "";
        $add_is_dealer_of_this_company = "";
        $add_customer_price_field = "";
        if ($userLevel == UserGroups::$USER) {
            $join_company_dealers_table_for_user_only = "LEFT JOIN  `company_dealers` ON `company_dealers`.`user_id`= %d AND `company_dealers`.`company_id` = `companies`.`id`";
            $join_company_dealers_table_for_user_only = sprintf($join_company_dealers_table_for_user_only, $user_id);
            $add_is_dealer_of_this_company = " IF (`company_dealers`.`user_id`, 1,0) AS `is_dealer_of_this_company`, ";
        } elseif ($userLevel == UserGroups::$GUEST) {
            $add_is_dealer_of_this_company = " 0 AS `is_dealer_of_this_company`, ";
        } else {
            $add_is_dealer_of_this_company = " 1 AS `is_dealer_of_this_company`, ";
        }

        $add_customer_price_field = " " . sprintf($profitFormula, 'dealer_price', 'dealer_price', 'dealer_price'
                        , 'dealer_price', 'dealer_price', 'dealer_price') . " AS `customer_item_price`, " . sprintf($profitFormula, 'vat_price', 'vat_price', 'vat_price'
                        , 'vat_price', 'vat_price', 'vat_price') . " AS `customer_vat_item_price` ";

        if ($showOutOfDateItems !== true) {
            $showOutOfDateItemsQuery = "AND item_available_till_date >= '" . date('Y-m-d') . "'";
        }
        $sqlQuery = sprintf(self::$GET_ITEM_FOR_ORDER, $add_is_dealer_of_this_company, $add_customer_price_field, $this->getTableName(), $join_company_dealers_table_for_user_only, $items_ids, $showOutOfDateItemsQuery);
        //var_dump($sqlQuery );exit;
        $result = $this->fetchRows($sqlQuery);
        if ($items_ids_count === 1 && count($result) === 1) {
            return $result[0];
        } else {
            if (count($result) > 1) {
                return $result;
            } else {
                return null;
            }
        }
    }

    /**
     * This is the main function that is used in system pc configurator tab.
     * $neededCategoriesIdsAndOrFormulaArray format is such this array('(', 1, 'and', 4,')','or', '8') it means '
     * $selected_items_sql_array_str format is this '(x,y,z,...)' where x,y and z are the items ids that should be order in the begining of the result
     */
    public static $GET_ITEM_BY_CAT_AND_PROP = "SELECT `items`.`id`,	`items`.`display_name`, `items`.`warranty`, `items`.`dealer_price`, `items`.`vat_price`,
																				`items`.`dealer_price_amd`, `items`.`vat_price_amd`,
																				`items`.`company_id`,
																				`items`.`hidden`,
																				`items`.`model`,
																				`items`.`brand`,
																				`items`.`categories_ids`,
																				`items`.`item_available_till_date`,
																				`items`.`pictures_count`,
																					`companies`.`name` as `company_name`,  `companies`.`rating` as `company_rating`,
                                                                                                                                                                                                                    GROUP_CONCAT(`company_branches`.`phones`) as `company_phones`,
																					%s																					
																					%s
 																					`online_users`.`email` = `online_users`.`email` AS `is_company_online`,
 																					%s 
  																				FROM `%s` 
                                                                                                                                                                                                        INNER JOIN `companies` ON `company_id` = `companies`.`id` AND `companies`.`hidden`=0 AND `companies`.`passive`=0
                                                                                                                                                                                                        LEFT JOIN `company_branches` ON `company_branches`.`company_id` = `companies`.`id`
																				  %s
																				  LEFT JOIN `online_users`	ON `companies`.`email` = `online_users`.`email`
																				  WHERE %s %s %s AND `items`.`hidden` = 0 %s GROUP BY `items`.`id` ORDER BY %s `pcc_item_compatible` DESC, `customer_item_price`  LIMIT %d, %d ";

    public function getPccItemsByCategoryFormula($user_id, $userLevel, $profitFormula, $requiredCategoriesFormulasArray, $neededCategoriesIdsAndOrFormulaArray, $offset, $limit, $selected_items_sql_array_str, $showOutOfDateItems = false, $search_text = null) {
        $join_company_dealers_table_for_user_only = "";
        $add_is_dealer_of_this_company = "";
        if ($userLevel == UserGroups::$USER) {
            $join_company_dealers_table_for_user_only = "LEFT JOIN  `company_dealers` ON `company_dealers`.`user_id`= %d AND `company_dealers`.`company_id` = `companies`.`id`";
            $join_company_dealers_table_for_user_only = sprintf($join_company_dealers_table_for_user_only, $user_id);
            $add_is_dealer_of_this_company = " IF (`company_dealers`.`user_id`, 1,0) AS `is_dealer_of_this_company`, ";
        } elseif ($userLevel == UserGroups::$GUEST) {
            $add_is_dealer_of_this_company = " 0 AS `is_dealer_of_this_company`, ";
        } else {
            $add_is_dealer_of_this_company = " 1 AS `is_dealer_of_this_company`, ";
        }

        $add_customer_price_field = " " . sprintf($profitFormula, 'dealer_price', 'dealer_price', 'dealer_price'
                        , 'dealer_price', 'dealer_price', 'dealer_price') . " AS `customer_item_price`, " . sprintf($profitFormula, 'vat_price', 'vat_price', 'vat_price'
                        , 'vat_price', 'vat_price', 'vat_price') . " AS `customer_vat_item_price` ";
        $base_category_filter = $this->makeRequiredCategoriesFolmula($requiredCategoriesFormulasArray);
        $categories_filter_query = "1 as `pcc_item_compatible`, ";
        if (!empty($neededCategoriesIdsAndOrFormulaArray)) {
            $categories_filter_query = $this->makeCategoriesIdsAndOrFormulaArrayQuery($neededCategoriesIdsAndOrFormulaArray);
        }

        $selected_items_sql_always_in_result = '';
        if (!empty($selected_items_sql_array_str)) {
            $selected_items_sql_always_in_result = " `items`.`id` IN " . $selected_items_sql_array_str . " OR ";
            $selected_items_sql_array_str = "case when `items`.`id` IN $selected_items_sql_array_str then 0 else 1 end,";
        }

        $showOutOfDateItemsQuery = "";
        if ($showOutOfDateItems !== true) {
            $showOutOfDateItemsQuery = "AND item_available_till_date >= '" . date('Y-m-d') . "'";
        }
        $text_serch_query = '';
        if (!empty($search_text)) {
            $search_text_formated_for_using_in_regext = $this->formatSearchTextForUsingInRegexp($search_text);
            $search_text_formated_for_match = $this->formatSearchTextForUsingInMatch($search_text);
            $text_serch_query = sprintf(" (MATCH (display_name, model, categories_names) AGAINST ('%s' IN BOOLEAN MODE) OR 
				CONCAT(`items`.display_name, `items`.model, `items`.categories_names) REGEXP '%s') AND", $search_text_formated_for_match, $search_text_formated_for_using_in_regext);
        }
        $sqlQuery = sprintf(self::$GET_ITEM_BY_CAT_AND_PROP, $categories_filter_query, $add_is_dealer_of_this_company, $add_customer_price_field, $this->getTableName(), $join_company_dealers_table_for_user_only, $selected_items_sql_always_in_result, $text_serch_query, $base_category_filter, $showOutOfDateItemsQuery, $selected_items_sql_array_str, $offset, $limit);
        $result = $this->fetchRows($sqlQuery);
        //var_dump($sqlQuery );exit;
        return $result;
    }

    /**
     * This is the main function that is used in system pc configurator tab.
     * $neededCategoriesIdsAndOrFormulaArray format is such this array('(', 1, 'and', 4,')','or', '8') it means '
     */
    public static $GET_ITEM_BY_CAT_AND_PROP_COUNT = "SELECT COUNT(*) as `count` FROM	`items` 
																									INNER JOIN `companies` ON `company_id` = `companies`.`id` AND `companies`.`hidden`=0 AND `companies`.`passive`=0
																									WHERE %s %s AND `items`.`hidden` = 0 %s";

    public function getPccItemsByCategoryFormulaCount($requiredCategoriesFormulasArray, $showOutOfDateItems = false, $search_text = null) {
        $base_category_filter = $this->makeRequiredCategoriesFolmula($requiredCategoriesFormulasArray);
        $showOutOfDateItemsQuery = "";
        if ($showOutOfDateItems !== true) {
            $showOutOfDateItemsQuery = "AND item_available_till_date >= '" . date('Y-m-d') . "'";
        }
        $text_serch_query = '';
        if (!empty($search_text)) {
            $search_text_formated_for_using_in_regext = $this->formatSearchTextForUsingInRegexp($search_text);
            $search_text_formated_for_match = $this->formatSearchTextForUsingInMatch($search_text);
            $text_serch_query = sprintf(" (MATCH (display_name, model, categories_names) AGAINST ('%s' IN BOOLEAN MODE) OR 
				CONCAT(`items`.display_name, `items`.model, `items`.categories_names) REGEXP '%s') AND", $search_text_formated_for_match, $search_text_formated_for_using_in_regext);
        }
        $sqlQuery = sprintf(self::$GET_ITEM_BY_CAT_AND_PROP_COUNT, $text_serch_query, $base_category_filter, $showOutOfDateItemsQuery);
        $count = $this->fetchField($sqlQuery, "count");
        //var_dump($count);exit;
        return $count;
    }

    public function makeRequiredCategoriesFolmula($requiredCategoriesFormulasArray) {
        $q = "(";
        foreach ($requiredCategoriesFormulasArray as $key => $el) {
            if ($el === '(') {
                $q .= '(';
            } else if ($el === ')') {
                $q .= ')';
            } else if (strtolower($el) === 'and') {
                $q .= ' and ';
            } else if (strtolower($el) === 'or') {
                $q .= ' or ';
            } else if (strtolower($el) === 'not') {
                $q .= ' not ';
            } else {
                $q .= "  `items`.`categories_ids` LIKE '%," . $el . ",%'";
            }
        }
        $q .= ')';
        return $q;
    }

    public function makeCategoriesIdsAndOrFormulaArrayQuery($neededCategoriesIdsAndOrFormulaArray) {
        $pcc_item_compatible = " IF (";
        $categories_filter_query = "";
        $subquery = "";
        foreach ($neededCategoriesIdsAndOrFormulaArray as $key => $el) {

            if ($el === '(') {
                $subquery .= '(';
            } else if ($el === ')') {
                $subquery .= ')';
            } else if (strtolower($el) === 'and') {
                $subquery .= ' and ';
            } else if (strtolower($el) === 'or') {
                $subquery .= ' or ';
            } else if (strtolower($el) === 'not') {
                $subquery .= ' not ';
            } else if (strtolower($el) === ':') {
                $subquery .= ' as ';
                $nextElementIsFieldName = true;
            } else if (isset($nextElementIsFieldName) && $nextElementIsFieldName === true) {
                $categories_filter_query .= $subquery . $el . ', ';
                $nextElementIsFieldName = false;
                $pcc_item_compatible .= '(' . substr($subquery, 0, strlen($subquery) - 3) . ' ) = 1 AND';
                $subquery = "";
            } else {
                $subquery .= "  `items`.`categories_ids` LIKE '%," . $el . ",%'";
            }
        }
        $pcc_item_compatible = substr($pcc_item_compatible, 0, strlen($pcc_item_compatible) - 3) . ',1,0) AS `pcc_item_compatible`,';

        return $categories_filter_query . $pcc_item_compatible;
    }

    public function getCapabilityFieldsNamesArray($neededCategoriesIdsAndOrFormulaArray) {
        $ret = array();
        foreach ($neededCategoriesIdsAndOrFormulaArray as $key => $value) {
            if ($nextIsFieldName === true) {
                $ret[] = $value;
                $nextIsFieldName = false;
            }
            if ($value === ':') {
                $nextIsFieldName = true;
            }
        }
        return $ret;
    }

    public static $GET_NOT_HIDDEN_ITEMS = "SELECT `items`.* FROM `%s` 
	INNER JOIN `companies` ON `company_id` = `companies`.`id` AND `companies`.`hidden`=0 
	WHERE `items`.`hidden` = 0 AND `items`.`id` in (%s)";

    public function getItemsByIds($item_ids) {
        $sqlQuery = sprintf(self::$GET_NOT_HIDDEN_ITEMS, $this->getTableName(), $item_ids);
        //var_dump($sqlQuery );exit;
        $result = $this->fetchRows($sqlQuery);

        return $result;
    }

    public static $GET_HIDDEN_BY_MONTHS = "SELECT `id` FROM `%s` WHERE hidden=1 AND `item_available_till_date`<'%s'";

    public function getHiddenItemsByMonthsNumber($monthsNumber) {
        $date = date("Y-m-d", strtotime("-" . $monthsNumber . " month"));
        $sqlQuery = sprintf(self::$GET_HIDDEN_BY_MONTHS, $this->getTableName(), $date);
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

}

?>