<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/CompanyDealersDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class CompanyDealersMapper extends AbstractMapper {

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
        $this->tableName = "company_dealers";
    }

    /**
     * Returns an singleton instance of this class
     * @return
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new CompanyDealersMapper();
        }
        return self::$instance;
    }

    /**
     */
    public function createDto() {
        return new CompanyDealersDto();
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
    //////////////////////////////// custom functions //////////////////////////////////

    public static $GET_COMPANY_DEALERS = "SELECT `users`.`id` as `user_id`,`users`.`name` as `user_name` ,`users`.`lname` as `user_lname`, 
																				`users`.`telephone` as `user_telephone`, `users`.`email` as `user_email` FROM `users` 
																				INNER JOIN `%s` ON `%s`.`user_id` = `users`.`id` 
																					INNER JOIN `companies` ON `%s`.`company_id`=`companies`.`id` WHERE 
																					`companies`.`id`= %d AND `users`.`hidden` = 0 ORDER BY `%s`.`timestamp`";

    public function getCompanyDealersJoindWithUsersFullInfo($company_id) {
        $sqlQuery = sprintf(self::$GET_COMPANY_DEALERS, $this->getTableName(), $this->getTableName(), $this->getTableName(), $company_id, $this->getTableName());
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

    public static $GET_COMPANY_DEALERS_HIDDEN_INCLUDED = "
																				SELECT `users`.`id` as `user_id`,`users`.`name` as `user_name` ,`users`.`lname` as `user_lname`, 
																				`users`.`telephone` as `user_telephone`, `users`.`email` as `user_email` FROM  company_dealers INNER JOIN users 
   																			ON users.id = company_dealers.user_id INNER JOIN companies ON companies.id = company_dealers.`company_id` 
   																			WHERE company_id = %d";

    public function getCompanyDealersUsersFullInfoHiddenIncluded($company_id) {
        $sqlQuery = sprintf(self::$GET_COMPANY_DEALERS_HIDDEN_INCLUDED, $company_id);
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

    public static $GET_BY_COMPANY_AND_USER_ID = "SELECT * FROM `%s` WHERE user_id = %d AND company_id = %d";

    public function getByCompanyIdAndUserId($userId, $companyId) {

        $sqlQuery = sprintf(self::$GET_BY_COMPANY_AND_USER_ID, $this->getTableName(), $userId, $companyId);
        $result = $this->fetchRows($sqlQuery);

        if (count($result) === 1) {
            return $result[0];
        } else {
            return null;
        }
    }

    public static $GET_DEALERS_COUNT = "SELECT COUNT(`%s`.user_id) AS `dealers_count` FROM `%s` LEFT JOIN `companies` ON 
							`companies`.id = `%s`.`company_id` LEFT JOIN `users` ON `users`.`id` = `%s`.`user_id` WHERE %s `%s`.`company_id` = %d 
							GROUP BY  (`%s`.`company_id`)";

    public function getCompanyDealersCount($companyId, $includedHiddenDealers = false) {
        $subQ = "";
        if ($includedHiddenDealers === false) {
            $subQ = "`users`.`hidden` = 0 AND";
        }
        $sqlQuery = sprintf(self::$GET_DEALERS_COUNT, $this->getTableName(), $this->getTableName(), $this->getTableName(), $this->getTableName(), $subQ, $this->getTableName(), $companyId, $this->getTableName());
        $result = $this->fetchField($sqlQuery, 'dealers_count');
        return $result;
    }

    public static $GET_USER_COMPANIES_IDS = "SELECT GROUP_CONCAT(`company_id`) as `companies_ids` FROM `%s` WHERE `user_id` = %d GROUP BY `user_id`";

    public function getUserCompaniesIds($userId) {
        $sqlQuery = sprintf(self::$GET_USER_COMPANIES_IDS, $this->getTableName(), $userId);
        $result = $this->fetchField($sqlQuery, companies_ids);
        return $result;
    }

    public static $GET_AFTER_DATE = "SELECT * FROM `%s` WHERE `timestamp`>'%s'";

    public function getAfterGivenDatetime($companyId, $datetime) {
        $sqlQuery = sprintf(self::$GET_AFTER_DATE, $this->getTableName(), $datetime);
        if ($companyId > 0) {
            $sqlQuery .= sprintf(" AND `company_id` = %d", $companyId);
        }
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

    public static $GET_BY_COMPANY_ID = "SELECT * FROM `%s` WHERE `company_id` = %d";

    public function getByCompanyId($companyId) {
        $sqlQuery = sprintf(self::$GET_BY_COMPANY_ID, $this->getTableName(), $companyId);
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

    public static $GET_USER_COMPANIES_FULL = 'SELECT `%s`.*, `companies`.`name` AS `company_name` FROM `%s` LEFT JOIN `companies` ON companies.id=`%s`.`company_id`';

    public function getAllUsersCompaniesFull() {
        $sqlQuery = sprintf(self::$GET_USER_COMPANIES_FULL, $this->getTableName(), $this->getTableName(), $this->getTableName());
        $result = $this->fetchRows($sqlQuery);
        return $result;
    }

}

?>