<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class CreditSuppliersDto extends AbstractDto {

	// Map of DB value to Field value
	protected $mapArray = array("id" => "id", "display_name_id" => "displayNameId", "minimum_credit_amount" => "minimumCreditAmount", "annual_commision" => "annualCommision", "annual_interest_percent" => "annualInterestPercent",
		"possible_credit_months" => "possibleCreditMonths", "commission" => "commission", "visible" => "visible");

	// constructs class instance
	public function __construct() {
		
	}

	// returns map array
	public function getMapArray() {
		return $this->mapArray;
	}

}

?>
