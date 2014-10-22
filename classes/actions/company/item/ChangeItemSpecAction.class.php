<?php

require_once (CLASSES_PATH . "/actions/company/CompanyAction.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class ChangeItemSpecAction extends CompanyAction {

    public function service() {

        $item_id = $this->secure($_REQUEST["item_id"]);
        $spec = $_REQUEST["spec"];
        $shortSpec = false;
        if (isset($_REQUEST["short_spec"])) {
            $shortSpec = true;
        }


        $itemManager = ItemManager::getInstance($this->config, $this->args);
        $itemDto = $itemManager->selectByPK($item_id);

        if ($itemDto != null) {
            $itemManager->updateTextField($item_id, $shortSpec ? "short_description" : "full_description", $spec);
            $jsonArr = array('status' => "ok", "item_id" => $item_id);
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