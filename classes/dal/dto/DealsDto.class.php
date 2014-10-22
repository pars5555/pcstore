<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class DealsDto extends AbstractDto {

	// Map of DB value to Field value
	protected $mapArray = array("id" => "id", "item_id" => "itemId", "daily_deal" => "dailyDeal",
		"price_amd" => "priceAmd", "promo_code" => "promoCode", "date" => "date", "enable" => "enable",
		"start_time" => "startTime", "duration_minutes" => "durationMinutes");

	// constructs class instance
	public function __construct() {
		
	}

	// returns map array
	public function getMapArray() {
		return $this->mapArray;
	}

}

?>
