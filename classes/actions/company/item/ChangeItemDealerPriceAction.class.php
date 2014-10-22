<?php

require_once (CLASSES_PATH . "/actions/company/CompanyAction.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class ChangeItemDealerPriceAction extends CompanyAction {

	public function service() {

		$item_id = $this->secure($_REQUEST["item_id"]);
		$dealer_price = $this->secure($_REQUEST["dealer_price"]);

		$itemManager = ItemManager::getInstance($this->config, $this->args);
		$itemDto = $itemManager->selectByPK($item_id);

		if ($itemDto != null) {
			$itemManager->changeItemDealerPrice($item_id, $dealer_price);
			$itemManager->changeItemDealerPriceAmd($item_id, 0);
			$itemDto = $itemManager->selectByPK($item_id);
			$jsonArr = array('status' => "ok", "item_id" => $item_id, "dealer_price" => $itemDto->getDealerPrice());
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