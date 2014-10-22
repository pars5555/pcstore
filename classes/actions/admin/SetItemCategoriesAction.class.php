<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class SetItemCategoriesAction extends AdminAction {

    public function service() {
        $itemId = $this->secure($_REQUEST['item_id']);
        $root = $this->secure($_REQUEST['root_category_id']);
        $subCategories = $this->secure($_REQUEST['sub_categories_ids']);
        $subCategoriesArray = explode(',', trim($subCategories, ','));
        $categoriesIdsArray = $subCategoriesArray;
        array_unshift($categoriesIdsArray, $root);
        $itemManager = ItemManager::getInstance($this->config, $this->args);
        $itemManager->setItemCategories($itemId, $categoriesIdsArray);
        $this->ok();
    }

}

?>