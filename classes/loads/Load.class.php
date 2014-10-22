<?php

// Make includes here.
require_once(CLASSES_PATH . "/framework/AbstractLoad.class.php");
require_once(CLASSES_PATH . "/users/UserGroups.class.php");


/* !	\brief	Brief description.
 * 					General parent action for all XWL actions
 * 					Making login operation.
 */

abstract class Load extends AbstractLoad {

    //! A constructor.
    public function __construct() {
        
    }

//	
//--responsible for reloading	
    protected function isMain() {
        return false;
    }

}

?>