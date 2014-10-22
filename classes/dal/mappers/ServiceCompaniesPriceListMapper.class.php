<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/ServiceCompaniesPriceListDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class ServiceCompaniesPriceListMapper extends AbstractMapper {

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
        $this->tableName = "service_companies_price_list";
    }

    /**
     * Returns an singleton instance of this class
     * @return
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new ServiceCompaniesPriceListMapper();
        }
        return self::$instance;
    }

    /**
     * @see AbstractMapper::createDto()
     */
    public function createDto() {
        return new ServiceCompaniesPriceListDto();
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

    public static $GET_COMPANY_LAST_PRICE = "SELECT * FROM `%s` WHERE service_company_id = '%s' AND file_ext <> 'zip' ORDER BY id";

    public function getCompanyLastPrices($service_company_id) {
        $sqlQuery = sprintf(self::$GET_COMPANY_LAST_PRICE, $this->getTableName(), $service_company_id);
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

    public static $GET_UPLOADED_PRICE_COUNT = "SELECT COUNT(id) AS `uploaded_price_count` FROM `%s` WHERE `uploader_type`= '%s' AND `uploader_id` = %d AND `upload_date_time`>= DATE_SUB('%s', INTERVAL %d DAY)";

    public function getUploadedPriceCountByUploaderTypeIdAndDays($uploaderType, $companyId, $days) {
        $sqlQuery = sprintf(self::$GET_UPLOADED_PRICE_COUNT, $this->getTableName(), $uploaderType, $companyId, date('Y-m-d H:i:s'), $days);
        return $this->fetchField($sqlQuery, 'uploaded_price_count');
    }

    public static $GET_COMPANIES_ZIPPPED_PRICES = "SELECT * FROM `%s` WHERE `file_ext`= 'zip' AND `service_company_id` IN (%s) ";

    public function getCompaniesZippedPricesByDaysNumber($companyIdsArray, $dayNumber) {
        $sqlQuery = sprintf(self::$GET_COMPANIES_ZIPPPED_PRICES, $this->getTableName(), implode(',', $companyIdsArray));
        if (isset($dayNumber)) {
            $sqlQuery .= sprintf("AND `upload_date_time`>= DATE_SUB('%s', INTERVAL %d DAY) ", date('Y-m-d'), $dayNumber);
        }
        $sqlQuery .= " ORDER BY `upload_date_time` DESC";
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

    public static $GET_PRICE_COUNT = "SELECT COUNT(id) AS `price_count` FROM `%s` WHERE `service_company_id` = %d AND `upload_date_time`>= DATE_SUB('%s', INTERVAL %d DAY)";

    public function getPriceCountByDays($companyId, $days) {
        $sqlQuery = sprintf(self::$GET_PRICE_COUNT, $this->getTableName(), $companyId, date('Y-m-d H:i:s'), $days);
        return $this->fetchField($sqlQuery, 'price_count');
    }

    public static $GET_ALL_AFTER_TIME = "SELECT `%s`.*, `companies`.`name` as `company_name` FROM `%s` LEFT JOIN `companies` ON `companies`.`id`=`%s`.`service_company_id` WHERE `companies`.`hidden`=0 AND `upload_date_time`>= '%s' AND `file_ext`<>'zip'";

    public function getAllPricesAfterTime($datetime, $companiesIds) {

        $sqlQuery = sprintf(self::$GET_ALL_AFTER_TIME, $this->getTableName(), $this->getTableName(), $this->getTableName(), $datetime);
        if (!empty($companiesIds)) {
            $sqlQuery.= sprintf(" AND FIND_IN_SET(`service_company_id`, '%s')", implode(',', $companiesIds));
        }
        return $this->fetchRows($sqlQuery);
    }

    public static $GET_COMPANY_HISTORY_PRICES_ORDER_BY_DATE = "SELECT * FROM `%s` WHERE service_company_id = '%s' AND file_ext = 'zip' ORDER BY upload_date_time DESC LIMIT %d, %d ";

    public function getCompanyHistoryPricesOrderByDate($service_company_id, $offset, $limit) {
        $sqlQuery = sprintf(self::$GET_COMPANY_HISTORY_PRICES_ORDER_BY_DATE, $this->getTableName(), $service_company_id, $offset, $limit);
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

    public static $GET_COMPANY_TODAY_PRICE_UPLOADED_TIMES = "SELECT COUNT(`id`) as `count` FROM `%s` WHERE service_company_id = %d AND DATE(upload_date_time) = CURDATE()";

    public function getCompanyTodayPriceUploadedTimes($companyId) {
        $sqlQuery = sprintf(self::$GET_COMPANY_TODAY_PRICE_UPLOADED_TIMES, $this->getTableName(), $companyId);
        $count = $this->fetchField($sqlQuery, "count");
        return $count;
    }

}

?>