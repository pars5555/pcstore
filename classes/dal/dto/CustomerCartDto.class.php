<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class CustomerCartDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id", "customer_email" => "customerEmail", "item_id" => "itemId",
        "bundle_id" => "bundleId", "is_system_bundle" => "isSystemBundle",
        "last_dealer_price" => "lastDealerPrice", "count" => "count", "discount" => "discount",
        "cached_item_display_name" => "cachedItemDisplayName",
        //for join the items table
        "item_available" => "itemAvailable",
        "item_display_name" => "itemDisplayName", "item_dealer_price" => "itemDealerPrice", "item_vat_price" => "itemVatPrice", "item_categories_ids" => "itemCategoriesIds",
        "item_brand" => "itemBrand", "item_available_till_date" => "itemAvailableTillDate", "item_hidden" => "itemHidden", "item_company_id" => "itemCompanyId",
        //for join the bundles_items table
        "bundle_display_name_id" => "bundleDisplayNameId", "bundle_item_id" => "bundleItemId",
        "bundle_cached_item_display_name" => "bundleCachedItemDisplayName", "bundle_item_count" => "bundleItemCount",
        "bundle_item_last_dealer_price" => "bundleItemLastDealerPrice", "special_fee_id" => "specialFeeId", "special_fee_dynamic_price" => "specialFeeDynamicPrice",
        //for company_dealers table
        "is_dealer_of_this_company" => "isDealerOfThisCompany",
        //for calculated customer item price
        "customer_item_price" => "CustomerItemPrice", "customer_vat_item_price" => "customerVatItemPrice",
        //for join the special_fees table
        "special_fee_price" => "specialFeePrice", "special_fee_description_text_id" => "specialFeeDescriptionTextId"
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
