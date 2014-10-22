<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class CreditOrdersDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id", "order_id" => "orderId", "deposit" => "deposit", "credit_supplier_id" => "creditSupplierId", "credit_months" => "creditMonths", "annual_interest_percent" => "annualInterestPercent", "monthly_payment" => "monthlyPayment");

    // constructs class instance
    public function __construct() {
        
    }

    // returns map array
    public function getMapArray() {
        return $this->mapArray;
    }

}

?>
