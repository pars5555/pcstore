<?php

require_once (CLASSES_PATH . "/loads/company/CompanyLoad.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyDealersManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class DealersListLoad extends CompanyLoad {

	public function load() {
		$companyDealersManager = CompanyDealersManager::getInstance($this->config, $this->args);
		$companyId = $this->sessionManager->getUser()->getId();
		$dealers = $companyDealersManager->getCompanyDealersJoindWithUsersFullInfo($companyId);
		$this->addParam('dealers', $dealers);
	}

	
	public function getTemplate() {
		return TEMPLATES_DIR . "/company/dealers_list.tpl";
	}


}

?>