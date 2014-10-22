<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/AdminManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class SetPriceGroupAction extends AdminAction {

    public function service() {
        $adminManager = AdminManager::getInstance($this->config, $this->args);
        $price_group = $_REQUEST['price_group'];
        $adminManager->setPriceGroup($this->getUserId(), $price_group);
        $this->ok();
    }

}

?>