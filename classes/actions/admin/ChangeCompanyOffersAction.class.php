<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class ChangeCompanyOffersAction extends AdminAction {

	public function service() {

		$companyManager = new CompanyManager($this->config, $this->args);
		$company_id = intval($this->secure($_REQUEST['company_id']));
		$company_offers = addslashes($this->secure($_REQUEST['offers']));
		$companyManager->setCompanyOffers($company_id, $company_offers);
		$jsonArr = array('status' => "ok");
		echo json_encode($jsonArr);
		return true;
	}

}

?>