<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class ItemWarrantyDto extends AbstractDto {

	// Map of DB value to Field value
	protected $mapArray = array("id" => "id", "company_id" => "companyId", "item_category" => "itemCategory", "serial_number" => "serialNumber",
		"buyer" => "buyer", "customer_warranty_period" => "customerWarrantyPeriod", "customer_warranty_start_date" => "customerWarrantyStartDate",
		"supplier" => "supplier", "supplier_warranty_start_date" => "supplierWarrantyStartDate", "supplier_warranty_period" => "supplierWarrantyPeriod");

	// constructs class instance
	public function __construct() {
		
	}

	// returns map array
	public function getMapArray() {
		return $this->mapArray;
	}

}

?>
