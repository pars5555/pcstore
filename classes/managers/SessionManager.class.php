<?php

require_once (CLASSES_PATH . "/framework/AbstractSessionManager.class.php");
require_once (CLASSES_PATH . "/RequestGroups.class.php");
require_once (CLASSES_PATH . "/framework/exceptions/InvalidUserException.class.php");
require_once (CLASSES_PATH . "/framework/exceptions/RedirectException.class.php");
require_once (CLASSES_PATH . "/users/UserGroups.class.php");
require_once (CLASSES_PATH . "/users/GuestUser.class.php");
require_once (CLASSES_PATH . "/users/AdminUser.class.php");
require_once (CLASSES_PATH . "/users/CustomerUser.class.php");
require_once (CLASSES_PATH . "/users/CompanyUser.class.php");
require_once (CLASSES_PATH . "/users/ServiceCompanyUser.class.php");
require_once (CLASSES_PATH . "/users/ServiceCompanyUser.class.php");

require_once (CLASSES_PATH . "/managers/UserManager.class.php");

/**
 *
 * @author Vahagn Sookiasian, Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class SessionManager extends AbstractSessionManager {

    private $user = null;
    private $config;

    public function __construct($config) {
        $this->config = $config;
    }

    public function getUser() {

        $status = $this->setOnlineStatus();

        if ($this->user != null) {
            return $this->user;
        }
        // for test
        $this->user = new GuestUser();

        try {
            if (isset($_COOKIE["ut"])) {
                if ($_COOKIE["uh"] && $_COOKIE["ud"]) {
                    if ($_COOKIE["ut"] == UserGroups::$USER) {
                        $user = new CustomerUser($_COOKIE["ud"]);
                    } else if ($_COOKIE["ut"] == UserGroups::$ADMIN) {
                        $user = new AdminUser($_COOKIE["ud"]);
                    } else if ($_COOKIE["ut"] == UserGroups::$COMPANY) {
                        $user = new CompanyUser($_COOKIE["ud"]);
                    } else if ($_COOKIE["ut"] == UserGroups::$SERVICE_COMPANY) {
                        $user = new ServiceCompanyUser($_COOKIE["ud"]);
                    }
                }
            }
            if ($user && $user->validate($_COOKIE["uh"])) {
                $this->user = $user;
            }

            if ($this->user && $this->user->getLevel() != UserGroups::$GUEST) {
                $hash = $_COOKIE["uh"];
                if (!$status) {
                    $hash = $this->updateUserHash($_COOKIE["ud"]);
                    $this->updateUserUniqueId($user);
                }
                $this->user->setUniqueId($hash, false);
            }
        } catch (InvalidUserException $ex) {
            
        }

        return $this->user;
    }

    public function validateRequest($request, $user) {
        if ($user->getLevel() == UserGroups::$ADMIN) {
            return true;
        }
        if ($request->getRequestGroup() == RequestGroups::$guestRequest) {
            return true;
        }

        if ($request->getRequestGroup() == RequestGroups::$userRequest && $user->getLevel() == UserGroups::$USER) {
            return true;
        }

        if ($request->getRequestGroup() == RequestGroups::$companyRequest && $user->getLevel() == UserGroups::$COMPANY) {
            return true;
        }

        if ($request->getRequestGroup() == RequestGroups::$serviceCompanyRequest && $user->getLevel() == UserGroups::$SERVICE_COMPANY) {
            return true;
        }

        if ($request->getRequestGroup() == RequestGroups::$companyAndServiceCompanyRequest &&
                ($user->getLevel() == UserGroups::$SERVICE_COMPANY || $user->getLevel() == UserGroups::$COMPANY)) {
            return true;
        }

        if ($request->getRequestGroup() == RequestGroups::$userCompanyRequest &&
                ($user->getLevel() == UserGroups::$SERVICE_COMPANY || $user->getLevel() == UserGroups::$COMPANY || $user->getLevel() == UserGroups::$USER)) {
            return true;
        }
        return false;
    }

    private function setOnlineStatus() {
        if ($_COOKIE["ustatus"] == 1) {
            return true;
        }
        if (HTTP_ROOT_HOST === HTTP_HOST) {
            $domain = "." . HTTP_HOST;
        } else {
            $domain = HTTP_ROOT_HOST;
        }
        setcookie("ustatus", 1, null, "/", $domain);
        return false;
    }

    private function removeOnlineStatus() {

        $sessionTimeout = time() - 42000;

        if (HTTP_ROOT_HOST === HTTP_HOST) {
            $domain = "." . HTTP_HOST;
        } else {
            $domain = HTTP_ROOT_HOST;
        }
        setcookie("ustatus", 1, $sessionTimeout, "/", $domain);
    }

    private function updateUserHash($uId) {
        $userManager = UserManager::getInstance($this->config, null);
        return $userManager->updateUserHash($uId);
    }

}

?>