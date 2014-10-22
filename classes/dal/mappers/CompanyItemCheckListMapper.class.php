<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/CompanyItemCheckListDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class CompanyItemCheckListMapper extends AbstractMapper {

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
		$this->tableName = "company_item_check_list";
	}

	/**
	 * Returns an singleton instance of this class
	 * @return
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new CompanyItemCheckListMapper();
		}
		return self::$instance;
	}

	/**
	 */
	public function createDto() {
		return new CompanyItemCheckListDto();
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
	public static $GET_UNSENT_BY_COMPANY_ID = "SELECT `%s`.*, `items`.`display_name` as `item_display_name` FROM `%s` 
		INNER JOIN `items` on `items`.id = `%s`.`item_id` WHERE `%s`.`deleted` = 0 AND `%s`.`company_responded` = 0 AND `%s`.`company_id` = %d AND (sent_to_company_uid IS NULL OR FIND_IN_SET('%s', `sent_to_company_uid`) = 0)";

	public function getCompanyQuestionsFromCustomers($companyId, $winUid) {
		$sqlQuery = sprintf(self::$GET_UNSENT_BY_COMPANY_ID, $this->getTableName(), $this->getTableName(), $this->getTableName(), $this->getTableName(), $this->getTableName(), $this->getTableName(), $companyId, $winUid);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

	public static $SET_COMANY_ITEM_AVAILABILITY = "UPDATE `%s` SET `item_availability`= %d, `company_responded`= 1 WHERE `deleted` = 0 AND `company_id` = %d AND `item_id` = %d";

	public function setCompanyItemAvailability($companyId, $itemId, $itemAvailability) {
		$sqlQuery = sprintf(self::$SET_COMANY_ITEM_AVAILABILITY, $this->getTableName(), $itemAvailability, $companyId, $itemId);
		$result = $this->dbms->query($sqlQuery);
		return $result;
	}

	public static $GET_SENT_COMPANY_ITEM_CHECK_DTOS = "SELECT * FROM `%s` WHERE `deleted` = 0 AND `company_id` = %d and `item_id`= %d";

	public function getSentCompanyItemCheckDtos($companyId, $itemId) {
		$sqlQuery = sprintf(self::$GET_SENT_COMPANY_ITEM_CHECK_DTOS, $this->getTableName(), $companyId, $itemId);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

	public static $GET_COMPANY_RESPONDED_ITEM_CHECK_LIST = "SELECT `%s`.*, `items`.`display_name` as `item_display_name` FROM `%s` 
INNER JOIN `items` on `items`.id = `%s`.`item_id`		
WHERE `%s`.`deleted` = 0 AND `from_email` = '%s' AND `company_responded`=1 AND (response_sent_to_customer_uid IS NULL OR FIND_IN_SET('%s', `response_sent_to_customer_uid`) = 0)";

	public function getCustomerSentQuestionsResponses($customerEmail, $winUid) {
		$sqlQuery = sprintf(self::$GET_COMPANY_RESPONDED_ITEM_CHECK_LIST, $this->getTableName(), $this->getTableName(), $this->getTableName(), $this->getTableName(), $customerEmail, $winUid);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

	public static $GET_BY_ITEM_ID_WHERE_SENT_TO_COMPANY_IS_1_AND_COMPANY_RESPONDED_IS_0 = "SELECT * FROM `%s` WHERE `deleted` = 0 AND `item_id` = %d AND `company_responded` = 0";

	public function isItemCheckingStartedByAnotherUser($itemId) {
		$sqlQuery = sprintf(self::$GET_BY_ITEM_ID_WHERE_SENT_TO_COMPANY_IS_1_AND_COMPANY_RESPONDED_IS_0, $this->getTableName(), $itemId);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

	public static $GET_BY_ITEM_ID__AND_FROM_EMAIL = "SELECT * FROM `%s` WHERE `deleted` = 0 AND `item_id` = %d AND `company_responded` = 0 AND `from_email` = '%s'";

	public function isDuplicateItemChecking($itemId, $fromEmail) {
		$sqlQuery = sprintf(self::$GET_BY_ITEM_ID__AND_FROM_EMAIL, $this->getTableName(), $itemId, $fromEmail);
		$result = $this->fetchRows($sqlQuery);
		return count($result) > 0;
	}

	public static $REMOVE_TIME_OUTED_ROWS = "UPDATE `%s` SET `deleted` = 1 WHERE `timestamp` < DATE_SUB('%s', INTERVAL %d SECOND)";

	public function removeOldRowsBySeconds($timoutSeconds) {
		$sqlQuery = sprintf(self::$REMOVE_TIME_OUTED_ROWS, $this->getTableName(), date('Y-m-d H:i:s'), $timoutSeconds);
		$result = $this->dbms->query($sqlQuery);
		return $result;
	}

	public static $GET_BY_ITEM_ID = "SELECT * FROM `%s` WHERE `deleted` = 0 AND item_id = %d";

	public function getByItemId($itemId) {
		$sqlQuery = sprintf(self::$GET_BY_ITEM_ID, $this->getTableName(), $itemId);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

}

?>