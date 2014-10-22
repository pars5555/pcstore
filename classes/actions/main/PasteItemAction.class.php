<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class PasteItemAction extends GuestAction {

	public function service() {

		if (!isset($_REQUEST['item_position'])) {
			$jsonArr = array('status' => "err", "errText" => "System Error: Item position is not set!");
			echo json_encode($jsonArr);
			return false;
		}

		$item_position = $this->secure($_REQUEST['item_position']);
		if (!is_numeric($item_position)) {
			$jsonArr = array('status' => "err", "errText" => "System Error: Item position should be positive number!");
			echo json_encode($jsonArr);
			return false;
		}


		if (!$_COOKIE['copied_item_id']) {
			$jsonArr = array('status' => "err", "errText" => "System Error: There is no copied item!");
			echo json_encode($jsonArr);
			return false;
		}
		$copied_item_id = $this->secure($_COOKIE['copied_item_id']);
		$itemManager = ItemManager::getInstance($this->config, $this->args);
		$itemDto = $itemManager->selectByPK($copied_item_id);
		if (!isset($itemDto)) {
			$jsonArr = array('status' => "err", "errText" => "Error: Item does not exist! (id:$copied_item_id)");
			echo json_encode($jsonArr);
			return false;
		}


		if (!isset($_REQUEST['selected_company_id'])) {
			$jsonArr = array('status' => "err", "errText" => "System Error: Company id is not set!");
			echo json_encode($jsonArr);
			return false;
		}

		$selected_company_id = $this->secure($_REQUEST['selected_company_id']);
		$companyManager = CompanyManager::getInstance($this->config, $this->args);
		$companyDto = $companyManager->selectByPK($selected_company_id);
		if (!isset($companyDto)) {
			$jsonArr = array('status' => "err", "errText" => "System Error: Company is not exist!");
			echo json_encode($jsonArr);
			return false;
		}
		$itemManager->copyItem($copied_item_id, $selected_company_id, $item_position);

		$jsonArr = array('status' => "ok");
		echo json_encode($jsonArr);
		return true;
	}

	public function getRequestGroup() {
		return RequestGroups::$companyRequest;
	}

}

?>