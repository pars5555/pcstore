<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class SentEmailsDto extends AbstractDto {

	// Map of DB value to Field value
	protected $mapArray = array("id" => "id", "from" => "from", "to" => "to", "subject" => "subject", "body" => "body", "log" => "log", "sent_date" => "sentDate");

	// constructs class instance
	public function __construct() {
		
	}

	// returns map array
	public function getMapArray() {
		return $this->mapArray;
	}

}

?>
