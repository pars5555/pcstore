<?php

require_once (CLASSES_PATH . "/loads/cms/CmsLoad.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class AdminsLoad extends CmsLoad {

    public function load() {
       
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/cms/admins.tpl";
    }

}

?>