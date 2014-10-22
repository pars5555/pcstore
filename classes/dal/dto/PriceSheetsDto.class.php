<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class PriceSheetsDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id",
        "company_id" => "companyId",
        "price_index" => "priceIndex",
        "sheet_title" => "sheetTitle",
        "visible" => "visible"
    );

    // returns map array
    public function getMapArray() {
        return $this->mapArray;
    }

}

?>
