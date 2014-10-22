<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class OrderDetailsDto extends AbstractDto {

	// Map of DB value to Field value
	protected $mapArray = array("id" => "id", "order_id" => "orderId", "item_id" => "itemId", "bundle_id" => "bundleId",
		"special_fee_id" => "specialFeeId", "item_display_name" => "itemDisplayName", "bundle_display_name_id" => "bundleDisplayNameId",
		"special_fee_display_name_id" => "specialFeeDisplayNameId", "bundle_count" => "bundleCount", "item_count" => "itemCount",
		"customer_item_price" => "customerItemPrice", "item_dealer_price" => "itemDealerPrice", "special_fee_price" => "specialFeePrice",
		"item_company_id" => "itemCompanyId", "is_dealer_of_item" => "isDealerOfItem", "discount" => "discount",
		"customer_bundle_price_amd" => "customerBundlePriceAmd", "customer_bundle_price_usd" => "customerBundlePriceUsd");

	// constructs class instance
	public function __construct() {
		
	}

	// returns map array
	public function getMapArray() {
		return $this->mapArray;
	}

}

?>
