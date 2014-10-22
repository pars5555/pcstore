<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class AdminDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id", "title" => "title", "email" => "email", "password" => "password", "hash" => "hash", "type" => "type",
        "last_ping" => "lastPing", "number_to_receive_sms_on_price_upload" => "numberToReceiveSmsOnPriceUpload", "sound_on" => "soundOn", "price_group" => "priceGroup");

    // constructs class instance
    public function __construct() {
        
    }

    // returns map array
    public function getMapArray() {
        return $this->mapArray;
    }

    public function getCustomerContactNameForEmail() {
        return $this->getTitle();
    }

}

?>
