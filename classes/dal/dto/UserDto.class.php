<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class UserDto extends AbstractDto {

	// Map of DB value to Field value
	protected $mapArray = array("id" => "id", "name" => "name", "lname" => "lastName", "email" => "email", "telephone" => "phones",
		"registered_date" => "registeredDate", "password" => "password", "hash" => "hash", "points" => "points", "address" => "address", "region" => "region",
		"sub_users_registration_code" => "subUsersRegistrationCode", "active" => "active", "activation_code" => "activationCode", "last_ping" => "lastPing",
		"hidden" => "hidden", "last_sms_validation_code" => "lastSmsValidationCode", "blocked" => "blocked", "login_type" => "loginType", "social_profile" => "socialProfile",
		"vip" => "vip", "enable_vip" => "enableVip", "language_code" => "languageCode", "sound_on" => "soundOn",
		//for left join to online_users table
		"online_status" => "onlineStatus"
	);

	// constructs class instance
	public function __construct() {
		
	}

	// returns map array
	public function getMapArray() {
		return $this->mapArray;
	}

	public function getCustomerContactNameForEmail() {
		return $this->getName() . ' ' . $this->getLastName();
	}

}

?>
