<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class DiscountPromoCodesDto extends AbstractDto {

	// Map of DB value to Field value
	protected $mapArray = array("id" => "id", "code" => "code", "discount_percent" => "discountPercent", "used" => "used");

	// constructs class instance
	public function __construct() {
		
	}

	// returns map array
	public function getMapArray() {
		return $this->mapArray;
	}

}

?>
