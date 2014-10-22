<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class NewsletterDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id", "title" => "title", "html" => "html",
        "recipients" => "recipients",
        "sender_email_ids" => "senderEmailIds",
        "include_all_active_users" => "includeAllActiveUsers"
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
