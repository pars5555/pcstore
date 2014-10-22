<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/CompanyDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class CompanyMapper extends AbstractMapper {

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
		$this->tableName = "companies";
	}

	/**
	 * Returns an singleton instance of this class
	 * @return
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new CompanyMapper();
		}
		return self::$instance;
	}

	/**
	 * @see AbstractMapper::createDto()
	 */
	public function createDto() {
		return new CompanyDto();
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

	public function getCompany($email, $password) {
		$sqlQuery = sprintf(self::$GET_COMPANY, $this->getTableName(), $email, $password);
		$result = $this->fetchRows($sqlQuery);
		if (count($result) === 1) {
			return $result[0];
		} else {
			return null;
		}
	}

	public static $GET_USER_REGION = "SELECT region FROM `%s` WHERE id = '%s'";

	public function getRegion($id) {
		$sqlQuery = sprintf(self::$GET_USER_REGION, $this->getTableName(), $id);
		$result = $this->fetchField($sqlQuery, 'region');
		return $result;
	}

	public static $GET_COMPANY_BY_EMAIL = "SELECT * FROM `%s` WHERE email = '%s'";

	public function getCompanyByEmail($email) {
		$sqlQuery = sprintf(self::$GET_COMPANY_BY_EMAIL, $this->getTableName(), $email);
		$result = $this->fetchRows($sqlQuery);
		if (count($result) === 1) {
			return $result[0];
		} else {
			return null;
		}
	}

	public static $GET_COMPANY_BY_ACCESS_KEY = "SELECT * FROM `%s` WHERE access_key = '%s'";

	public function getCompanyByAccessKey($access_key) {
		$sqlQuery = sprintf(self::$GET_COMPANY_BY_ACCESS_KEY, $this->getTableName(), $access_key);
		$result = $this->fetchRows($sqlQuery);
		if (count($result) === 1) {
			return $result[0];
		} else {
			return null;
		}
	}

	public function setAccessedUsersFieldValue($company_id, $value) {
		$this->updateTextField($company_id, 'accessed_users', $value);
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

	public static $GET_USER_COMPANIES = "SELECT `companies`.`id` , `companies`.`name` 
	  FROM `%s` INNER JOIN `company_dealers` ON `company_dealers`.`company_id` = `companies`.`id`
			WHERE `company_dealers`.`user_id` = %d %s
			ORDER BY `companies`.`passive` ,`companies`.`rating` DESC";

	public function getUserCompanies($user_id, $includePassiveCompanies = true) {
		$passiveSql = '';
		if (!$includePassiveCompanies) {
			$passiveSql = "AND `companies`.`passive` = 0";
		}
		$sqlQuery = sprintf(self::$GET_USER_COMPANIES, $this->getTableName(), $user_id, $passiveSql);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

	public static $GET_ALL_COMPANIES_EMAILS = "SELECT `email` FROM `%s` WHERE `hidden`=0";

	public function getAllCompaniesEmails() {
		$sqlQuery = sprintf(self::$GET_ALL_COMPANIES_EMAILS, $this->getTableName());
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

	public static $GET_COMPANY_AND_BRANCHES = "SELECT `%s`.*,`company_branches`.`id` as  `branch_id`,
													`company_branches`.`street` as  `street`,
													`company_branches`.`region` as  `region`,
													`company_branches`.`zip` as  `zip`,
													`company_branches`.`phones` as  `phones`,
													`company_branches`.`lng` as  `lng`,
													`company_branches`.`lat` as  `lat`,
													`company_branches`.`working_hours` as  `working_hours`,
													`company_branches`.`working_days` as  `working_days`
FROM `%s` LEFT JOIN `company_branches` ON  `company_branches`.`company_id` = `%s`.`id` WHERE `%s`.`id` = %d";

	public function getCompanyAndBranches($id) {
		$sqlQuery = sprintf(self::$GET_COMPANY_AND_BRANCHES, $this->getTableName(), $this->getTableName(), $this->getTableName(), $this->getTableName(), $id);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

	public static $GET_ALL_COMPANIES = "SELECT * FROM `%s` WHERE 1=1 %s %s ORDER BY `passive`,`rating` DESC";

	public function getAllCompanies($includePassiveCompanies, $includeHiddenCompanies) {


		$includeHiddenCompaniesSubQuery = 'AND `hidden`=0';
		if ($includeHiddenCompanies === true) {
			$includeHiddenCompaniesSubQuery = '';
		}

		$passiveSql = "AND `passive`=0";
		if ($includePassiveCompanies === true) {
			$passiveSql = "";
		}
		$sqlQuery = sprintf(self::$GET_ALL_COMPANIES, $this->getTableName(), $includeHiddenCompaniesSubQuery, $passiveSql);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

	public static $GET_SMS_COMPANIES = "SELECT * FROM `%s` WHERE `hidden`=0 AND `price_upload_sms_phone_number`<>'' AND FIND_IN_SET('%s' ,`interested_companies_ids_for_sms`)";

	public function getCompanyPriceInterestedForSmsCompanies($companyId) {
		$sqlQuery = sprintf(self::$GET_SMS_COMPANIES, $this->getTableName(), $companyId);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

	public static $GET_USER_COMPANIES_JOINED_FULL_INFO = "SELECT `%s`.*  ,
		`companies_price_list`.`id` as `price_id`, 
		`companies_price_list`.`file_ext` as `price_ext`, 
		`companies_price_list`.`upload_date_time` as `price_upload_date_time`, 
                        GROUP_CONCAT(`company_branches`.`street` separator ';') as `street`,
                        GROUP_CONCAT(`company_branches`.`phones`) as `phones`,
                        GROUP_CONCAT(`company_branches`.`zip`) as `zip`,
                        GROUP_CONCAT(`company_branches`.`region`) as `region`
                        FROM
                        `companies`  
						LEFT JOIN `company_branches` ON  `company_branches`.`company_id` = `%s`.`id`
						%s                        
                        INNER JOIN `company_dealers` 
                        ON `company_dealers`.`company_id` = `companies`.`id`  AND `companies`.`hidden` = 0 
                        INNER JOIN `users` 
                        ON `company_dealers`.`user_id` = `users`.`id` AND users.id= %d
					 LEFT JOIN `companies_price_list` ON `companies_price_list`.`file_ext` <> 'zip' AND `companies_price_list`.`company_id`= `companies`.`id`  AND `companies`.`hidden`=0                                                        
                        WHERE ISNULL(`companies_price_list`.`file_ext`) OR `companies_price_list`.`file_ext` != 'zip'
                        %s                    
                        %s                    
                       GROUP BY `companies`.`id`  ORDER BY `companies`.`passive` ,`companies`.`rating` DESC";

	public function getUserCompaniesJoindWithFullInfo($user_id, $show_only_last_hours, $searchText) {
		$subq1 = "";
		if ($show_only_last_hours > 0) {
			$subq1 = sprintf("AND `companies_price_list`.`upload_date_time` >= DATE_SUB('%s', INTERVAL %d HOUR)", date('Y-m-d H:i:s'), $show_only_last_hours);
		}
		$searchTextInPriceQuery = "";
		$searchTextInPriceQueryWhere = "";
		if (!empty($searchText)) {
			$searchTextInPriceQuery = "LEFT JOIN `price_texts` ON `price_texts`.`company_id` = `companies`.`id`";
			$searchText = trim(preg_replace('/ +/', ' ', $searchText));
			$searchTextArray = explode(' ', $searchText);
			foreach ($searchTextArray as $text) {
				$searchTextInPriceQueryWhere.= "AND `price_texts`.`price_text` LIKE '%" . $text . "%'";
			}
		}

		$sqlQuery = sprintf(self::$GET_USER_COMPANIES_JOINED_FULL_INFO, $this->getTableName(), $this->getTableName(), $searchTextInPriceQuery, $user_id, $subq1, $searchTextInPriceQueryWhere);
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

	public static $GET_ALL_COMPANIES_BY_PRICE_HOURS = "SELECT `companies`.*,		
		`companies_price_list`.`id` as `price_id`, 
		`companies_price_list`.`file_ext` as `price_ext`, 
		`companies_price_list`.`upload_date_time` as `price_upload_date_time`, 
                                                        GROUP_CONCAT(`company_branches`.`street` separator ';') as `street`,
                                                        GROUP_CONCAT(`company_branches`.`phones`) as `phones`,
                                                        GROUP_CONCAT(`company_branches`.`zip`) as `zip`,
                                                        GROUP_CONCAT(`company_branches`.`region`) as `region`
                                                        FROM `%s`
                                                        LEFT JOIN `company_branches` ON `company_branches`.`company_id` = `companies`.`id`
                                                        LEFT JOIN `companies_price_list` ON `companies_price_list`.`file_ext` <> 'zip' AND `companies_price_list`.`company_id`= `companies`.`id`
                                                        %s
                                                        WHERE 1=1 %s %s %s %s GROUP BY `companies`.`id` ORDER BY `passive`,`rating` DESC";

	public function getAllCompaniesByPriceHours($show_only_last_hours, $searchText, $includePassiveCompanies, $includeHiddenCompanies) {

		$includeHiddenCompaniesSubQuery = 'AND `hidden`=0';
		if ($includeHiddenCompanies === true) {
			$includeHiddenCompaniesSubQuery = '';
		}

		$passiveSql = '';
		if (!$includePassiveCompanies) {
			$passiveSql = "AND `passive`=0";
		}
		$searchTextInPriceQuery = "";
		$searchTextInPriceQueryWhere = "";
		if (!empty($searchText)) {
			$searchTextInPriceQuery = "LEFT JOIN `price_texts` ON `price_texts`.`company_id` = `companies`.`id`";
			$searchText = trim(preg_replace('/ +/', ' ', $searchText));
			$searchTextArray = explode(' ', $searchText);
			foreach ($searchTextArray as $text) {
				$searchTextInPriceQueryWhere.= "AND `price_texts`.`price_text` LIKE '%" . $text . "%'";
			}
		}

		$subq = "";
		if ($show_only_last_hours > 0) {
			$subq = sprintf(" AND `companies_price_list`.`upload_date_time` >= DATE_SUB('%s', INTERVAL %d HOUR)", date('Y-m-d H:i:s'), $show_only_last_hours);
		}

		$sqlQuery = sprintf(self::$GET_ALL_COMPANIES_BY_PRICE_HOURS, $this->getTableName(), $searchTextInPriceQuery, $subq, $searchTextInPriceQueryWhere, $includeHiddenCompaniesSubQuery, $passiveSql);
		//var_dump($sqlQuery);exit;
		$result = $this->fetchRows($sqlQuery);
		return $result;
	}

}

?>