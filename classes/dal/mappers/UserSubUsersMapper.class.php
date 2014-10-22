<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/UserSubUsersDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class UserSubUsersMapper extends AbstractMapper {

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
		$this->tableName = "user_sub_users";
	}

	/**
	 * Returns an singleton instance of this class
	 * @return
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new UserSubUsersMapper();
		}
		return self::$instance;
	}

	/**
	 */
	public function createDto() {
		return new UserSubUsersDto();
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

	public static $GET_USER_SUB_USERS = "SELECT `user_id`, `sub_user_id` ,`users`.`name` as `user_name` ,`users`.`login_type` as `user_login_type` ,`users`.`lname` as `user_lname`, 
																				`users`.`telephone` as `user_telephone`, `users`.`email` as `user_email` FROM `users` 
																				INNER JOIN `%s` ON `%s`.`sub_user_id` = `users`.`id`
																					WHERE `%s`.`user_id`= %d ORDER BY `timestamp`";

	public function getUserSubUsers($userId) {
		$sqlQuery = sprintf(self::$GET_USER_SUB_USERS, $this->getTableName(), $this->getTableName(), $this->getTableName(), $userId);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

	public static $GET_BY_USER_ID_AND_SUB_USER_ID = "SELECT * FROM `%s` WHERE user_id = %d AND sub_user_id = %d";

	public function getByUserIdAndSubUserId($userId, $subUserId) {

		$sqlQuery = sprintf(self::$GET_BY_USER_ID_AND_SUB_USER_ID, $this->getTableName(), $userId, $subUserId);
		$result = $this->fetchRows($sqlQuery);
		if (count($result) === 1) {
			return $result[0];
		} else {
			return null;
		}
	}

	public static $GET_AFTER_DATE = "SELECT * FROM `%s` WHERE `user_id` = %d AND `timestamp`>'%s' ";

	public function getRowsAddedAfterGivenDatetime($userId, $datetime) {
		$sqlQuery = sprintf(self::$GET_AFTER_DATE, $this->getTableName(), $userId, $datetime);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

	public static $GET_USER_PARENT_ID = "SELECT * FROM `%s` WHERE sub_user_id = %d";

	public function getUserParentId($subUserId) {
		$sqlQuery = sprintf(self::$GET_USER_PARENT_ID, $this->getTableName(), $subUserId);
		$result = $this->fetchRows($sqlQuery);
		if (count($result) === 1) {
			return $result[0];
		} else {
			return null;
		}
	}

}

?>