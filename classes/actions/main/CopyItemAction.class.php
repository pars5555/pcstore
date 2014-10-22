<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class CopyItemAction extends GuestAction {

    public function service() {

        if (!isset($_REQUEST['item_id'])) {
            $jsonArr = array('status' => "err", "errText" => "Error: Item id must be specified!");
            echo json_encode($jsonArr);
            return false;
        }
        $item_id = $this->secure($_REQUEST['item_id']);

        $itemManager = ItemManager::getInstance($this->config, $this->args);
        $itemDto = $itemManager->selectByPK($item_id);
        if (!isset($itemDto)) {
            $jsonArr = array('status' => "err", "errText" => "Error: Item does not exist! (id:$item_id)");
            echo json_encode($jsonArr);
            return false;
        }
        $this->setCookie('copied_item_id', $item_id);
        $jsonArr = array('status' => "ok");
        echo json_encode($jsonArr);
        return true;
    }

    public function getRequestGroup() {
        return RequestGroups::$companyRequest;
    }

}

?>