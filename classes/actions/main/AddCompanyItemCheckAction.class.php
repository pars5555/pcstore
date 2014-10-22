<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyItemCheckListManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class AddCompanyItemCheckAction extends GuestAction {

	public function service() {

		$customer = $this->getCustomer();
		$userLevel = $this->sessionManager->getUser()->getLevel();
		if ($userLevel == UserGroups::$GUEST) {
			$jsonArr = array('status' => "err", "errText" => "Access denied!");
			echo json_encode($jsonArr);
			return false;
		}

		$item_id = $this->secure($_REQUEST['item_id']);
		$keep_anonymous = $this->secure($_REQUEST['keep_anonymous']);
		$from_email = $customer->getEmail();
		$from_name = $customer->getName();
		$from_customer_type = ($userLevel == UserGroups::$USER) ? 'user' : (($userLevel == UserGroups::$COMPANY) ? 'company' : 'admin');
		$itemManager = ItemManager::getInstance($this->config, $this->args);
		$itemDto = $itemManager->selectByPK($item_id);
		if (!$itemDto) {
			$jsonArr = array('status' => "err", "errText" => "Item doesn't exist!");
			echo json_encode($jsonArr);
			return false;
		}
		if ($itemDto->getItemAvailableTillDate() >= date('Y-m-d')) {
			$jsonArr = array('status' => "message", "item_available" => true);
			echo json_encode($jsonArr);
			return false;
		}
		if ($itemDto->getHidden() == 1) {
			$jsonArr = array('status' => "message", "item_not_available" => true);
			echo json_encode($jsonArr);
			return false;
		}

		$company_id = $itemDto->getCompanyId();
		$companyItemCheckListManager = CompanyItemCheckListManager::getInstance($this->config, $this->args);
		$companyItemCheckListManager->addCompanyItemCheckList($company_id, $item_id, $from_email, $from_name, $from_customer_type, $keep_anonymous);
		$jsonArr = array('status' => "ok");
		echo json_encode($jsonArr);
		return true;
	}

	public function getRequestGroup() {
		return RequestGroups::$userCompanyRequest;
	}

}

?>