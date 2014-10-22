<?php

require_once (CLASSES_PATH . "/actions/company/CompanyAction.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class ChangeItemVatPriceAction extends CompanyAction {

	public function service() {

		$item_id = $this->secure($_REQUEST["item_id"]);
		$vat_price = $this->secure($_REQUEST["vat_price"]);

		$itemManager = ItemManager::getInstance($this->config, $this->args);
		$itemDto = $itemManager->selectByPK($item_id);

		if ($itemDto != null) {
			$itemManager->changeItemVatPrice($item_id, $vat_price);
			$itemManager->changeItemVatPriceAmd($item_id, 0);
			$itemDto = $itemManager->selectByPK($item_id);
			$jsonArr = array('status' => "ok", "item_id" => $item_id, "vat_price" => $itemDto->getVatPrice());
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