<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/ReferersManager.class.php");
require_once (CLASSES_PATH . "/managers/UserSubUsersManager.class.php");

class MainSiteLoad extends GuestLoad {

    /**
     *
     * @author Vahagn Sookiasian
     * test Load
     */
    public $static_body_content = false;

    public function load() {
        $this->addParam('under_construction', $this->getCmsVar('under_construction'));
        
        $winUid = uniqid();
        $this->addParam("winUid", $winUid);

        $refererUrl = $_SERVER['HTTP_REFERER'];
        if (!empty($refererUrl) && strpos($refererUrl, $_SERVER['HTTP_HOST']) === false) {
            $referersManager = ReferersManager::getInstance($this->config, $this->args);
            $referersManager->addRow($refererUrl, $_SERVER['REQUEST_URI']);
        }
        if ($_REQUEST["lang"]) {
            $lc = $_REQUEST["lang"];
            $this->setcookie('ul', $lc);
            $_COOKIE['ul'] = $lc;
        }

        if (isset($_REQUEST["activation_code"])) {
            $user_activation_code = $this->secure($_REQUEST["activation_code"]);
            $userManager = UserManager::getInstance($this->config, $this->args);
            $inactiveUser = $userManager->getUserByActivationCode($user_activation_code);
            if ($inactiveUser) {
                if ($inactiveUser->getActive() == 1) {
                    $this->addParam('user_activation', 'already activated');
                } else {
                    $inactiveUser->setActive(1);
                    $userManager->updateByPK($inactiveUser);
                    $userSubUsersManager = UserSubUsersManager::getInstance($this->config, $this->args);
                    $prentId = $userSubUsersManager->getUserParentId($inactiveUser->getId());
                    if ($prentId > 0) {
                        $invbonus = intval($this->getCmsVar("bonus_points_for_every_accepted_invitation"));
                        $userManager->addUserPoints($prentId, $invbonus, $invbonus . " bonus for invitation accept from user number: " . $inactiveUser->getId());
                    }
                    $this->addParam('user_activation', 'just activated');
                }
            }
        }

        $userLevel = $this->sessionManager->getUser()->getLevel();
        if ($userLevel === UserGroups::$GUEST) {
            if (isset($_GET["invc"])) {
                $this->setCookie('invc', $this->secure($_REQUEST["invc"]));
            } else
            if (isset($_GET["invitation_code"])) {
                //depracated should be removed
                $this->setCookie('invc', $this->secure($_REQUEST["invitation_code"]));
            }
        }
    }

    public function getDefaultLoads($args) {
        $loads = array();

        //if (isset($_REQUEST["p"])) {
        // $pageName = $_REQUEST["p"];
        // $loadName = ucfirst($pageName);
        // $loadName = preg_replace("/_(\w)/e", "strtoupper('\\1')", $loadName);
        // $loadName .= "Load";
        //}

        $dealsLoadName = "DealsLoad";
        $loads["deals"]["load"] = "loads/main/" . $dealsLoadName;
        $loads["deals"]["args"] = array("parentLoad" => &$this);
        $loads["deals"]["loads"] = array();

        $homePageLoadName = "HomePageLoad";
        $loads["content"]["load"] = "loads/main/" . $homePageLoadName;
        $loads["content"]["args"] = array("mainLoad" => &$this);
        $loads["content"]["loads"] = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/main_site.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

    public function isMain() {
        return true;
    }

    protected function logRequest() {
        return false;
    }

}

?>
