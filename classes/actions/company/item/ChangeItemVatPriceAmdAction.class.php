<?php

require_once (CLASSES_PATH . "/actions/company/CompanyAction.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class ChangeItemVatPriceAmdAction extends CompanyAction {

    public function service() {

        $item_id = $this->secure($_REQUEST["item_id"]);
        $vat_price_amd = intval($this->secure($_REQUEST["vat_price_amd"]));

        $itemManager = ItemManager::getInstance($this->config, $this->args);
        $itemDto = $itemManager->selectByPK($item_id);

        if ($itemDto != null) {
            $vat_price_usd = $itemManager->exchangeFromAMDToUSD($vat_price_amd);
            $itemManager->changeItemVatPrice($item_id, $vat_price_usd);
            $itemManager->changeItemVatPriceAmd($item_id, $vat_price_amd);
            $itemDto = $itemManager->selectByPK($item_id);
            $jsonArr = array('status' => "ok", "item_id" => $item_id, "vat_price_amd" => $itemDto->getVatPriceAmd(), "vat_price" => $itemDto->getVatPrice());
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