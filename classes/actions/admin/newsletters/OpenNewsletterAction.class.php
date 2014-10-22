<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/NewslettersManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class OpenNewsletterAction extends AdminAction {

    public function service() {
        $newslettersManager = NewslettersManager::getInstance($this->config, $this->args);
        $newsletter_id = $this->secure($_REQUEST['newsletter_id']);
        $byTitle = $newslettersManager->selectByPK($newsletter_id);
        if (isset($byTitle)) {
            $this->ok(array('html' => $byTitle->getHtml(),
                'include_all_active_users' => $byTitle->getIncludeAllActiveUsers(),
                'newsletter_title' => $byTitle->getTitle()
            ));
        } else {
            $this->error(array('message' => "Newsletter doesn't exists!"));
        }
    }

}

?>