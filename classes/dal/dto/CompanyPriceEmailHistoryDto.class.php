<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class CompanyPriceEmailHistoryDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array(
        "id" => "id",
        "company_id" => "companyId",
        "company_type" => "companyType",
        "from_email" => "fromEmail",
        "to_emails" => "toEmails",
        "body" => "body",
        "subject" => "subject",
        "attachments" => "attachments",
        "datetime" => "datetime"
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
