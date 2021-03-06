<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class CmsSearchRequestsDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id", "search_text" => "searchText", "datetime" => "datetime",
        "win_uid" => "winUid",
        "search_count" => "searchCount");

    // constructs class instance
    public function __construct() {
        
    }

    // returns map array
    public function getMapArray() {
        return $this->mapArray;
    }

}

?>
