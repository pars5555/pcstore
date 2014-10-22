<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/NewsletterSubscribersManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class DeleteNewsletterSubscriberAction extends GuestAction {

    public function service() {
        unset($_SESSION['nl_error_message']);
        unset($_SESSION['nl_success_message']);
        $newsletterSubscribersManager = NewsletterSubscribersManager::getInstance($this->config, $this->args);
        $email = $this->secure($_REQUEST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['nl_error_message'] = 'Invalid email address!';
            $this->redirect('unsubscribe');
            exit;
        }
        $dtos = $newsletterSubscribersManager->selectByField('email', $email);
        if (empty($dtos)) {
            $_SESSION['nl_error_message'] = $email . ' is not registerd in our newsletter recipient list!';
            $this->redirect('unsubscribe');
            exit;
        }

        $newsletterSubscribersManager->removeSubscriberEmail($email);
        $_SESSION['nl_success_message'] = "You have successfully unsubscribed from our list!";
        $this->redirect('unsubscribe');
    }

}

?>