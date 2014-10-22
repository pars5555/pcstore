<?php

require_once(CLASSES_PATH . "/users/UserGroups.class.php");
require_once(CLASSES_PATH . "/users/AuthenticateUser.class.php");
require_once(CLASSES_PATH . "/managers/CompanyManager.class.php");

/**
 * User class for system administrators.
 * 
 * @author Vahagn Sookiasian, Yerem Khalatyan
 */
class CompanyUser extends AuthenticateUser {

	/**
	 * Creates en instance of admin user class and
	 * initializes class members necessary for validation. 
	 * 
	 * @param object $adminId
	 * @return 
	 */
	public function __construct($id) {
		parent::__construct($id);
		$this->setCookieParam("ut", UserGroups::$COMPANY);
		$this->companyManager = CompanyManager::getInstance(null, null);
	}

	public function setUniqueId($uniqueId, $updateDb = true) {

		if ($updateDb) {
			$uniqueId = $this->companyManager->updateCompanyHash($this->getId());
		}
		$this->setCookieParam("uh", $uniqueId);
	}

	/**
	 * Validates user credentials 
	 * 
	 * @return TRUE - if validation passed, and FALSE - otherwise
	 */
	public function validate($uniqueId = false) {
		if (!$uniqueId) {
			$uniqueId = $this->getUniqueId();
		}
		return $this->companyManager->validate($this->getId(), $uniqueId);
	}

}

?>