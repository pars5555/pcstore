<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/CustomerLocalEmailDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class CustomerLocalEmailsMapper extends AbstractMapper {

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
		$this->tableName = "customer_local_emails";
	}

	/**
	 * Returns an singleton instance of this class
	 * @return
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new CustomerLocalEmailsMapper();
		}
		return self::$instance;
	}

	/**
	 * @see AbstractMapper::createDto()
	 */
	public function createDto() {
		return new CustomerLocalEmailDto();
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

	public static $GET_INBOX_UNREAD_COUNT = "SELECT count(id) as `unread_count` from `%s` WHERE
`customer_email` = '%s'  AND `folder` = 'inbox'  AND FIND_IN_SET('%s',`to_emails`) AND `trash`=0 AND `deleted`=0 AND `read_status` = 0";

	public function getCustomerInboxUnreadEmailsCountCustomerEmail($customerEmail) {
		$sqlQuery = sprintf(self::$GET_INBOX_UNREAD_COUNT, $this->getTableName(), $customerEmail, $customerEmail);
		$result = $this->fetchField($sqlQuery, 'unread_count');
		return $result;
	}

	public static $GET_SENT_EMAILS = "SELECT 
`%s`.*,  CONCAT(IF(`users`.`name` <> '',CONCAT(`users`.`name`, ' '),''),IF(`users`.`lname` <> '',CONCAT(`users`.`lname`),''),IF(`companies`.`name` <> '',`companies`.`name`,''),
IF(`admins`.`title` <> '',`admins`.`title`,'')
) AS `customer_title`  
FROM `%s` 
LEFT JOIN `users` 
    ON SUBSTRING_INDEX(`customer_local_emails`.`to_emails`,',',1) = `users`.`email` 
  LEFT JOIN companies 
    ON SUBSTRING_INDEX(`customer_local_emails`.`to_emails`,',',1) = `companies`.`email` 
    LEFT JOIN `admins` 
    ON SUBSTRING_INDEX(`customer_local_emails`.`to_emails`,',',1) = `admins`.`email`  
WHERE `customer_email` = '%s' AND `from_email` = '%s' AND `folder` = 'sent' AND `trash`=0 AND `deleted`=0 ORDER BY `datetime` DESC";

	public function getCustomerSentEmailsByCustomerEmail($customerEmail) {
		$sqlQuery = sprintf(self::$GET_SENT_EMAILS, $this->getTableName(), $this->getTableName(), $customerEmail, $customerEmail);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

	public static $GET_INBOX_EMAILS = "SELECT 
        `%s`.*,  CONCAT(IF(`users`.`name` <> '',CONCAT(`users`.`name`, ' '),''),IF(`users`.`lname` <> '',CONCAT(`users`.`lname`),''),IF(`companies`.`name` <> '',`companies`.`name`,''),
IF(`admins`.`title` <> '',`admins`.`title`,'')
) AS `customer_title`  
FROM `%s` 
LEFT JOIN `users` 
    ON `customer_local_emails`.`from_email` = `users`.`email` 
  LEFT JOIN companies 
    ON `customer_local_emails`.`from_email` = `companies`.`email` 
    LEFT JOIN `admins` 
    ON `customer_local_emails`.`from_email` = `admins`.`email`  
WHERE `customer_email` = '%s'  AND `folder` = 'inbox'  AND FIND_IN_SET('%s',`to_emails`) AND `trash`=0 AND `deleted`=0 ORDER BY `datetime` DESC";

	public function getCustomerInboxEmailsByCustomerEmail($customerEmail) {
		$sqlQuery = sprintf(self::$GET_INBOX_EMAILS, $this->getTableName(), $this->getTableName(), $customerEmail, $customerEmail);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

	public static $GET_TRASH_EMAILS = "SELECT  
        `%s`.*,  CONCAT(IF(`users`.`name` <> '',CONCAT(`users`.`name`, ' '),''),IF(`users`.`lname` <> '',CONCAT(`users`.`lname`),''),IF(`companies`.`name` <> '',`companies`.`name`,''),
IF(`admins`.`title` <> '',`admins`.`title`,'')
) AS `customer_title`  
FROM `%s` 

LEFT JOIN `users` 
    ON `customer_local_emails`.`from_email` = `users`.`email` 
  LEFT JOIN companies 
    ON `customer_local_emails`.`from_email` = `companies`.`email` 
    LEFT JOIN `admins` 
    ON `customer_local_emails`.`from_email` = `admins`.`email`  

WHERE `customer_email` = '%s' AND `trash`=1 AND `deleted`=0 ORDER BY `datetime` DESC";

	public function getCustomerTrashEmailsByCustomerEmail($customerEmail) {
		$sqlQuery = sprintf(self::$GET_TRASH_EMAILS, $this->getTableName(), $this->getTableName(), $customerEmail);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

	public static $TRASH_EMAILS = "UPDATE `%s` SET `trash`=1 WHERE `customer_email`='%s' AND `id` IN (%s)";

	public function trashEmails($customerEmail, $ids_array) {
		$sqlQuery = sprintf(self::$TRASH_EMAILS, $this->getTableName(), $customerEmail, implode(',', $ids_array));
		return $this->dbms->query($sqlQuery);
	}

	public static $DELETE_EMAILS = "UPDATE `%s` SET `deleted`=1 WHERE `customer_email`='%s' AND `id` IN (%s)";

	public function deleteEmails($customerEmail, $ids_array) {
		$sqlQuery = sprintf(self::$DELETE_EMAILS, $this->getTableName(), $customerEmail, implode(',', $ids_array));
		return $this->dbms->query($sqlQuery);
	}

	public static $RESTORE_EMAILS = "UPDATE `%s` SET `trash`=0 WHERE `customer_email`='%s' AND `id` IN (%s)";

	public function restoreEmails($customerEmail, $ids_array) {
		$sqlQuery = sprintf(self::$RESTORE_EMAILS, $this->getTableName(), $customerEmail, implode(',', $ids_array));
		return $this->dbms->query($sqlQuery);
	}

}

?>