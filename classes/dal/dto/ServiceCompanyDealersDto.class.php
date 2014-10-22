<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class ServiceCompanyDealersDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id", "service_company_id" => "serviceCompanyId", "user_id" => "userId", "timestamp" => "timestamp",
        //for users table
        "user_name" => "userName", "user_lname" => "userLastName", "user_email" => "userEmail", "user_telephone" => "userPhones",
        //for companies table
        "company_name" => "companyName", "company_email" => "companyEmail",
        "company_telephone" => "companyPhones", "company_url" => "companyUrl", "company_address" => "companyAddress",
        "company_rating" => "companyRating", "company_offers" => "companyOffers",
        "company_passive" => "companyPassive",
        "company_interested_companies_ids_for_sms" => "companyInterestedCompaniesIdsForSms"
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
