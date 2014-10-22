<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class BundleItemsDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id", "bundle_id" => "bundleId", "item_id" => "itemId", "special_fee_id" => "specialFeeId", "special_fee_dynamic_price" => "specialFeeDynamicPrice",
        "item_last_dealer_price" => "ItemLastDealerPrice", "cached_item_display_name" => "cachedItemDisplayName",
        "bundle_display_name_id" => "bundleDisplayNameId", "item_count" => "itemCount"
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
