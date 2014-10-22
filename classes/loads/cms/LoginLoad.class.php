<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class LoginLoad extends GuestLoad {

    public function load() {
        
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/cms/login.tpl";
    }

}

?>