<?php

require_once (CLASSES_PATH . "/loads/cms/CmsLoad.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class OnlineUsersLoad extends CmsLoad {

    public function load() {
       
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/cms/online_users.tpl";
    }

}

?>