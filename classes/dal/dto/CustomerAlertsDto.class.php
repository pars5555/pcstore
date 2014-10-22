<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class CustomerAlertsDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id", "type" => "type", "title" => "title", "message" => "message", "to_login" => "toLogin"
        , "delivered_to_uid" => "deliveredToUid", "metadata" => "metadata", "datetime" => "datetime");

    // constructs class instance
    public function __construct() {
        
    }

    // returns map array
    public function getMapArray() {
        return $this->mapArray;
    }

}

?>
