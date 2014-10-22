<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/NewslettersManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class SaveNewsletterAction extends AdminAction {

    public function service() {
        $newslettersManager = NewslettersManager::getInstance($this->config, $this->args);
        $title = $this->secure($_REQUEST['title']);
        $html = $_REQUEST['html'];
        $include_all_active_users = $this->secure($_REQUEST['include_all_active_users']);
        $byTitle = $newslettersManager->getByTitle($title);
        if (isset($byTitle)) {
            $newslettersManager->saveNewsletter($byTitle->getId(), $html, $include_all_active_users);
        } else {
            $newslettersManager->addNewsletter($title, $html, $include_all_active_users);
        }
        $this->ok();
    }

}

?>