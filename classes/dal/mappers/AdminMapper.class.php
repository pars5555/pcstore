<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/AdminDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class AdminMapper extends AbstractMapper {

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
		$this->tableName = "admins";
	}

	/**
	 * Returns an singleton instance of this class
	 * @return
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new AdminMapper();
		}
		return self::$instance;
	}

	/**
	 * @see AbstractMapper::createDto()
	 */
	public function createDto() {
		return new AdminDto();
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

	public static $GET_ADMIN = "SELECT * FROM `%s` WHERE email = '%s' AND BINARY password = '%s'";

	public function getAdmin($email, $password) {
		$sqlQuery = sprintf(self::$GET_ADMIN, $this->getTableName(), $email, $password);
		$result = $this->fetchRows($sqlQuery);
		if (count($result) === 1) {
			return $result[0];
		} else {
			return null;
		}
	}

	public static $GET_ADMIN_BY_EMAIL = "SELECT * FROM `%s` WHERE email = '%s'";

	public function getAdminByEmail($email) {
		$sqlQuery = sprintf(self::$GET_ADMIN_BY_EMAIL, $this->getTableName(), $email);
		$result = $this->fetchRows($sqlQuery);
		if (count($result) === 1) {
			return $result[0];
		} else {
			return null;
		}
	}

	public static $VALIDATE_ADMIN = "SELECT * FROM `%s` WHERE id = %d AND hash = '%s'";

	public function validate($id, $hash) {
		$sqlQuery = sprintf(self::$VALIDATE_ADMIN, $this->getTableName(), $id, $hash);

		$result = $this->fetchRows($sqlQuery);

		if (count($result) === 1) {
			return $result[0];
		} else {
			return null;
		}
	}

	public static $GET_SMS_ENABLE_ADMINS = "SELECT * FROM `%s` WHERE  NOT ISNULL(`number_to_receive_sms_on_price_upload`) ";

	public function getSmsEnabledAdmins() {
		$sqlQuery = sprintf(self::$GET_SMS_ENABLE_ADMINS, $this->getTableName(), $id, $hash);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

}

?>