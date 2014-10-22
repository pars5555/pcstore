<?php

require_once (CLASSES_PATH . "/dal/dto/AbstractDto.class.php");

/**
 * CategoryHierarchyDto class is extended class from AbstractDto.
 *
 * @author	Vahagn Sookiasian
 */
class ServiceCompaniesPriceListDto extends AbstractDto {

    // Map of DB value to Field value
    protected $mapArray = array("id" => "id", "service_company_id" => "serviceCompanyId", "file_name" => "fileName", "file_ext" => "fileExt",
        "upload_date_time" => "uploadDateTime", "uploader_type" => "uploaderType", "uploader_id" => "uploaderId",
        "company_name" => "companyName"
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
