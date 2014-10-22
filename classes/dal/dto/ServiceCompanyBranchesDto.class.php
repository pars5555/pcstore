<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * AdminDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class ServiceCompanyBranchesDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id", "service_company_id" => "serviceCompanyId", "street" => "street", "region" => "region", "zip" => "zip",
        "lat" => "lat", "lng" => "lng", "phones" => "phones", "working_days" => "workingDays",
        "working_hours" => "workingHours", "created_at" => "createdAt"
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
