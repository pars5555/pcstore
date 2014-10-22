<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class UserPendingSubUsersDto extends AbstractDto {

	// Map of DB value to Field value
	protected $mapArray = array("id" => "id", "user_id" => "userId", "pending_sub_user_email" => "pendingSubUserEmail",
            'last_sent'=>"lastSent",'timestamp'=>"timestamp",
		//for users table
		"user_name" => "userName", "user_lname" => "userLastName", "user_email" => "userEmail", "user_telephone" => "userPhones",
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
