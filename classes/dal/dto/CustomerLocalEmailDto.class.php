<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class CustomerLocalEmailDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id", "customer_email" => "customerEmail", "from_email" => "fromEmail", "to_emails" => "toEmails",
        "subject" => "subject", "body" => "body", "datetime" => "datetime", "replied" => "replied",
        "read_status" => "readStatus",
        "trash" => "trash",
        "deleted" => "deleted",
        "folder" => "folder",
        //for joining
        "customer_title" => "customerTitle"
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
