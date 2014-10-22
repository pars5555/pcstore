<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/AdminManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class RemoveCompanyItemAction extends AdminAction {

    public function service() {

        $itemId = $this->secure($_REQUEST["item_id"]);
        $companyId = $this->secure($_REQUEST["company_id"]);

        $itemManager = ItemManager::getInstance($this->config, $this->args);
        $itemDto = $itemManager->selectByPK($itemId);

        if ($this->getUserLevel() !== UserGroups::$ADMIN && !($this->getUserLevel() === UserGroups::$COMPANY && $itemDto->getCompanyId() === $companyId)) {
            $jsonArr = array('status' => "err", "errText" => "System Error: Access denied!");
            echo json_encode($jsonArr);
            return false;
        }

        $itemManager->deleteItemWithPictures($itemId);
        $jsonArr = array('status' => "ok", "message" => "ok");
        echo json_encode($jsonArr);
        return true;
    }

}

?>