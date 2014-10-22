<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ImportGoogleContactsLoad extends GuestLoad {

    public function load() {
        $emails = $this->secure($_REQUEST['emails']);
        $emailsArray = array();
        if (!empty($emails)) {
            $emailsArray = explode(',', $emails);
        }
        $this->addParam('emails', $emailsArray);
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/import_google_contacts.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$userRequest;
    }

}

?>