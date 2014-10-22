<?php

require_once (CLASSES_PATH . "/loads/admin/AdminLoad.class.php");
require_once (CLASSES_PATH . "/managers/OnlineUsersManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class AdminStatisticsLoad extends AdminLoad {

    public function load() {
        $onlineUsersManager = OnlineUsersManager::getInstance($this->config, $this->args);
        $ac = $onlineUsersManager->selectAll();
        $this->addParam('onlineusers', $ac);
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/admin/admin_statistics.tpl";
    }

}

?>