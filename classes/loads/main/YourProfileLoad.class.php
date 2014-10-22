<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class YourProfileLoad extends GuestLoad {

    public function load() {
        $userManager = UserManager::getInstance($this->config, $this->args);
        $userId = $this->getUserId();

        if (!isset($_REQUEST['refresh']) && $_REQUEST['refresh'] != 1 && $this->getCustomer()->getLoginType() === 'pcstore') {
            $custEmail = $this->getCustomerLogin();
            if (isset($_REQUEST['password'])) {
                $password = $_REQUEST['password'];
                $dto = $userManager->getUserByEmailAndPassword($custEmail, $password);
                if (!isset($dto)) {
                    $this->addParam("message", $this->getPhraseSpan(581));
                    $this->addParam("checkPassword", 1);
                    return;
                }
            } else {
                $this->addParam("checkPassword", 1);
                return;
            }
        }

        $userPhonesArray = $userManager->getUserPhonesArray($userId);
        $this->addParam("phones", $userPhonesArray);

        $regions_phrase_ids_array = explode(',', $this->getCmsVar('armenia_regions_phrase_ids'));
        $this->addParam('regions_phrase_ids_array', $regions_phrase_ids_array);
        $region = $userManager->selectByPK($userId)->getRegion();
        $this->addParam('region_selected', $region);
        $this->addParam('userManager', $userManager);
    }

    public function getDefaultLoads($args) {
        $loads = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/your_profile.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$userRequest;
    }

}

?>