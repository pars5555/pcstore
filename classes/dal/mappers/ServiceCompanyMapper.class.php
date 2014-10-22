<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/ServiceCompanyDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class ServiceCompanyMapper extends AbstractMapper {

    /**
     * @var table name in DB
     */
    private $tableName;

    /**
     * @var an instance of this class
     */
    private static $instance = null;

    /**
     * Initializes DBMS pointers and table name private class member.
     */
    function __construct() {
        // Initialize the dbms pointer.
        AbstractMapper::__construct();

        // Initialize table name.
        $this->tableName = "service_companies";
    }

    /**
     * Returns an singleton instance of this class
     * @return
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new ServiceCompanyMapper();
        }
        return self::$instance;
    }

    /**
     * @see AbstractMapper::createDto()
     */
    public function createDto() {
        return new ServiceCompanyDto();
    }

    /**
     * @see AbstractMapper::getPKFieldName()
     */
    public function getPKFieldName() {
        return "id";
    }

    /**
     * @see AbstractMapper::getTableName()
     */
    public function getTableName() {
        return $this->tableName;
    }

    //////////////////////////////// custom functions //////////////////////////////////

    public static $GET_COMPANY = "SELECT * FROM `%s` WHERE email = '%s' AND BINARY password = '%s'";

    public function getServiceCompany($email, $password) {
        $sqlQuery = sprintf(self::$GET_COMPANY, $this->getTableName(), $email, $password);
        $result = $this->fetchRows($sqlQuery);
        if (count($result) === 1) {
            return $result[0];
        } else {
            return null;
        }
    }

    public static $VALIDATE_COMPANY = "SELECT * FROM `%s` WHERE id = %d AND hash = '%s'";

    public function validate($id, $hash) {
        $sqlQuery = sprintf(self::$VALIDATE_COMPANY, $this->getTableName(), $id, $hash);

        $result = $this->fetchRows($sqlQuery);

        if (count($result) === 1) {
            return $result[0];
        } else {
            return null;
        }
    }

    public static $GET_ALL_COMPANIES_EMAILS = "SELECT `email` FROM `%s`";

    public function getAllServiceCompaniesEmails() {
        $sqlQuery = sprintf(self::$GET_ALL_COMPANIES_EMAILS, $this->getTableName());
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

    public static $GET_COMPANY_AND_BRANCHES = "SELECT `%s`.*,`service_company_branches`.`id` as  `branch_id`,
													`service_company_branches`.`street` as  `street`,
													`service_company_branches`.`region` as  `region`,
													`service_company_branches`.`zip` as  `zip`,
													`service_company_branches`.`phones` as  `phones`,
													`service_company_branches`.`lng` as  `lng`,
													`service_company_branches`.`lat` as  `lat`,
													`service_company_branches`.`working_hours` as  `working_hours`,
													`service_company_branches`.`working_days` as  `working_days`
FROM `%s` LEFT JOIN `service_company_branches` ON  `service_company_branches`.`service_company_id` = `%s`.`id` WHERE `%s`.`id` = %d";

    public function getServiceCompanyAndBranches($id) {
        $sqlQuery = sprintf(self::$GET_COMPANY_AND_BRANCHES, $this->getTableName(), $this->getTableName(), $this->getTableName(), $this->getTableName(), $id);
        $result = $this->fetchRows($sqlQuery);

        return $result;
    }

    public static $GET_SMS_COMPANIES = "SELECT * FROM `%s` WHERE `hidden`=0 AND `price_upload_sms_phone_number`<>'' AND 
                                                    FIND_IN_SET('%s' ,`interested_companies_ids_for_sms`)";

    public function getServiceCompanyPriceInterestedForSmsCompanies($companyId) {
        $sqlQuery = sprintf(self::$GET_SMS_COMPANIES, $this->getTableName(), $companyId);
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

    public static $GET_ALL_COMPANIES_BY_PRICE_HOURS = "SELECT `service_companies`.*,		
		`service_companies_price_list`.`id` as `price_id`, 
		`service_companies_price_list`.`file_ext` as `price_ext`, 
		`service_companies_price_list`.`upload_date_time` as `price_upload_date_time`, 
                                                        GROUP_CONCAT(`service_company_branches`.`street` separator ';') as `street`,
                                                        GROUP_CONCAT(`service_company_branches`.`phones`) as `phones`,
                                                        GROUP_CONCAT(`service_company_branches`.`zip`) as `zip`,
                                                        GROUP_CONCAT(`service_company_branches`.`region`) as `region`
                                                        FROM `%s`
                                                        LEFT JOIN `service_company_branches` ON `service_company_branches`.`service_company_id` = `service_companies`.`id`
                                                        LEFT JOIN `service_companies_price_list` ON `service_companies_price_list`.`file_ext` <> 'zip' AND `service_companies_price_list`.`service_company_id`= `service_companies`.`id`                                                        
                                                        GROUP BY `service_companies`.`id`";

    public function getAllServiceCompaniesWithBranches() {
        $sqlQuery = sprintf(self::$GET_ALL_COMPANIES_BY_PRICE_HOURS, $this->getTableName());
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

}

?>