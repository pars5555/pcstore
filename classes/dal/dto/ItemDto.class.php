<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");
require_once (CLASSES_PATH . "/managers/pcc_managers/PcConfiguratorManager.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class ItemDto extends AbstractDto {

	// Map of DB value to Field value
	protected $mapArray = array("id" => "id", "display_name" => "displayName", "short_description" => "shortDescription", "full_description" => "fullDescription",
		"warranty" => "warranty", "dealer_price" => "dealerPrice", "vat_price" => "vatPrice",
		"dealer_price_amd" => "dealerPriceAmd", "vat_price_amd" => "vatPriceAmd",             
                "list_price_amd" => "listPriceAmd", "company_id" => "companyId",
		"hidden" => "hidden", "model" => "model", "brand" => "brand", "categories_names" => "categoriesNames", "categories_ids" => "categoriesIds",
		"item_available_till_date" => "itemAvailableTillDate", "shows_count" => "showsCount", "order_index_in_price" => "orderIndexInPrice", "pictures_count" => "picturesCount",
		"created_date" => "createdDate", "updated_date" => "updatedDate", "created_by_email" => "createdByEmail", "updated_by_email" => "updatedByEmail",
		 "image1"=>"image1", "image2"=>"image2",
		//for company table join
		"company_name" => "companyName", "company_rating" => "companyRating", "company_phones" => "companyPhones",
		//for search_item result info (value of this field is 1 if user is dealer of this item's company, null otherwise)
		"is_dealer_of_this_company" => "isDealerOfThisCompany", "is_company_online" => "isCompanyOnline", "customer_item_price" => "customerItemPrice",
		"customer_vat_item_price" => "customerVatItemPrice",
		//for PC Configurator if item is capatible
		"pcc_item_compatible" => "pccItemCompatible",
		//for PC Configurator Select Case Load
		PcConfiguratorManager::PCC_MB_SOCKET_COMPATIBLE_DB => PcConfiguratorManager::PCC_MB_SOCKET_COMPATIBLE_FN,
		PcConfiguratorManager::PCC_CASE_SIZE_COMPATIBLE_DB => PcConfiguratorManager::PCC_CASE_SIZE_COMPATIBLE_FN,
		PcConfiguratorManager::PCC_RAM_TYPE_COMPATIBLE_DB => PcConfiguratorManager::PCC_RAM_TYPE_COMPATIBLE_FN,
		PcConfiguratorManager::PCC_RAM_COUNT_COMPATIBLE_DB => PcConfiguratorManager::PCC_RAM_COUNT_COMPATIBLE_FN,
		PcConfiguratorManager::PCC_CPU_SOCKET_COMPATIBLE_DB => PcConfiguratorManager::PCC_CPU_SOCKET_COMPATIBLE_FN,
		PcConfiguratorManager::PCC_GRAPHICS_INTERFACE_COMPATIBLE_DB => PcConfiguratorManager::PCC_GRAPHICS_INTERFACE_COMPATIBLE_FN,
		PcConfiguratorManager::PCC_SATA_STORAGE_INTERFACE_COMPATIBLE_DB => PcConfiguratorManager::PCC_SATA_STORAGE_INTERFACE_COMPATIBLE_FN,
		PcConfiguratorManager::PCC_ATA_STORAGE_INTERFACE_COMPATIBLE_DB => PcConfiguratorManager::PCC_ATA_STORAGE_INTERFACE_COMPATIBLE_FN,
		PcConfiguratorManager::PCC_SATA_STORAGE_COUNT_COMPATIBLE_DB => PcConfiguratorManager::PCC_SATA_STORAGE_COUNT_COMPATIBLE_FN,
		PcConfiguratorManager::PCC_ATA_STORAGE_COUNT_COMPATIBLE_DB => PcConfiguratorManager::PCC_ATA_STORAGE_COUNT_COMPATIBLE_FN,
		PcConfiguratorManager::PCC_COOLER_SOCKET_COMPATIBLE_DB => PcConfiguratorManager::PCC_COOLER_SOCKET_COMPATIBLE_FN,
		PcConfiguratorManager::PCC_STORAGE_INTERFACE_COMPATIBLE_DB => PcConfiguratorManager::PCC_STORAGE_INTERFACE_COMPATIBLE_FN
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
