<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class UserSubUsersDto extends AbstractDto {

	// Map of DB value to Field value
	protected $mapArray = array("id" => "id", "user_id" => "userId", "sub_user_id" => "subUserId", "timestamp" => "timestamp",
		//for users table
		"user_name" => "userName", "user_lname" => "userLastName", "user_email" => "userEmail", "user_telephone" => "userPhones",
		"user_login_type" => "userLoginType"
	);

	// constructs class instance
	public function __construct() {
		
	}

	// returns map array
	public function getMapArray() {
		return $this->mapArray;
	}

}

?>
