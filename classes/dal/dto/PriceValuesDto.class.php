<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class PriceValuesDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id",
        "company_id" => "companyId",
        "price_index" => "priceIndex",
        "sheet_index" => "sheetIndex",
        "column_a" => "columnA",
        "column_b" => "columnB",
        "column_c" => "columnC",
        "column_d" => "columnD",
        "column_e" => "columnE",
        "column_f" => "columnF",
        "column_g" => "columnG",
        "column_h" => "columnH",
        "column_i" => "columnI",
        "column_j" => "columnJ",
        "column_k" => "columnK",
        "column_l" => "columnL",
        "column_m" => "columnM",
        "column_n" => "columnN",
        "column_o" => "columnO",
        "column_p" => "columnP",
        "column_q" => "columnQ",
        "column_r" => "columnR",
        "column_s" => "columnS",
        "column_t" => "columnT",
        "column_u" => "columnU",
        "column_v" => "columnV",
        "column_w" => "columnW",
        "column_x" => "columnX",
        "column_y" => "columnY",
        "column_z" => "columnZ"
    );

    // returns map array
    public function getMapArray() {
        return $this->mapArray;
    }

}

?>
