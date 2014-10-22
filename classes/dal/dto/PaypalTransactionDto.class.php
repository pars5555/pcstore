<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * PaypalTransactionDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class PaypalTransactionDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id", "order_id" => "orderId", "payment_received" => "paymentReceived", "message" => "message");

    // constructs class instance
    public function __construct() {
        
    }

    // returns map array
    public function getMapArray() {
        return $this->mapArray;
    }

}

?>
