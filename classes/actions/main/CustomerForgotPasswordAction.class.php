<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/EmailSenderManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class CustomerForgotPasswordAction extends GuestAction {

    public function service() {

        $userManager = new UserManager($this->config, $this->args);

        $email = strtolower($this->secure($_REQUEST["email"]));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $jsonArr = array('status' => "err", "errText" => $this->getPhrase(471));
            echo json_encode($jsonArr);
            return false;
        }

        $customer = $userManager->getCustomerByEmail($email);
        if ($customer != null) {
            $emailSenderManager = new EmailSenderManager('gmail');
            $customerEmail = $customer->getEmail();
            $userName = $customer->getName();
            $password = $customer->getPassword();

            $subject = "Your PcStore Password!";
            $templateId = "customer_forgot_password";
            $params = array("name" => $userName, "password" => $password);
            $emailSenderManager->sendEmail('support', $customerEmail, $subject, $templateId, $params);
            $jsonArr = array('status' => "ok", "message" => "Your password sent to your " . $email . " email.\nPlease check your email.");
            echo json_encode($jsonArr);
            return true;
        } else {
            $jsonArr = array('status' => "err", "errText" => $this->getPhrase(381));
            echo json_encode($jsonArr);
            return false;
        }
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

}

?>