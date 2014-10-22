<?php

require_once (CLASSES_PATH . "/loads/admin/AdminLoad.class.php");

class CreateNewAlertLoad extends AdminLoad {

    public function load() {
        
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/admin/sysconfig/create_new_alert.tpl";
    }

}

?>