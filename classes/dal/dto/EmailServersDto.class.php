<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class EmailServersDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id", "name" => "name", "smtp_host" => "smtpHost", "smtp_port" => "smtpPort",
        "day_max_sending_limit" => "dayMaxSendingLimit",
        "smtp_auth" => "smtpAuth", "email_default_postfix" => "emailDefaultPostfix", "image_file_name" => "imageFileName",
        "display_name" => "displayName");

    // constructs class instance
    public function __construct() {
        
    }

    // returns map array
    public function getMapArray() {
        return $this->mapArray;
    }

}

?>
