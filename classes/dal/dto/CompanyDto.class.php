<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class CompanyDto extends AbstractDto {

	// Map of DB value to Field value
	protected $mapArray = array("id" => "id", "name" => "name", "short_name" => "shortName", "email" => "email", 
            "price_emails_keywords"=>"priceEmailsKeywords",
            "access_key" => "accessKey",
		"rating" => "rating", "registered_date" => "registeredDate", "password" => "password", "hash" => "hash",
		"last_ping" => "lastPing", "url" => "url", "last_sms_validation_code" => "lastSmsValidationCode", "offers" => "offers",
		"hidden" => "hidden", "blocked" => "blocked",
		"interested_companies_ids_for_sms" => "interestedCompaniesIdsForSms",
		"sms_receive_time_start" => "smsReceiveTimeStart",
		"sms_to_duration_minutes" => "smsToDurationMinutes",
		"sms_receive_weekdays" => "smsReceiveWeekdays",
		"price_upload_sms_phone_number" => "priceUploadSmsPhoneNumber",
		"passive" => "passive",
		"receive_email_on_stock_update" => "receiveEmailOnStockUpdate",
		"language_code" => "languageCode", "sound_on" => "soundOn",
		// to left join company_branches table
		"branch_id" => "branchId", "street" => "street", "region" => "region", "zip" => "zip", "lat" => "lat", "lng" => "lng",
		"working_days" => "workingDays", "working_hours" => "workingHours", "phones" => "phones",
		// to left join companies_price_list table
		"price_id" => "priceId",
		"price_ext" => "priceExt",
		"price_upload_date_time" => "priceUploadDateTime"
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
