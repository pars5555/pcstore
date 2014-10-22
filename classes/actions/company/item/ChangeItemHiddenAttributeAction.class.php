<?php

require_once (CLASSES_PATH . "/actions/company/CompanyAction.class.php");
require_once (CLASSES_PATH . "/managers/AdminManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class ChangeItemHiddenAttributeAction extends CompanyAction {

    public function service() {

        $item_id = $this->secure($_REQUEST["item_id"]);
        $item_hidden = $this->secure($_REQUEST["item_hidden"]);

        $itemManager = ItemManager::getInstance($this->config, $this->args);
        $itemDto = $itemManager->selectByPK($item_id);

        if ($itemDto != null) {
            $itemManager->changeItemHiddenAttributeValue($item_id, $item_hidden);
            $itemDto = $itemManager->selectByPK($item_id);
            $jsonArr = array('status' => "ok", "item_id" => $item_id, "is_item_hidden" => $itemDto->getHidden(), "is_item_available" => $itemManager->isItemAvailable($itemDto));
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