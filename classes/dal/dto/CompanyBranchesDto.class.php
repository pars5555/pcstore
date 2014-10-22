<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class CompanyBranchesDto extends AbstractDto {

	// Map of DB value to Field value
	protected $mapArray = array("id" => "id", "company_id" => "companyId", "street" => "street", "region" => "region", "zip" => "zip",
		"lat" => "lat", "lng" => "lng", "phones" => "phones", "working_days" => "workingDays",
		"working_hours" => "workingHours", "confirmed_by" => "confirmedBy", "created_at" => "createdAt"
	);

	// constructs class instance
	public function __construct() {
		
	}

	// returns map array
	public function getMapArray() {
		return $this->mapArray;
	}

	public function getCustomerContactNameForEmail() {
		return $this->getName();
	}

}

?>
