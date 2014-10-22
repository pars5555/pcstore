<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class CompanyItemCheckListDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id", "company_id" => "companyId", "item_id" => "itemId", "from_name" => "fromName",
        "from_customer_type" => "fromCustomerType", "keep_anonymous" => "keepAnonymous",
        "sent_to_company_uid" => "sentToCompanyUid", "from_email" => "fromEmail", "item_availability" => "itemAvailability",
        "company_responded" => "companyResponded", "response_sent_to_customer_uid" => "responseSentToCustomerUid",
        "timestamp" => "timestamp", "deleted" => "deleted",
        "item_display_name" => "itemDisplayName"
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
