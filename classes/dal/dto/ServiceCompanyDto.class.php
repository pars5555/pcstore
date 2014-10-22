<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class ServiceCompanyDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id", "short_name" => "shortName", "name" => "name", "email" => "email",
        "price_emails_keywords" => "priceEmailsKeywords",
        "registered_date" => "registeredDate", "password" => "password", "hash" => "hash",
        "last_ping" => "lastPing", "url" => "url", "services_description_html" => "servicesDescriptionHtml",
        "access_key" => "accessKey",
        "has_price" => "hasPrice",
        "blocked" => "blocked",
        "language_code" => "languageCode", "sound_on" => "soundOn",
        // to left join service_company_branches table
        "branch_id" => "branchId", "street" => "street", "region" => "region", "zip" => "zip", "lat" => "lat", "lng" => "lng",
        "working_days" => "workingDays", "working_hours" => "workingHours", "phones" => "phones",
        // to left join service_companies_price_list table
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
