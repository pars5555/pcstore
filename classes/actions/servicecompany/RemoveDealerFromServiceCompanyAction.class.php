<?php

require_once (CLASSES_PATH . "/actions/servicecompany/ServiceCompanyAction.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyDealersManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class RemoveDealerFromServiceCompanyAction extends ServiceCompanyAction {

	public function service() {

		$serviceCompanyDealersManager = new ServiceCompanyDealersManager($this->config, $this->args);
		$userId = $serviceCompanyDealersManager->secure($_REQUEST["user_id"]);
		$serviceCompanyId = $this->sessionManager->getUser()->getId();


		if ($serviceCompanyDealersManager->getByCompanyIdAndUserId($userId, $serviceCompanyId)) {
			$serviceCompanyDealersManager->removeUserFromCompany($userId, $serviceCompanyId);
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