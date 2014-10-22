<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");

/**
 * General parent action for all AdminAction classes.
 *
 */
abstract class AdminAction extends GuestAction {

    public function getRequestGroup() {
        return RequestGroups::$adminRequest;
    }

}

?>