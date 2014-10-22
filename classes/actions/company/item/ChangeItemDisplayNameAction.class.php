<?php

require_once (CLASSES_PATH . "/actions/company/CompanyAction.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class ChangeItemDisplayNameAction extends CompanyAction {

	public function service() {

		$item_id = $this->secure($_REQUEST["item_id"]);
		$display_name = $this->secure($_REQUEST["display_name"]);

		$itemManager = ItemManager::getInstance($this->config, $this->args);
		$itemDto = $itemManager->selectByPK($item_id);

		if ($itemDto != null) {
			$itemManager->changeItemDisplayNamePrice($item_id, $display_name);
			$itemDto = $itemManager->selectByPK($item_id);
			$jsonArr = array('status' => "ok", "item_id" => $item_id, "display_name" => $itemDto->getDisplayName());
			echo json_encode($jsonArr);
			return true;
		} else {
			$jsonArr = array('status' => "err", "errText" => "System Error: Item does not exist!");
			echo json_encode($jsonArr);
			return false;
		}
	}

}

?>