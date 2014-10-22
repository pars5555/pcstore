<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/NewsletterSubscribersManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class AddNewsletterSubscriberAction extends GuestAction {

    public function service() {
        $newsletterSubscribersManager = NewsletterSubscribersManager::getInstance($this->config, $this->args);
        $email = $this->secure($_REQUEST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error(array('message' => $this->getPhraseSpan(471)));
        }
        $dtos = $newsletterSubscribersManager->selectByField('email', $email);
        if (!empty($dtos)) {
            $this->error(array('message' => $this->getPhraseSpan(359)));
        }
        $newsletterSubscribersManager->addSubscriber($email);
        $this->ok(array('message' => $this->getPhraseSpan(606)));
    }

}

?>