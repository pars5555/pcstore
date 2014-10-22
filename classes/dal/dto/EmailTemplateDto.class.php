<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class EmailTemplateDto extends AbstractDto {

	// Map of DB value to Field value
	protected $mapArray = array("id" => "id", "content_en" => "contentEn", "content_am" => "contentAm", "content_ru" => "contentRu");

	// constructs class instance
	public function __construct() {
		
	}

	// returns map array
	public function getMapArray() {
		return $this->mapArray;
	}

}

?>
