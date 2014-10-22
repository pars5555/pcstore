<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class PriceTranslationsDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id", "language_code" => "languageCode"
        , "phrase" => "phrase", "phrase_english" => "phraseEnglish");

    // returns map array
    public function getMapArray() {
        return $this->mapArray;
    }

}

?>
