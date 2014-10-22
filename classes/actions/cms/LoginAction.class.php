<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/AdminManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class LoginAction extends GuestAction {

    public function service() {
        $login = strtolower($this->secure($_REQUEST['login']));
        $password = $this->secure($_REQUEST['password']);
        $adminManager = new AdminManager($this->config, $this->args);
        $adminDto = $adminManager->getAdminByEmailAndPassword($login, $password);
        if (isset($adminDto)) {
            $user = new AdminUser($adminDto->getId());
            $user->setUniqueId($adminDto->getHash());
            $this->sessionManager->setUser($user, true, true);
        }
        $this->redirect('admin');
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

}

?>