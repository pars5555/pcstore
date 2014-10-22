<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/CustomerCartDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class CustomerCartMapper extends AbstractMapper {

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
        $this->tableName = "customer_cart";
    }

    /**
     * Returns an singleton instance of this class
     * @return
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new CustomerCartMapper();
        }
        return self::$instance;
    }

    /**
     * @see AbstractMapper::createDto()
     */
    public function createDto() {
        return new CustomerCartDto();
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

    public static $GET_CUTOMER_CART = "SELECT `%s`.* , %s %s,	
	`bundle_items`.`bundle_display_name_id` AS `bundle_display_name_id`,  
	`bundle_items`.`cached_item_display_name` AS `bundle_cached_item_display_name`,
	`bundle_items`.`item_last_dealer_price` AS `bundle_item_last_dealer_price`,   
  `bundle_items`.`item_id` AS `bundle_item_id`,
  `bundle_items`.`item_count` AS `bundle_item_count`,
  `bundle_items`.`special_fee_id`  AS `special_fee_id`,
  `bundle_items`.`special_fee_dynamic_price`  AS `special_fee_dynamic_price`,  
  `special_fees`.`price` AS `special_fee_price`,
  `special_fees`.`description_text_id` AS `special_fee_description_text_id`,
	IF (`items`.`id`>0 AND `items`.`hidden`=0 AND `items`.`item_available_till_date`>= '%s', 1, 0) AS `item_available`,
	`items`.`display_name` as `item_display_name`,
	`items`.`dealer_price` as `item_dealer_price`,`items`.`vat_price` as `item_vat_price`,  `items`.`brand` as `item_brand`,	`items`.`categories_ids` AS `item_categories_ids`,	 
	`items`.`item_available_till_date` as `item_available_till_date` , `items`.`hidden` as `item_hidden`,`items`.`company_id` AS `item_company_id` 	
  FROM `%s` 
  LEFT JOIN `bundle_items` ON `customer_cart`.`bundle_id` = `bundle_items`.`bundle_id` 
  LEFT JOIN `items` ON `customer_cart`.`item_id` = `items`.`id` OR  `bundle_items`.`item_id` = `items`.`id`
  LEFT JOIN `special_fees` ON `bundle_items`.`special_fee_id` = `special_fees`.`id`  
  LEFT JOIN `companies` ON `items`.`company_id` = `companies`.`id` 
	%s
	WHERE `customer_email` = '%s' %s ORDER BY `%s`.`id`, `bundle_items`.`id`";

    public function getCustomerCart($email, $user_id, $userLevel, $profitFormula, $id = null) {
        $join_company_dealers_table_for_user_only = "";
        $add_is_dealer_of_this_company = "";
        if ($userLevel == UserGroups::$USER) {
            $join_company_dealers_table_for_user_only = "LEFT JOIN `company_dealers` ON `company_dealers`.`user_id`= %d AND `company_dealers`.`company_id` = `companies`.`id`";
            $join_company_dealers_table_for_user_only = sprintf($join_company_dealers_table_for_user_only, $user_id);
            $add_is_dealer_of_this_company = "IF (`company_dealers`.`user_id`, 1,0) AS `is_dealer_of_this_company` ,";
        } elseif ($userLevel == UserGroups::$GUEST) {
            $add_is_dealer_of_this_company = " 0 AS `is_dealer_of_this_company`, ";
        } else {
            $add_is_dealer_of_this_company = " 1 AS `is_dealer_of_this_company`, ";
        }
        $add_customer_price_field = " " . sprintf($profitFormula, 'dealer_price', 'dealer_price', 'dealer_price'
                        , 'dealer_price', 'dealer_price', 'dealer_price') . " AS `customer_item_price`, " . sprintf($profitFormula, 'vat_price', 'vat_price', 'vat_price'
                        , 'vat_price', 'vat_price', 'vat_price') . " AS `customer_vat_item_price` ";

        $sqlOnlyRow = "";
        if (isset($id)) {
            //only returns specified row
            $sqlOnlyRow = "AND `customer_cart`.`id` =" . $id;
        }

        $sqlQuery = sprintf(self::$GET_CUTOMER_CART, $this->getTableName(), $add_is_dealer_of_this_company, $add_customer_price_field, date('Y-m-d'), $this->getTableName(), $join_company_dealers_table_for_user_only, $email, $sqlOnlyRow, $this->getTableName());
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

    public static $GET_ITEM_AVAILABLE = "SELECT * FROM `%s` WHERE `customer_email` = '%s' AND `item_id` = %d";

    public function getItemInCustomerCart($email, $item_id) {
        $sqlQuery = sprintf(self::$GET_ITEM_AVAILABLE, $this->getTableName(), $email, $item_id);
        $result = $this->fetchRows($sqlQuery);
        if (count($result) === 1) {
            return $result[0];
        }
    }

    public static $GET_BUNDLE_AVAILABLE = "SELECT * FROM `%s` WHERE `customer_email` = '%s' AND `bundle_id` = %d";

    public function getBundleInCustomerCart($email, $bundle_id) {
        $sqlQuery = sprintf(self::$GET_BUNDLE_AVAILABLE, $this->getTableName(), $email, $bundle_id);
        $result = $this->fetchRows($sqlQuery);
        if (count($result) === 1) {
            return $result[0];
        }
    }

    public static $GET_CUSTOMER_CART_COUNT = "SELECT SUM(`altable`.`total_count`) AS `total_count` FROM(
  SELECT sum(DISTINCT `customer_cart`.`count`) as `total_count` FROM `%s` 
	LEFT JOIN bundle_items ON `customer_cart`.`bundle_id` = `bundle_items`.`bundle_id` 
  LEFT JOIN items ON `customer_cart`.`item_id` = `items`.`id` OR  `bundle_items`.`item_id` = `items`.`id`
	WHERE `items`.`id` > 0 AND `items`.`hidden` = 0 AND `customer_email` = '%s' GROUP BY `customer_cart`.`id`) as `altable`";

    public function getCustomerCartTotalCount($customer_email) {
        $sqlQuery = sprintf(self::$GET_CUSTOMER_CART_COUNT, $this->getTableName(), $customer_email);
        return (int) $this->fetchField($sqlQuery, 'total_count');
    }

    public static $EMPTY_CUSTOMER_CART = "DELETE FROM `%s` WHERE `customer_email`='%s'";

    public function emptyCustomerCart($customerEmail) {
        $sqlQuery = sprintf(self::$EMPTY_CUSTOMER_CART, $this->getTableName(), $customerEmail);
        return $this->dbms->query($sqlQuery);
    }

    public static $GET_CUSTOMER_BUNDLES_IDS = "SELECT GROUP_CONCAT(`bundle_id`) as `bundles_ids` FROM `%s` WHERE 
																															customer_email = '%s' AND `bundle_id`>0 AND `is_system_bundle` = 0";

    public function getCustomerCartBundlesIdsJoinedByComma($customerEmail) {
        $sqlQuery = sprintf(self::$GET_CUSTOMER_BUNDLES_IDS, $this->getTableName(), $customerEmail);
        return $this->fetchField($sqlQuery, "bundles_ids");
    }

    private static $DELETE_BY_COMPANY_ID = "DELETE `%s` FROM `%s` LEFT JOIN `items` ON `%s`.`item_id` = `items`.`id` 
																					WHERE `%s`.`customer_email` = '%s' AND `items`.`company_id`=%d";

    public function deleteCustomerItemsByCompanyId($customerEmail, $companyId) {
        $sqlQuery = sprintf(self::$DELETE_BY_COMPANY_ID, $this->getTableName(), $this->getTableName(), $this->getTableName(), $this->getTableName(), $customerEmail, $companyId);
        return $this->dbms->query($sqlQuery);
    }

    private static $GET_BY_COMPANY_ID = "SELECT * FROM `%s` LEFT JOIN `items` ON `%s`.`item_id` = `items`.`id` 
																					WHERE `%s`.`customer_email` = '%s' AND `items`.`company_id`=%d";

    public function getCustomerItemsByCompanyId($customerEmail, $companyId) {
        $sqlQuery = sprintf(self::$GET_BY_COMPANY_ID, $this->getTableName(), $this->getTableName(), $this->getTableName(), $customerEmail, $companyId);
        return $this->fetchRows($sqlQuery);
    }

    private static $DELETE_BY_BUNDLES_IDS = "DELETE FROM `%s` WHERE `customer_email` = '%s' AND `bundle_id` IN (%s)";

    public function deleteByBundlesIds($customerEmail, $bundlesIds) {
        $sqlQuery = sprintf(self::$DELETE_BY_BUNDLES_IDS, $this->getTableName(), $customerEmail, $bundlesIds);
        return $this->dbms->query($sqlQuery);
    }

    private static $GET_BUNDLES_IDS_BY_COMPANY_ID = "SELECT GROUP_CONCAT(`bid` SEPARATOR ',') as `bundles_ids`  FROM (
																									SELECT `%s`.`bundle_id` AS `bid` FROM `%s` LEFT JOIN `bundle_items` ON 
																									`bundle_items`.`bundle_id` = `%s`.`bundle_id` LEFT JOIN `items` 
																									ON `bundle_items`.`item_id` = `items`.`id`  
																									WHERE `customer_email` = '%s' AND `%s`.`bundle_id`>0 AND `items`.`company_id`=%d GROUP BY `%s`.id) AS `temp_table`";

    public function getCustomerBundlesIdsByCompanyId($customerEmail, $companyId) {
        $sqlQuery = sprintf(self::$GET_BUNDLES_IDS_BY_COMPANY_ID, $this->getTableName(), $this->getTableName(), $this->getTableName(), $customerEmail, $this->getTableName(), $companyId, $this->getTableName());
        return $this->fetchField($sqlQuery, "bundles_ids");
    }

}

?>