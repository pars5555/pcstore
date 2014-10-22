<?php

require_once (CLASSES_PATH . "/users/UserGroups.class.php");
require_once (CLASSES_PATH . "/framework/AbstractUser.class.php");

class GuestUser extends AbstractUser {

    public function __construct() {
        
    }

    public function getLevel() {
        return UserGroups::$GUEST;
    }

    public function validate() {
        return true;
    }

    public function getId() {
        return null;
    }

}

?>