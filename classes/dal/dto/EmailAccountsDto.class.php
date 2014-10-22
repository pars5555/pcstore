<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class EmailAccountsDto extends AbstractDto {

	// Map of DB value to Field value
	protected $mapArray = array("id" => "id", "login" => "login", "pass" => "pass",
            "from_name" => "fromName",
            "imap_inbox" => "imapInbox"
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
