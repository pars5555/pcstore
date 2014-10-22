<?php

require_once (CLASSES_PATH . "/loads/company/CompanyLoad.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ItemPicturesLoad extends CompanyLoad {

    public function load() {
        $item_id = intval($_REQUEST['item_id']);
        $itemManager = ItemManager::getInstance($this->config, $this->args);
        $itemDto = $itemManager->selectByPK($item_id);
        $this->addParam('itemDto', $itemDto);
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/company/stock/item_pictures.tpl";
    }

}

?>