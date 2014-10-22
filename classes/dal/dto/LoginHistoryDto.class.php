<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class LoginHistoryDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id", "email" => "email", "customer_type" => "customerType", "datetime" => "datetime",
        "ip" => "ip", "host" => "host", "country" => "country", "browser_name" => "browserName", "browser_version" => "browserVersion", "browser_platform" => "browserPlatform");

    // constructs class instance
    public function __construct() {
        
    }

    // returns map array
    public function getMapArray() {
        return $this->mapArray;
    }

}

?>
