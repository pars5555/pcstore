<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class ImportItemsTempDto extends AbstractDto {

	// Map of DB value to Field value
	protected $mapArray = array("id" => "id", "login" => "login", "display_name" => "displayName", "original_display_name" => "originalDisplayName",
		"model" => "model", "original_model" => "originalModel", "brand" => "brand",
		"original_brand" => "originalBrand",
		"dealer_price" => "dealerPrice",
		"original_dealer_price" => "originalDealerPrice", "dealer_price_amd" => "dealerPriceAmd",
		"original_dealer_price_amd" => "originalDealerPriceAmd",
		"vat_price" => "vatPrice",
		"original_vat_price" => "originalVatPrice", "vat_price_amd" => "vatPriceAmd",
		"original_vat_price_amd" => "originalVatPriceAmd",
		"warranty_months" => "warrantyMonths", "original_warranty" => "originalWarranty",
		"matched_item_id" => "matchedItemId",
		"simillar_item_id" => "simillarItemId",
		"root_category_id" => "rootCategoryId",
		"sub_categories_ids" => "subCategoriesIds",
		"short_spec" => "shortSpec",
		"full_spec" => "fullSpec"
	);

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
