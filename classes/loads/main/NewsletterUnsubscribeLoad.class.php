<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class NewsletterUnsubscribeLoad extends GuestLoad {

    public function load() {
        if (isset($_SESSION['nl_error_message'])) {
            $error_message = $this->secure($_SESSION['nl_error_message']);
        }
        if (isset($_SESSION['nl_success_message'])) {
            $success_message = $this->secure($_SESSION['nl_success_message']);
        }
        $this->addParam('error_message', $error_message);
        $this->addParam('success_message', $success_message);
    }

    public function getDefaultLoads($args) {
        $loads = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/newsletter_unsubscribe.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

    protected function logRequest() {
        return false;
    }

}

?>