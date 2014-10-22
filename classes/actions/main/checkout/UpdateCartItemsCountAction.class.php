<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/CustomerCartManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class UpdateCartItemsCountAction extends GuestAction {

	public function service() {

		$customer = $this->getCustomer();
		$customerEmail = strtolower($customer->getEmail());
		$customerCartManager = CustomerCartManager::getInstance($this->config, $this->args);
		$totalCount = $customerCartManager->getCustomerCartTotalCount($customerEmail);
		$jsonArr = array('status' => "ok", "cart_items_count" => $totalCount);
		echo json_encode($jsonArr);
		return true;
	}

	public function getRequestGroup() {
		return RequestGroups::$userCompanyRequest;
	}

}

?>