<?php

require_once (CLASSES_PATH . "/actions/company/CompanyAction.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class ChangeItemDealerPriceAmdAction extends CompanyAction {

	public function service() {

		$item_id = $this->secure($_REQUEST["item_id"]);
		$dealer_price_amd = intval($this->secure($_REQUEST["dealer_price_amd"]));

		$itemManager = ItemManager::getInstance($this->config, $this->args);
		$itemDto = $itemManager->selectByPK($item_id);

		if ($itemDto != null) {
			$dealer_price_usd = $itemManager->exchangeFromAMDToUSD($dealer_price_amd);
			$itemManager->changeItemDealerPrice($item_id, $dealer_price_usd);
			$itemManager->changeItemDealerPriceAmd($item_id, $dealer_price_amd);
			$itemDto = $itemManager->selectByPK($item_id);
			$jsonArr = array('status' => "ok", "item_id" => $item_id, "dealer_price_amd" => $itemDto->getDealerPriceAmd(), "dealer_price" => $itemDto->getDealerPrice());
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