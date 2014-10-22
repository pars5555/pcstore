<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * CategoryHierarchyDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class CategoryHierarchyDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id", "category_id" => "categoryId", "child_id" => "childId", "sort_index" => "sortIndex", "display_name" => "displayName");

    // constructs class instance
    public function __construct() {
        
    }

    // returns map array
    public function getMapArray() {
        return $this->mapArray;
    }

}

?>
