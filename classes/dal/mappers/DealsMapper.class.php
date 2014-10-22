<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/DealsDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class DealsMapper extends AbstractMapper {

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
		$this->tableName = "deals";
	}

	/**
	 * Returns an singleton instance of this class
	 * @return
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new DealsMapper();
		}
		return self::$instance;
	}

	/**
	 * @see AbstractMapper::createDto()
	 */
	public function createDto() {
		return new DealsDto();
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

	public static $GET_DAILY_DEAL = "SELECT * FROM `%s` WHERE `date` = '%s' and `daily_deal`=1 and `enable`=1";

	public function getDateDailyDeal($date) {
		$sqlQuery = sprintf(self::$GET_DAILY_DEAL, $this->getTableName(), $date);
		$result = $this->fetchRows($sqlQuery);
		if (!empty($date)) {
			return $result[0];
		}
		return null;
	}

	public static $GET_DATE_TIME_LIGHTING_DEAL = "SELECT * FROM `%s` WHERE `daily_deal`=0 and `enable`=1 AND 
																	 CONCAT(`date`, ' ', `start_time`)<='%s' AND DATE_ADD( CONCAT(`date`, ' ', `start_time`), INTERVAL `duration_minutes` MINUTE)>='%s'";

	public function getDateTimeLightingDeals($datetime) {
		$sqlQuery = sprintf(self::$GET_DATE_TIME_LIGHTING_DEAL, $this->getTableName(), $datetime, $datetime);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

	public static $GET_DEAL_BY_PROMO_CODE = "SELECT * FROM `%s` WHERE `promo_code`='%s' AND `enable`=1 AND
		DATE_ADD( CONCAT(`date`, ' ', `start_time`), INTERVAL `duration_minutes` MINUTE)>='%s'";

	public function getDealsByPromoCode($promoCode) {
		$sqlQuery = sprintf(self::$GET_DEAL_BY_PROMO_CODE, $this->getTableName(), $promoCode, date('Y-m-d H:i:s'));
		$results = $this->fetchRows($sqlQuery);
		if (count($results) === 1)
			return $results[0];
	}

}

?>