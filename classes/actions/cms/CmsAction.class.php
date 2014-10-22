<?php

require_once (CLASSES_PATH . "/actions/Action.class.php");

/**
 * General parent action for all AdminAction classes.
 *
 */
abstract class CmsAction extends Action {

    public function __construct() {
        
    }

    public function onNoAccess() {
        $this->redirect('admin/login');
    }

    public function getRequestGroup() {
        return RequestGroups::$adminRequest;
    }

}

?>