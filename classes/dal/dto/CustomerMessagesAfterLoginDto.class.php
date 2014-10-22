<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class CustomerMessagesAfterLoginDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id", "from_email" => "fromEmail", "email" => "email", "title_formula" => "titleFormula", "message_formula" => "messageFormula",
        "shows_count" => "showsCount", "showed_count" => "showedCount", "type" => "type", "datetime" => "datetime");

    // constructs class instance
    public function __construct() {
        
    }

    // returns map array
    public function getMapArray() {
        return $this->mapArray;
    }

}

?>
