<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/MandrillEmailSenderManager.class.php");
require_once (CLASSES_PATH . "/managers/UninterestingEmailsManager.class.php");
require_once (CLASSES_PATH . "/managers/NewsletterSubscribersManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class SendNewsletterAction extends AdminAction {

    public function service() {
        $mandrillEmailSenderManager = new MandrillEmailSenderManager($this->getCmsVar("mandrill_api_key_news"));

        $email_body_html = $_REQUEST['email_body_html'];
        if (empty($email_body_html)) {
            $this->error(array('errText' => 'email body is empty!'));
        }

        $fromEmail = $this->getCmsVar('pcstore_news_email');
        $fromName = 'Pcstore.am Newsletter!';


        $includeUsers = $_REQUEST['include_all_active_users'];
        $subject = 'Newsletter from PcStore.am!!!';

        $test = $_REQUEST['test'];
        if ($test == 1) {
            $testEmail = $_REQUEST['test_email'];
            $res = $mandrillEmailSenderManager->sendHtmlEmail($testEmail, $subject, $email_body_html, $fromEmail, $fromName);
            $this->ok(array('count' => 1));
        }

        $uninterestingEmailsManager = UninterestingEmailsManager::getInstance($this->config, $this->args);
        $newsletterSubscribersManager = NewsletterSubscribersManager::getInstance($this->config, $this->args);
        $emailsArray = $newsletterSubscribersManager->getAllSubscribers();
        $filteredEmailsArray = $uninterestingEmailsManager->removeUninterestingEmailsFromList($emailsArray);

        if ($includeUsers == 1) {
            $userManager = UserManager::getInstance($this->config, $this->args);
            $allUsers = $userManager->selectAll();
            foreach ($allUsers as $userDto) {
                if ($userDto->getAvtive() == 0) {
                    continue;
                }
                $userRealEmailAddress = strtolower($userManager->getRealEmailAddressByUserDto($userDto));
                if (filter_var($userRealEmailAddress, FILTER_VALIDATE_EMAIL)) {
                    $filteredEmailsArray[] = $userRealEmailAddress;
                }
            }
        }
        //for FB, Google and TWITTER users emails may be duplicated!!!! so used array_unique
        $recipients = array_unique($filteredEmailsArray);

        if (count($recipients) === 0) {
            $this->error(array('errText' => 'There is no any recipient!'));
        }
        $email_body_html .= '<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>';
        $email_body_html .= '<p style="font-size:10px"><a href="*|UNSUB:http://pcstore.am/unsubscriber|*">Click here to unsubscribe.</a></p>';
        $res = $mandrillEmailSenderManager->sendHtmlEmail($recipients, $subject, $email_body_html, $fromEmail, $fromName);
        $this->ok(array('count' => count($recipients)));
    }

}

?>