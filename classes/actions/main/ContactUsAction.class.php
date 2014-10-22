<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/EmailSenderManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class ContactUsAction extends GuestAction {

    public function service() {
        $emailSenderManager = new EmailSenderManager('gmail');
        $email = strtolower($this->secure($_REQUEST['email']));
        $msg = $this->secure($_REQUEST['msg']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $jsonArr = array('status' => "err", "message" => $this->getPhraseSpan(471));
            echo json_encode($jsonArr);
            return false;
        }
        $pcstore_contact_us_email = $this->getCmsVar('pcstore_contact_us_email');
        $subject = "Message To PcStore from " . $email;
        $templateId = "contact_us";
        $params = array("msg" => $msg);
        if ($this->getUserLevel() !== UserGroups::$GUEST) {
            $fromName = $this->getCustomer()->getName() . ' (' . $this->getUserLevelString() . '-' . $this->getUserId() . ')';
        } else {
            $fromName = $email;
        }
        $emailSenderManager->sendEmail('contactus', $pcstore_contact_us_email, $subject, $templateId, $params, $email, $fromName);
        $jsonArr = array('status' => "ok", "message" => $this->getPhrase(438));
        echo json_encode($jsonArr);
        return true;
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

}

?>