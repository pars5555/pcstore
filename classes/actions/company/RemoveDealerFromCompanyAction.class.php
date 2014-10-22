<?php

require_once (CLASSES_PATH . "/actions/company/CompanyAction.class.php");
require_once (CLASSES_PATH . "/managers/CompanyDealersManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class RemoveDealerFromCompanyAction extends CompanyAction {

	public function service() {

		$companyDealersManager = new CompanyDealersManager($this->config, $this->args);
		$userId = $companyDealersManager->secure($_REQUEST["user_id"]);
		$companyId = $this->sessionManager->getUser()->getId();


		if ($companyDealersManager->getByCompanyIdAndUserId($userId, $companyId)) {
			$companyDealersManager->removeUserFromCompany($userId, $companyId);
			$jsonArr = array('status' => "ok", "message" => "Dealer successfully removed from your company!");
			echo json_encode($jsonArr);
			return true;
		} else {
			$jsonArr = array('status' => "err", "errText" => "System Error: Dealer dosn't exist in your list!");
			echo json_encode($jsonArr);
			return false;
		}
	}

}

?>