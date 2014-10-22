<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/EmailSenderManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class RegisterNewUserAction extends GuestAction {

    public function service() {

        $userManager = new UserManager($this->config, $this->args);

        $email = strtolower($this->secure($_REQUEST["email"]));
        $name = $this->secure($_REQUEST["name"]);
        $phone = $this->secure($_REQUEST["phone"]);
        $pass = $this->secure($_REQUEST["pass"]);

        $invitation_code = $this->secure($_COOKIE["invc"]);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $jsonArr = array('status' => "err", "errText" => $this->getPhraseSpan(471));
            echo json_encode($jsonArr);
            return false;
        }

        $custDto = $userManager->getCustomerByEmail($email);
        if ($custDto) {
            $jsonArr = array('status' => "err", "errText" => $this->getPhraseSpan(359));
            echo json_encode($jsonArr);
            return false;
        }

        if (!$userManager->checkPassword($pass)) {
            $jsonArr = array('status' => "err", "errText" => $this->getPhraseSpan(358));
            echo json_encode($jsonArr);
            return false;
        }

        if (!$name || strlen($name) == 0) {
            $jsonArr = array('status' => "err", "errText" => $this->getPhraseSpan(356));
            echo json_encode($jsonArr);
            return false;
        }

        if ($phone != null) {
            if (strpos($phone, ',') !== false) {
                $jsonArr = array('status' => "err", "errText" => $this->getPhraseSpan(521));
                echo json_encode($jsonArr);
                return false;
            }
        }

        $userId = $userManager->createUser($email, $pass, $name, $phone);
        $userManager->setSubUser($invitation_code, $userId, $email);

        $userDto = $userManager->selectByPK($userId);
        //sending activation email using $userDto->getActivationCode();		
        $emailSenderManager = new EmailSenderManager('gmail');
        $username = $name;
        $subject = "PcStore Activation!";
        $activation_code = $userDto->getActivationCode();
        $template = "account_activation";
        $params = array("user_name" => $username, "activation_code" => $activation_code);
        $emailSenderManager->sendEmail('registration', $email, $subject, $template, $params);
        $jsonArr = array('status' => "ok");
        echo json_encode($jsonArr);
        return true;
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

}

?>