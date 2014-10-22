<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class CompanyExtendedProfileDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id",
        "company_id" => "companyId",
        "dealer_emails" => "dealerEmails",
        "unsubscribed_emails" => "unsubscribedEmails",
        "from_email" => "fromEmail",
        "price_email_subject" => "priceEmailSubject",
        "price_email_body" => "priceEmailBody");

    // constructs class instance
    public function __construct() {
        
    }

    // returns map array
    public function getMapArray() {
        return $this->mapArray;
    }

}

?>
