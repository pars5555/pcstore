<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/UserPendingSubUsersManager.class.php");
require_once (CLASSES_PATH . "/managers/EmailSenderManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class InviteUserAction extends GuestAction {

    public function service() {

        $userManager = UserManager::getInstance($this->config, $this->args);
        $userPendingSubUsersManager = new UserPendingSubUsersManager($this->config, $this->args);
        $emailSenderManager = new EmailSenderManager('gmail');
        $userId = $this->getUserId();
        $user = $userManager->selectByPK($userId);
        $subUsersRegistrationCode = $user->getSubUsersRegistrationCode();
        if ($subUsersRegistrationCode == null || strlen($subUsersRegistrationCode) == 0) {
            $subUsersRegistrationCode = uniqid();
            $userManager->setSubUsersRegistrationCode($userId, $subUsersRegistrationCode);
        }

        if (isset($_REQUEST["emails"])) {
            $emails = $this->secure($_REQUEST["emails"]);
            $emailsArray = explode(',', $emails);
            $validEmailToBeInvited = array();
            foreach ($emailsArray as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $customerByEmail = $userManager->getCustomerByEmail($email);
                    if (!isset($customerByEmail)) {
                        $byUserIdAndPendingSubUserEmail = $userPendingSubUsersManager->getByUserIdAndPendingSubUserEmail($userId, $email);
                        if (!isset($byUserIdAndPendingSubUserEmail)) {
                            $userPendingSubUsersManager->addPendingSubUserEmailToUser($email, $userId);
                            $validEmailToBeInvited[] = $email;
                        }
                    }
                }
            }
            if (count($validEmailToBeInvited) > 0) {
                $from = $userManager->getRealEmailAddress($userId);
                $username = $user->getName();
                $subject = $username . " invites you to join PcStore!";
                $template = "user_to_user_invitation";
                $params = array("user_name" => $username, "invitation_code" => $subUsersRegistrationCode);
                $fromName = $user->getName() . ' ' . $user->getLastName();
                $emailSenderManager->sendEmail('news', $validEmailToBeInvited, $subject, $template, $params, $from, $fromName);
            }
            $this->ok(array("message" => $this->getPhraseSpan(603)));
        } elseif (isset($_REQUEST["user_email"])) {
            $pendingUserEmail = $this->secure($_REQUEST["user_email"]);
            if (filter_var($pendingUserEmail, FILTER_VALIDATE_EMAIL)) {
                if ($userManager->getCustomerByEmail($pendingUserEmail)) {
                    $this->error(array("errText" => $this->getPhraseSpan(359)));
                }
                if (!$userPendingSubUsersManager->getByUserIdAndPendingSubUserEmail($userId, $pendingUserEmail)) {
                    $userPendingSubUsersManager->addPendingSubUserEmailToUser($pendingUserEmail, $userId);

                    //sending invitation email to user

                    $from = $userManager->getRealEmailAddress($userId);
                    $username = $user->getName();
                    $subject = $username . " invites you to join PcStore!";
                    $template = "user_to_user_invitation";
                    $params = array("user_name" => $username, "invitation_code" => $subUsersRegistrationCode);
                    $fromName = $user->getName() . ' ' . $user->getLastName();
                    $emailSenderManager->sendEmail('news', $pendingUserEmail, $subject, $template, $params, $from, $fromName);
                    $this->ok(array("message" => $this->getPhraseSpan(603)));
                } else {
                    $this->error(array("errText" => $this->getPhraseSpan(605)));
                }
            } else {
                $this->error(array("errText" => $this->getPhraseSpan(471)));
            }
        } elseif (isset($_REQUEST["invitation_id"])) {
            $invitationId = intval($_REQUEST["invitation_id"]);
            $from = $userManager->getRealEmailAddress($userId);
            $username = $user->getName();
            $subject = $username . " invites you to join PcStore!";
            $template = "user_to_user_invitation";
            $params = array("user_name" => $username, "invitation_code" => $subUsersRegistrationCode);
            $fromName = $user->getName() . ' ' . $user->getLastName();
            $dto = $userPendingSubUsersManager->selectByPK($invitationId);
            $pendingSubUserEmail = $dto->getPendingSubUserEmail();
            $emailSenderManager->sendEmail('news', $pendingSubUserEmail, $subject, $template, $params, $from, $fromName);
            $dto->setLastSent(date('Y-m-d H:i:s'));
            $userPendingSubUsersManager->updateByPk($dto);
            $this->ok(array("message" => $this->getPhraseSpan(603)));
        }
    }

    public function getRequestGroup() {
        return RequestGroups::$userRequest;
    }

}

?>