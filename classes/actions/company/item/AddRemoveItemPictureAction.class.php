<?php

require_once (CLASSES_PATH . "/actions/company/CompanyAction.class.php");
require_once (CLASSES_PATH . "/managers/AdminManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/util/ImageThumber.php");

/**
 * @author Vahagn Sookiasian
 */
class AddRemoveItemPictureAction extends CompanyAction {

    public function service() {
        $itemManager = ItemManager::getInstance($this->config, $this->args);

        $action = $this->secure($_REQUEST["action"]);
        $itemId = intval($_REQUEST["item_id"]);
        switch ($action) {
            case 'add':
                $this->addItemPicture($itemId);
                break;
            case 'delete':
                $pictureIndex = intval($_REQUEST['picture_index']);
                $itemManager->removeItemPicturesByPicturesIndexes($itemId, $pictureIndex);
                $this->ok(array("item_id" => $itemId));
            case 'make_default':
                $pictureIndex = intval($_REQUEST['picture_index']);
                $itemManager->setItemDefaultPicture($itemId, $pictureIndex);
                $this->ok(array("item_id" => $itemId));
                break;
        }
    }

    private function addItemPicture($itemId) {
        $itemManager = ItemManager::getInstance($this->config, $this->args);
        $logoCheck = $this->checkInputFile('item_picture');
        if ($logoCheck != 'ok') {
            $jsonArr = array('status' => "error", "errText" => $logoCheck);
            echo "<script>var l= new parent.ngs.AddRemoveItemPictureAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return false;
        }

        $itemDto = $itemManager->selectByPK($itemId);
        $ret = $itemManager->addPicture($itemDto, 'item_picture');
        if ($ret === true) {
            $jsonArr = array('status' => "ok", "item_id" => $itemId);
            echo "<script>var l= new parent.ngs.AddRemoveItemPictureAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return true;
        } else {
            $jsonArr = array('status' => "error", "errText" => $ret);
            echo "<script>var l= new parent.ngs.AddRemoveItemPictureAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return false;
        }
    }

}

?>