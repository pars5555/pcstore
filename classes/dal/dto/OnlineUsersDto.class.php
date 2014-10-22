<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class OnlineUsersDto extends AbstractDto {

	// Map of DB value to Field value
	protected $mapArray = array("id" => "id", "email" => "email", "status" => "status",
		"ip" => "ip", "host" => "host", "country" => "country", "login_date_time" => "loginDateTime",
		"browser_name" => "browserName", "browser_version" => "browserVersion", "browser_platform" => "browserPlatform", "last_ping_time_stamp" => "lastPingTimeStamp",
		//for left join to users or companies
		"language_code" => "languageCode"
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
