<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/OrderDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class OrderMapper extends AbstractMapper {

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
        $this->tableName = "orders";
    }

    /**
     * Returns an singleton instance of this class
     * @return
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new OrderMapper();
        }
        return self::$instance;
    }

    /**
     * @see AbstractMapper::createDto()
     */
    public function createDto() {
        return new OrderDto();
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

    /*
      public static $GET_ORDERS_BY_FILTERS = "SELECT * FROM `%s` WHERE %s %s ORDER BY order_date_time DESC";
      public function getOrdersByFilters($show_only_filter, $pastDate) {

      $showFilter = "";
      if ($show_only_filter == 'nc') {
      $showFilter = "`orders`.`confirmed_by_pcstore` = 0 AND";
      } elseif ($show_only_filter == 'nd') {
      $showFilter = "`orders`.`confirmed_by_pcstore` = 1 AND `orders`.`delivered_date_time` = '2000-01-01' AND";
      } elseif ($show_only_filter == 'all') {
      $showFilter = "";
      }
      $dateFilter = "DATE(`orders`.`order_date_time`) >= DATE('" . $pastDate . "')";
      $sqlQuery = sprintf(self::$GET_ORDERS_BY_FILTERS, $this->getTableName(), $showFilter, $dateFilter);
      $result = $this->fetchRows($sqlQuery);
      return $result;
      }
     */
    /*
      public static $GET_CUSTOMER_CONFIRMED_ORDERS_BY_EMAIL = "SELECT * FROM `%s` WHERE `customer_email` = '%s' AND confirmed_by_customer = 1 ORDER BY order_date_time DESC";
      public function getCustomerConfirmedOrdersByEmail($email) {
      $sqlQuery = sprintf(self::$GET_CUSTOMER_CONFIRMED_ORDERS_BY_EMAIL, $this->getTableName(), $email);
      $result = $this->fetchRows($sqlQuery);
      return $result;
      } */

    public static $GET_ORDER_JOINED_WITH_DETAILS = "SELECT `%s`.*, 
							 `order_details`.`bundle_id` as `order_details_bundle_id`,
							 `order_details`.`item_id` as `order_details_item_id`,
							 `order_details`.`special_fee_id` as `order_details_special_fee_id`,
							 `order_details`.`item_display_name` as `order_details_item_display_name`,
							 `order_details`.`bundle_display_name_id` as `order_details_bundle_display_name_id`,
							 `order_details`.`special_fee_display_name_id` as `order_details_special_fee_display_name_id`,
							 `order_details`.`bundle_count` as `order_details_bundle_count`,
							 `order_details`.`item_count` as `order_details_item_count`,							 
							 `order_details`.`customer_item_price` as `order_details_customer_item_price`,
							 `order_details`.`item_dealer_price` as `order_details_item_dealer_price`,
							 `order_details`.`special_fee_price` as `order_details_special_fee_price`,							 
							 `order_details`.`item_company_id` as `order_details_item_company_id`,
							 `order_details`.`is_dealer_of_item` as `order_details_is_dealer_of_item`,
							 `order_details`.`discount` as `order_details_discount`,
							 `order_details`.`customer_bundle_price_amd` as `order_details_customer_bundle_price_amd`,
							 `order_details`.`customer_bundle_price_usd` as `order_details_customer_bundle_price_usd`,
							 `credit_orders`.`deposit` as `credit_orders_deposit`,							 
							 `credit_orders`.`credit_supplier_id` as `credit_orders_credit_supplier_id`,
							 `credit_orders`.`credit_months` as `credit_orders_credit_months`,
							 `credit_orders`.`annual_interest_percent` as `credit_orders_annual_interest_percent`,
							 `credit_orders`.`monthly_payment` as `credit_orders_monthly_payment`						 
							 FROM `%s`
							 LEFT JOIN `order_details` ON `%s`.id=`order_details`.`order_id`
							 LEFT JOIN `credit_orders` ON `%s`.id=`credit_orders`.`order_id`
							 WHERE `%s`.id=%d ";

    public function getOrderJoinedWithDetails($orderId) {
        $sqlQuery = sprintf(self::$GET_ORDER_JOINED_WITH_DETAILS, $this->getTableName(), $this->getTableName(), $this->getTableName(), $this->getTableName(), $this->getTableName(), $orderId);
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

    public static $GET_CUSTOMER_ORDERS_JOINED_WITH_DETAILS = "SELECT `%s`.*, 
							 `order_details`.`bundle_id` as `order_details_bundle_id`,
							 `order_details`.`item_id` as `order_details_item_id`,
							 `order_details`.`special_fee_id` as `order_details_special_fee_id`,
							 `order_details`.`item_display_name` as `order_details_item_display_name`,
							 `order_details`.`bundle_display_name_id` as `order_details_bundle_display_name_id`,
							 `order_details`.`special_fee_display_name_id` as `order_details_special_fee_display_name_id`,
							 `order_details`.`bundle_count` as `order_details_bundle_count`,
							 `order_details`.`item_count` as `order_details_item_count`,							 
							 `order_details`.`customer_item_price` as `order_details_customer_item_price`,
							 `order_details`.`item_dealer_price` as `order_details_item_dealer_price`,
							 `order_details`.`special_fee_price` as `order_details_special_fee_price`,							 
							 `order_details`.`item_company_id` as `order_details_item_company_id`,
							 `order_details`.`is_dealer_of_item` as `order_details_is_dealer_of_item`,
							 `order_details`.`discount` as `order_details_discount`,
							 `order_details`.`customer_bundle_price_amd` as `order_details_customer_bundle_price_amd`,
							 `order_details`.`customer_bundle_price_usd` as `order_details_customer_bundle_price_usd`
							 FROM `%s` LEFT JOIN `order_details` ON `%s`.id=`order_details`.`order_id` WHERE `%s`.`customer_email`='%s'
							 ORDER BY `orders`.`order_date_time` DESC, `order_details`.`bundle_id`";

    public function getCustomerOrderJoinedWithDetails($customer_email) {
        $sqlQuery = sprintf(self::$GET_CUSTOMER_ORDERS_JOINED_WITH_DETAILS, $this->getTableName(), $this->getTableName(), $this->getTableName(), $this->getTableName(), $customer_email);
        $result = $this->fetchRows($sqlQuery);
        //var_dump($sqlQuery );exit;
        return $result;
    }

    public static $GET_ALL_ORDERS_JOINED_WITH_DETAILS = "SELECT `%s`.*, 
							 `order_details`.`bundle_id` as `order_details_bundle_id`,
							 `order_details`.`item_id` as `order_details_item_id`,
							 `order_details`.`special_fee_id` as `order_details_special_fee_id`,
							 `order_details`.`item_display_name` as `order_details_item_display_name`,
							 `order_details`.`bundle_display_name_id` as `order_details_bundle_display_name_id`,
							 `order_details`.`special_fee_display_name_id` as `order_details_special_fee_display_name_id`,
							 `order_details`.`bundle_count` as `order_details_bundle_count`,
							 `order_details`.`item_count` as `order_details_item_count`,							 
							 `order_details`.`customer_item_price` as `order_details_customer_item_price`,
							 `order_details`.`item_dealer_price` as `order_details_item_dealer_price`,
							 `order_details`.`special_fee_price` as `order_details_special_fee_price`,							 
							 `order_details`.`item_company_id` as `order_details_item_company_id`,
							 `order_details`.`is_dealer_of_item` as `order_details_is_dealer_of_item`,
							 `order_details`.`discount` as `order_details_discount`,
							 `order_details`.`customer_bundle_price_amd` as `order_details_customer_bundle_price_amd`,
							 `order_details`.`customer_bundle_price_usd` as `order_details_customer_bundle_price_usd`
							 FROM `%s` LEFT JOIN `order_details` ON `%s`.id=`order_details`.`order_id`							  
							 %s
							 ORDER BY `orders`.`order_date_time` DESC, `order_details`.`bundle_id`";

    public function getAllOrdersJoinedWithDetails($status = -1) {
        $statusFilter = "";
        if ($status >= 0) {
            $statusFilter = "WHERE `" . $this->getTableName() . "`.`status`=" . $status;
        }
        $sqlQuery = sprintf(self::$GET_ALL_ORDERS_JOINED_WITH_DETAILS, $this->getTableName(), $this->getTableName(), $this->getTableName(), $statusFilter);
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

}

?>