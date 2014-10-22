<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/OnlineUsersManager.class.php");
require_once (CLASSES_PATH . "/actions/main/UploadCompanyPriceAction.class.php");


/**
 * @author Vahagn Sookiasian
 */
class LoginAction extends GuestAction {

    public function service() {
        $userManager = new UserManager($this->config, $this->args);
        if (isset($_REQUEST['login_type'])) {
            $json_profile = $_REQUEST['json_profile'];
            $social_user_id = $this->secure($_REQUEST['social_user_id']);
            $first_name = $this->secure($_REQUEST['first_name']);
            $last_name = $this->secure($_REQUEST['last_name']);

            $custDto = $userManager->getUserByEmail($social_user_id);
            if (!isset($custDto)) {
                $userId = $userManager->createUser($social_user_id, uniqid(), $first_name, '', $last_name, $_REQUEST['login_type']);
                $userManager->setActive($userId);
                $userManager->setUserSocialProfile($userId, $json_profile);
                $custDto = $userManager->getUserByEmail($social_user_id);

                //bonus to inviter
                $invitation_code = $this->secure($_COOKIE["invc"]);
                $inviterId = $userManager->setSubUser($invitation_code, $userId);
                if ($inviterId > 0) {
                    $invbonus = intval($this->getCmsVar("bonus_points_for_every_accepted_invitation"));
                    $userManager->addUserPoints($inviterId, $invbonus, "$invbonus bonus for invitation accept from user number: $userId");
                }
            }
            $userType = UserGroups::$USER;
        } else {
            $email = strtolower($userManager->secure($_REQUEST["user_email"]));
            $pass = $userManager->secure($_REQUEST["user_pass"]);
            $custDto = $userManager->getCustomerByEmailAndPassword($email, $pass);
            $userType = $userManager->getCustomerType($email, $pass);

            if ($userType == UserGroups::$USER && $custDto->getActive() == 0) {
                $jsonArr = array('status' => "err", "errText" => sprintf($this->getPhrase(380), $custDto->getEmail()));
                echo json_encode($jsonArr);
                return false;
            }
        }

        if ($custDto) {
            if ($userType !== UserGroups::$ADMIN && $custDto->getBlocked() == 1) {
                $jsonArr = array('status' => "err", "errText" => $this->getPhraseSpan(411) . ' ' . $this->getCmsVar("pcstore_support_phone_number"));
                echo json_encode($jsonArr);
                return false;
            }
            $user = null;
            if ($userType === UserGroups::$ADMIN) {
                $user = new AdminUser($custDto->getId());
            } else if ($userType === UserGroups::$USER) {
                $user = new CustomerUser($custDto->getId());
                $this->setcookie('ul', $custDto->getLanguageCode());
            } else if ($userType === UserGroups::$COMPANY) {
                $user = new CompanyUser($custDto->getId());
                $companyManager = CompanyManager::getInstance($this->config, $this->args);
                $companyManager->updateCompanyRating($custDto);
                $this->setcookie('ul', $custDto->getLanguageCode());
            } else if ($userType === UserGroups::$SERVICE_COMPANY) {
                $user = new ServiceCompanyUser($custDto->getId());
                $companyManager = ServiceCompanyManager::getInstance($this->config, $this->args);
                $this->setcookie('ul', $custDto->getLanguageCode());
            }
            $user->setUniqueId($custDto->getHash());
            $this->sessionManager->setUser($user, true, true);
            $jsonArr = array('status' => "ok");
            echo json_encode($jsonArr);
            return true;
        } else {
            $jsonArr = array('status' => "err", "errText" => $this->getPhrase(412));
            echo json_encode($jsonArr);
            return false;
        }
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

}

?>